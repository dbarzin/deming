<?php

namespace App\Services;

use App\Models\Risk;
use App\Models\RiskScoringConfig;

/**
 * Moteur de scoring des risques.
 *
 * Ce service centralise toute la logique de calcul afin que :
 *  - le modèle Risk reste un simple conteneur de données,
 *  - les controllers et vues n'aient aucune connaissance de la formule active,
 *  - l'ajout d'une nouvelle formule ne nécessite qu'une méthode ici + une entrée dans FORMULAS.
 *
 * Utilisation :
 *   $service = app(RiskScoringService::class);
 *   $result  = $service->score($risk);
 *   // => ['score' => 12, 'level' => 'high', 'label' => 'Élevé', 'color' => 'danger']
 *
 * Enregistrement dans AppServiceProvider :
 *   $this->app->singleton(RiskScoringService::class);
 */
class RiskScoringService
{
    // -------------------------------------------------------------------------
    // Catalogue des formules disponibles
    // -------------------------------------------------------------------------

    /**
     * Liste des formules proposées dans l'interface de configuration.
     *
     * Clé = valeur stockée en base.
     * Valeur = [label affiché, description, champs requis sur le risque]
     */
    public const FORMULAS = [
        'probability_x_impact' => [
            'label'          => 'Probabilité × Impact',
            'description'    => 'Formule classique ISO 27005 / ISO 27001. Score = P × I. Matrice 5×5 standard.',
            'requires'       => ['probability', 'impact'],
            'requires_exposure' => false,
        ],
        'likelihood_x_impact' => [
            'label'          => 'Vraisemblance × Impact (BSI 200-3)',
            'description'    => 'Méthode ISACA / BSI IT-Grundschutz. Vraisemblance = Exposition + Vulnérabilité. Score = V × I.',
            'requires'       => ['exposure', 'vulnerability', 'impact'],
            'requires_exposure' => true,
        ],
        'additive' => [
            'label'          => 'Probabilité + Impact',
            'description'    => 'Méthode additive simplifiée. Score = P + I. Appropriée pour un premier triage rapide.',
            'requires'       => ['probability', 'impact'],
            'requires_exposure' => false,
        ],
        'max_pi' => [
            'label'          => 'max(Probabilité, Impact)',
            'description'    => 'Approche conservatrice : le score est dominé par la dimension la plus défavorable.',
            'requires'       => ['probability', 'impact'],
            'requires_exposure' => false,
        ],
    ];

    // -------------------------------------------------------------------------
    // Construction
    // -------------------------------------------------------------------------

    private RiskScoringConfig $config;

    public function __construct()
    {
        $this->config = RiskScoringConfig::active();
    }

    // -------------------------------------------------------------------------
    // API principale
    // -------------------------------------------------------------------------

    /**
     * Calcule le score et le niveau de risque pour un risque donné.
     *
     * @return array{
     *   score: int,
     *   likelihood: int|null,
     *   level: string,
     *   label: string,
     *   color: string,
     *   max_score: int,
     * }
     */
    public function score(Risk $risk): array
    {
        [$score, $likelihood] = $this->calculate($risk);
        $threshold = $this->config->thresholdFor($score);

        return [
            'score'      => $score,
            'likelihood' => $likelihood,           // null si formule sans exposition
            'level'      => $threshold['level'],
            'label'      => $threshold['label'],
            'color'      => $threshold['color'],
            'max_score'  => $this->config->maxScore(),
        ];
    }

    /**
     * Expose la configuration active (pour les vues de formulaire).
     */
    public function config(): RiskScoringConfig
    {
        return $this->config;
    }

    /**
     * Expose le catalogue des formules disponibles.
     */
    public function availableFormulas(): array
    {
        return self::FORMULAS;
    }

    /**
     * Génère les données pour la matrice de risque.
     *
     * Retourne une structure [score][statut] => nombre de risques,
     * adaptée à la formule active (axes variables).
     *
     * @param  \Illuminate\Support\Collection $risks
     * @return array
     */
    public function buildMatrix(\Illuminate\Support\Collection $risks): array
    {
        $matrix = [];

        foreach ($risks as $risk) {
            $result = $this->score($risk);
            $x = $this->xAxisValue($risk);   // axe horizontal (impact)
            $y = $this->yAxisValue($risk);   // axe vertical (prob. ou vraisemblance)

            $matrix[$y][$x][] = [
                'id'     => $risk->id,
                'name'   => $risk->name,
                'score'  => $result['score'],
                'level'  => $result['level'],
                'color'  => $result['color'],
                'status' => $risk->status,
            ];
        }

        return $matrix;
    }

    /** Labels et valeurs pour l'axe X de la matrice (toujours l'impact) */
    public function matrixXAxis(): array
    {
        return $this->config->impact_levels ?? [];
    }

    /** Labels et valeurs pour l'axe Y de la matrice (prob. ou vraisemblance) */
    public function matrixYAxis(): array
    {
        if ($this->config->usesLikelihood()) {
            // Générer les valeurs de vraisemblance = toutes combinaisons exposition + vulnérabilité
            $exposures     = array_column($this->config->exposure_levels ?? [], 'value');
            $vulnerabilities = array_column($this->config->vulnerability_levels ?? [], 'value');
            $likelihoods   = [];

            foreach ($exposures as $e) {
                foreach ($vulnerabilities as $v) {
                    $likelihoods[$e + $v] = $e + $v;
                }
            }
            ksort($likelihoods);

            return array_map(
                fn($l) => ['value' => $l, 'label' => "Vraisemblance $l"],
                array_values($likelihoods)
            );
        }

        return $this->config->probability_levels ?? [];
    }

    // -------------------------------------------------------------------------
    // Calcul interne par formule
    // -------------------------------------------------------------------------

    /**
     * @return array{int, int|null}  [score, likelihood|null]
     */
    private function calculate(Risk $risk): array
    {
        return match ($this->config->formula) {
            'probability_x_impact' => $this->formulaProbabilityXImpact($risk),
            'likelihood_x_impact'  => $this->formulaLikelihoodXImpact($risk),
            'additive'             => $this->formulaAdditive($risk),
            'max_pi'               => $this->formulaMaxPI($risk),
            default                => $this->formulaProbabilityXImpact($risk),
        };
    }

    /** Score = Probabilité × Impact */
    private function formulaProbabilityXImpact(Risk $risk): array
    {
        return [$risk->probability * $risk->impact, null];
    }

    /**
     * Vraisemblance = Exposition + Vulnérabilité
     * Score = Vraisemblance × Impact
     */
    private function formulaLikelihoodXImpact(Risk $risk): array
    {
        $likelihood = ($risk->exposure ?? 0) + ($risk->vulnerability ?? 0);
        return [$likelihood * $risk->impact, $likelihood];
    }

    /** Score = Probabilité + Impact */
    private function formulaAdditive(Risk $risk): array
    {
        return [$risk->probability + $risk->impact, null];
    }

    /** Score = max(Probabilité, Impact) */
    private function formulaMaxPI(Risk $risk): array
    {
        return [max($risk->probability, $risk->impact), null];
    }

    // -------------------------------------------------------------------------
    // Axes matrice
    // -------------------------------------------------------------------------

    private function xAxisValue(Risk $risk): int
    {
        return $risk->impact ?? 1;
    }

    private function yAxisValue(Risk $risk): int
    {
        if ($this->config->usesLikelihood()) {
            return ($risk->exposure ?? 0) + ($risk->vulnerability ?? 0);
        }

        return $risk->probability ?? 1;
    }
}
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Configuration du moteur de scoring des risques.
 *
 * Une seule ligne active à la fois (is_active = true).
 * Les niveaux sont stockés en JSON pour permettre un nombre
 * variable d'échelons sans modifier le schéma.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risk_scoring_configs', function (Blueprint $table) {
            $table->id();
            $table->string('name');                    // Libellé affiché dans l'UI
            $table->string('formula');                 // Voir RiskScoringService::FORMULAS
            $table->boolean('is_active')->default(false)->index();

            // Niveaux configurables — tableaux JSON d'objets :
            // [{"value": 1, "label": "Rare", "description": "..."}]
            // Pour likelihood_x_impact : exposure + vulnerability remplacent probability.
            $table->json('probability_levels');        // Utilisé par : probability_x_impact, additive, max_pi
            $table->json('impact_levels');             // Utilisé par toutes les formules
            $table->json('exposure_levels')->nullable();    // Utilisé par : likelihood_x_impact
            $table->json('vulnerability_levels')->nullable(); // Utilisé par : likelihood_x_impact

            // Seuils de classification du score final
            // [{"level": "low", "label": "Faible", "max": 4, "color": "success"}, ...]
            // Le dernier seuil doit avoir "max": null (= pas de borne supérieure)
            $table->json('risk_thresholds');

            $table->timestamps();
        });

        // Insérer la configuration par défaut (ISO 27005 classique, alignée lb-consult)
        DB::table('risk_scoring_configs')->insert([
            'name'    => 'ISO 27005 — Probabilité × Impact (défaut)',
            'formula' => 'probability_x_impact',
            'is_active' => true,

            'probability_levels' => json_encode([
                ['value' => 1, 'label' => 'Rare',           'description' => 'Moins d\'une fois tous les 10 ans'],
                ['value' => 2, 'label' => 'Peu probable',   'description' => 'Tous les 5 à 10 ans'],
                ['value' => 3, 'label' => 'Possible',       'description' => 'Tous les 1 à 5 ans'],
                ['value' => 4, 'label' => 'Probable',       'description' => 'Plusieurs fois par an'],
                ['value' => 5, 'label' => 'Très probable',  'description' => 'Plusieurs fois par mois'],
            ]),

            'impact_levels' => json_encode([
                ['value' => 1, 'label' => 'Négligeable', 'description' => 'Aucun impact opérationnel mesurable'],
                ['value' => 2, 'label' => 'Faible',      'description' => 'Impact limité, facilement résorbé'],
                ['value' => 3, 'label' => 'Modéré',      'description' => 'Perturbation significative, récupérable'],
                ['value' => 4, 'label' => 'Élevé',       'description' => 'Impact majeur sur les opérations'],
                ['value' => 5, 'label' => 'Critique',    'description' => 'Menace existentielle pour l\'organisation'],
            ]),

            'exposure_levels'      => null,
            'vulnerability_levels' => null,

            'risk_thresholds' => json_encode([
                ['level' => 'low',      'label' => 'Faible',   'max' => 4,    'color' => 'success'],
                ['level' => 'medium',   'label' => 'Moyen',    'max' => 9,    'color' => 'warning'],
                ['level' => 'high',     'label' => 'Élevé',    'max' => 16,   'color' => 'danger'],
                ['level' => 'critical', 'label' => 'Critique', 'max' => null, 'color' => 'alert'],
            ]),

            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('risk_scoring_configs');
    }
};

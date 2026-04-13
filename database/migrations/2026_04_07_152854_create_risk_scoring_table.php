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
            'name'    => 'ISO 27005',
            'formula' => 'probability_x_impact',
            'is_active' => true,
            'probability_levels' =>  json_encode([
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.probability_levels.rare'),        'description' => ''],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.probability_levels.unlikely'),    'description' => ''],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.probability_levels.possible'),    'description' => ''],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.probability_levels.likely'),      'description' => ''],
                ['value' => 5, 'label' => __('cruds.risk_scoring.defaults.probability_levels.very_likely'), 'description' => ''],
            ]),
            'exposure_levels' => json_encode([
                ['value' => 0, 'label' => __('cruds.risk_scoring.defaults.exposure_levels.offline'),  'description' => ''],
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.exposure_levels.internal'), 'description' => ''],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.exposure_levels.internet'), 'description' => ''],
            ]),
            'vulnerability_levels' => json_encode([
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.vulnerability_levels.none'),            'description' => ''],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.vulnerability_levels.known'),           'description' => ''],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.vulnerability_levels.exploitable_int'), 'description' => ''],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.vulnerability_levels.exploitable_ext'), 'description' => ''],
            ]),
            'impact_levels' => json_encode([
                ['value' => 1, 'label' => __('cruds.risk_scoring.defaults.impact_levels.negligible'), 'description' => ''],
                ['value' => 2, 'label' => __('cruds.risk_scoring.defaults.impact_levels.low'),        'description' => ''],
                ['value' => 3, 'label' => __('cruds.risk_scoring.defaults.impact_levels.moderate'),   'description' => ''],
                ['value' => 4, 'label' => __('cruds.risk_scoring.defaults.impact_levels.high'),       'description' => ''],
                ['value' => 5, 'label' => __('cruds.risk_scoring.defaults.impact_levels.critical'),   'description' => ''],
            ]),
            'risk_thresholds' => json_encode([
                ['level' => 'low',      'label' => __('cruds.risk_scoring.defaults.risk_thresholds.low'),      'max' => 4,    'color' => '#27ae60'],
                ['level' => 'medium',   'label' => __('cruds.risk_scoring.defaults.risk_thresholds.medium'),   'max' => 9,    'color' => '#f39c12'],
                ['level' => 'high',     'label' => __('cruds.risk_scoring.defaults.risk_thresholds.high'),     'max' => 16,   'color' => '#e74c3c'],
                ['level' => 'critical', 'label' => __('cruds.risk_scoring.defaults.risk_thresholds.critical'), 'max' => null, 'color' => '#c0392b'],
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

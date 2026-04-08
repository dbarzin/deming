
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Registre des risques — ISO 27001 §6.1.2 / §8.2
 *
 * v1 : modèle centré sur le risque (lb-consult)
 * v2 prévue : colonnes exposure/vulnerability pour scoring BSI/ISACA (gorth-git)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('risks', function (Blueprint $table) {
            $table->id();

            // Identification
            $table->string('name');
            $table->text('description')->nullable();

            // Propriétaire du risque (responsable de la revue)
            $table->unsignedInteger('owner_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Évaluation — Probabilité 1 à 5
            $table->tinyInteger('probability')->default(1);
            $table->text('probability_comment')->nullable();

            // Évaluation — Impact 1 à 5
            $table->tinyInteger('impact')->default(1);
            $table->text('impact_comment')->nullable();

            // Traitement du risque
            $table->enum('status', [
                'not_evaluated',      // Non évalué
                'not_accepted',       // Non accepté → plan d'action obligatoire
                'temporarily_accepted', // Accepté temporairement
                'accepted',           // Accepté
                'mitigated',          // Mitigé → contrôles liés obligatoires
                'transferred',        // Transféré (assurance, tiers)
                'avoided',            // Évité
            ])->default('not_evaluated');
            $table->text('status_comment')->nullable();

            // Planification des revues
            $table->unsignedSmallInteger('review_frequency')->default(12); // mois
            $table->date('next_review_at')->nullable();

            // --- Champs réservés v2 (BSI 200-3 / ISACA scoring) ---
            // Exposure : 0 = offline, 1 = réseau interne, 2 = Internet
            $table->tinyInteger('exposure')->nullable()->comment('v2 - BSI 200-3');
            // Vulnerability : 1 = aucune, 2 = connue non exploitable, 3 = exploitable interne, 4 = exploitable externe
            $table->tinyInteger('vulnerability')->nullable()->comment('v2 - BSI 200-3');
            // likelihood et risk_score sont calculés (non stockés en base)

            $table->timestamps();
            $table->softDeletes();

            // Index utiles pour les filtrages fréquents
            $table->index('status');
            $table->index('owner_id');
            $table->index('next_review_at');
        });

        // Table pivot : risques ↔ mesures de contrôle
        // Obligatoire si status = 'mitigated'
        Schema::create('control_risk', function (Blueprint $table) {
            $table->unsignedInteger('risk_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('control_id')->constrained()->cascadeOnDelete();
            $table->primary(['risk_id', 'control_id']);
        });

        // Table pivot : risques ↔ plans d'action
        // Obligatoire si status = 'not_accepted'
        Schema::create('action_risk', function (Blueprint $table) {
            $table->unsignedInteger('risk_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('action_id')->constrained()->cascadeOnDelete();
            $table->primary(['risk_id', 'action_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('action_risk');
        Schema::dropIfExists('control_risk');
        Schema::dropIfExists('risks');
    }
};
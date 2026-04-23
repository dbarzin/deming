<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Statuts du workflow de validation :
     *   0 = Brouillon     (draft)
     *   1 = Soumise       (submitted – en attente de validation)
     *   2 = Approuvée     (approved)
     *   3 = Refusée       (rejected)
     *   4 = Expirée       (expired – date_fin dépassée)
     */
    public function up(): void
    {
        Schema::create('exceptions', function (Blueprint $table) {
            // ── Identifiant ──────────────────────────────────────────────────
            $table->id();

            // ── Lien au contrôle non conforme ────────────────────────────────
            $table->unsignedInteger('measure_id')->nullable();
            $table->foreign('measure_id')
                ->references('id')
                ->on('measures')
                ->nullOnDelete();

            // ── Description de l'exception ───────────────────────────────────
            $table->string('name');                      // Nom court (titre)
            $table->text('description')->nullable();     // Explication détaillée
            $table->text('justification')->nullable();   // Justification métier / raison
            $table->text('compensating_controls')->nullable(); // Mesures compensatoires éventuelles

            // ── Période de validité ──────────────────────────────────────────
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();        // Null = pas d'échéance définie

            // ── Workflow de validation ────────────────────────────────────────
            // 0=brouillon | 1=soumise | 2=approuvée | 3=refusée | 4=expirée
            $table->tinyInteger('status')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->unsignedBigInteger('submitted_by')->nullable();
            $table->foreign('submitted_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->foreign('approved_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->text('approval_comment')->nullable(); // Commentaire d'approbation / refus

            // ── Audit ────────────────────────────────────────────────────────
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exceptions');
    }
};

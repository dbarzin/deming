<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Supprime la colonne "standard" (norme) des tables controls et measures :
 * elle n'est plus utilisée par l'application.
 */
return new class extends Migration
{
    public function up(): void
    {
        foreach (['controls', 'measures'] as $tableName) {
            if (Schema::hasColumn($tableName, 'standard')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropColumn('standard');
                });
            }
        }
    }

    public function down(): void
    {
        foreach (['controls', 'measures'] as $tableName) {
            if (! Schema::hasColumn($tableName, 'standard')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->string('standard')->nullable();
                });
            }
        }
    }
};

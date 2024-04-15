<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::table('attributes', function (Blueprint $table) {
        $table->string('values',512*8)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    Schema::table('attributes', function (Blueprint $table) {
        $table->string('values',512)->nullable()->change();
        });
    }
};

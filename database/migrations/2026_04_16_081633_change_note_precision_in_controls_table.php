<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('controls', function (Blueprint $table) {
            $table->decimal('note', 9, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('controls', function (Blueprint $table) {
            $table->decimal('note', 5, 2)->nullable()->change();
        });
    }
};

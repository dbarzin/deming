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
        // Group type: 'business' or 'security' (existing groups default to 'business')
        Schema::table('user_groups', function (Blueprint $table) {
            $table->string('type', 20)->default('business');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};

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
        // Drop field owner from controls and measures
        Schema::table("controls", function(Blueprint $table) {
            $table->dropColumn("owner");
            $table->dropColumn("retention");            
        });

        Schema::table("measures", function(Blueprint $table) {
            $table->dropColumn("owner");
            $table->dropColumn("retention");            
            $table->dropColumn("periodicity");            
        });

        // Add control_user table
        Schema::create('control_user', function (Blueprint $table) {
            $table->unsignedInteger('control_id')->index('control_id_fk_5920381');
            $table->unsignedBigInteger('user_id')->index('user_id_fk_5837573');
        });

        Schema::table('control_user', function (Blueprint $table) {
            $table->foreign('control_id', 'control_id_fk_49294573')->references('id')->on('controls')->onUpdate('NO ACTION')->onDelete('CASCADE');
            $table->foreign('user_id', 'user_id_fk_304958543')->references('id')->on('users')->onUpdate('NO ACTION')->onDelete('CASCADE');
        });

        // Change unique
        try {
            Schema::table("users", function(Blueprint $table) {
                $table->string('email')->unique(true)->change();
                $table->string('title')->unique(false)->change();
            });
        } catch(Exception $e) {
            
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // restore owner field
        Schema::table("controls", function(Blueprint $table) {
            $table->string('owner')->nullable();
            $table->integer('retention')->nullable();
        });

        Schema::table("measures", function(Blueprint $table) {
            $table->string('owner')->nullable();
            $table->integer('periodicity')->nullable();
            $table->integer('retention')->nullable();
        });

        // delete link table
        Schema::dropIfExists('control_user');

        // No rollback for unique
    }
};

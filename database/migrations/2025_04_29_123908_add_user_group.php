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

        Schema::create('user_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('user_user_group', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('user_group_id');
            $table->foreign('user_group_id')->references('id')->on('user_groups');
        });

        Schema::create('control_user_group', function (Blueprint $table) {
            $table->unsignedInteger('control_id');
            $table->foreign('control_id')->references('id')->on('controls');
            $table->unsignedInteger('user_group_id');
            $table->foreign('user_group_id')->references('id')->on('user_groups');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_user_group');
        Schema::dropIfExists('control_user_group');
        Schema::dropIfExists('user_groups');
    }
};

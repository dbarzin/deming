<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('audit_logs'))
            Schema::create('audit_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->text('description');
                $table->unsignedInteger('subject_id')->nullable();
                $table->string('subject_type')->nullable();
                $table->unsignedInteger('user_id')->nullable();
                $table->text('properties')->nullable();
                $table->string('host', 45)->nullable();
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_logs');
    }

};

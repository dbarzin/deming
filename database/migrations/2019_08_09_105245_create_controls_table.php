<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controls', function (Blueprint $table) {
            $table->increments('id');
            // from control table
            $table->integer('domain_id')->unsigned();
            $table->foreign('domain_id')->references('id')->on('domains');
            $table->integer('measure_id')->unsigned();
            $table->foreign('measure_id')->references('id')->on('measures');
            $table->string('name');
            $table->text('clause')->nullable();
            $table->text('objective')->nullable();
            $table->text('attributes')->nullable();
            $table->text('model')->nullable();
            $table->text('indicator')->nullable();
            $table->longText('action_plan')->nullable();
            $table->string('owner')->nullable();
            $table->integer('periodicity')->nullable();
            $table->integer('retention')->nullable();

            // measurement
            $table->date('plan_date');
            $table->date('realisation_date')->nullable();
            $table->longText('observations')->nullable();
            $table->string('score')->nullable();
            $table->integer('note')->nullable();

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
        Schema::dropIfExists('controls');
    }
}

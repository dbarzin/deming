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
            $table->integer('domain_id')->unsigned();
            $table->foreign('domain_id')->references('id')->on('domains');
            $table->string('name');
            $table->text('clause')->nullable();
            $table->text('objective')->nullable();
            $table->text('attributes')->nullable();
            $table->text('model')->nullable();
            $table->text('indicator')->nullable();
            $table->text('action_plan')->nullable();
            $table->string('owner')->nullable();
            $table->integer('periodicity')->nullable();
            $table->integer('retention')->nullable();
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

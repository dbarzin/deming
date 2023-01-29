<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->unique();
            $table->string('values')->nullable();
            $table->timestamps();
        });

        Schema::table('controls', function (Blueprint $table) {
            $table->string('standard')->nullable();
            $table->string('tags')->nullable();
        });

        Schema::table('measures', function (Blueprint $table) {
            $table->string('standard')->nullable();
            $table->string('tags')->nullable();
            $table->string('site')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');

        Schema::table('controls', function (Blueprint $table) {
            $table->dropColumn('standard');
            $table->dropColumn('tags');
        });

        Schema::table('measures', function (Blueprint $table) {
            $table->dropColumn('standard');
            $table->dropColumn('tags');
            $table->dropColumn('site');
        });
    }
}

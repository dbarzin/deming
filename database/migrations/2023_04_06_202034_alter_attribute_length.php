<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterAttributeLength extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    Schema::table('measures', function (Blueprint $table) {
        $table->string('attributes',1024)->nullable()->change();
        });
    Schema::table('controls', function (Blueprint $table) {
        $table->string('attributes',1024)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    Schema::table('measures', function (Blueprint $table) {
        $table->string('values',255)->nullable()->change();
        });
    Schema::table('controls', function (Blueprint $table) {
        $table->string('values',255)->nullable()->change();
        });
    }
}

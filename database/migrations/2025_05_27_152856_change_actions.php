<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Make the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->integer('type')->change();
            $table->integer('progress')->nullable()->after('type');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->integer('action_id')->nullable()->unsigned();
            $table->foreign('action_id')->references('id')->on('actions');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('actions', function (Blueprint $table) {
            $table->string('type', 32)->change();
            $table->dropColumn('progress');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['action_id']);
            $table->dropColumn('action_id');
        });
    }
};

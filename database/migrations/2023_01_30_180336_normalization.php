<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Normalization extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename("tags", "attributes");

        Schema::table("controls", function(Blueprint $table) {
            $table->renameColumn("attributes", "input");
            $table->renameColumn("tags", "attributes");
            $table->string("site")->nullable();
        });

        Schema::table("measures", function(Blueprint $table) {
            $table->renameColumn("attributes", "input");
            $table->renameColumn("tags", "attributes");
            $table->dropColumn("site");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename("attributes", "tags");

        Schema::table("controls", function(Blueprint $table) {
            $table->renameColumn("attributes", "tags");
            $table->renameColumn("input", "attributes");
            $table->dropColumn("site");
        });

        Schema::table("measures", function(Blueprint $table) {
            $table->renameColumn("attributes", "tags");
            $table->renameColumn("input", "attributes");
            $table->string("site")->nullable();
        });
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ControlScoreToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE "controls"  
                ALTER COLUMN "score" TYPE integer USING (score)::integer, 
                ALTER COLUMN "score" DROP NOT NULL, 
                ALTER COLUMN "score" DROP DEFAULT,  
                ALTER COLUMN "score" DROP identity IF EXISTS);');
        } else {
            Schema::table('controls', function (Blueprint $table) {
                $table->integer('score')->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('controls', function (Blueprint $table) {
            $table->string('score')->nullable()->change();
        });
    }
}

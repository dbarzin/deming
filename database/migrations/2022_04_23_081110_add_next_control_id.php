<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Control;

class AddNextControlId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controls', function (Blueprint $table) {
            $table->unsignedInteger('next_id')->nullable();
            $table->foreign('next_id','fk_controls_next_id')->references('id')->on('controls');
        });

        // Update link to next control
        $controls=Control::All();
        foreach($controls as $control) {
            $control->next_id=DB::select(DB::raw(
                    "select min(id) as next_id from controls " .
                    "where id > " . $control->id .
                    " and measure_id = " . $control->measure_id))[0]->next_id;
            $control->update();
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
            $table->dropForeign('fk_controls_next_id');
            $table->dropColumn('next_id');
        });
    }
}

<?php
use App\Models\Control;

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
        //
        Schema::create('control_measure', function (Blueprint $table) {
            $table->integer('control_id')->unsigned();
//            $table->foreign('control_id')->references('id')->on('controls');
            $table->integer('measure_id')->unsigned();
//            $table->foreign('measure_id')->references('id')->on('measures');
        });

        // Fill table
        foreach(Control::All() as $control) {
            $control->measures()->sync([$control->measure_id]);
        }

        Schema::table('controls', function (Blueprint $table) {
//                $table->dropForeign(['measure_id']);
                $table->dropForeign(['domain_id']);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('control_measure', function (Blueprint $table) {
            $table->dropForeign('domain_id');
            $table->dropForeign('measure_id');
            });

        Schema::dropIfExists('control_measure');

        Schema::table('controls', function (Blueprint $table) {
            $table->foreign('domain_id')->references('id')->on('domains');
            $table->foreign('measure_id')->references('id')->on('measures');
            });
    }
};

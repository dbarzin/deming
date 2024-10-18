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
        Schema::create('control_measure', function (Blueprint $table) {
            $table->integer('control_id')->unsigned();
            $table->foreign('control_id')->references('id')->on('controls');
            $table->integer('measure_id')->unsigned();
            $table->foreign('measure_id')->references('id')->on('measures');
        });

        // Fill table
        foreach(Control::All() as $control) {
            if (($control->measure_id !== null)&&($control->measure_id !== 0))
                $control->measures()->sync([$control->measure_id]);
        }

        if (DB::getDriverName() !== 'pgsql')
            Schema::table('controls', function (Blueprint $table) {
                $table->dropForeign(['controls_domain_id_foreign']);
                $table->dropForeign(['controls_measure_id_foreign']);
            });

        if (DB::getDriverName() === 'sqlite')
            // Could not drop column with sqlite
            Schema::table('controls', function (Blueprint $table) {
                $table->integer('domain_id')->nullable()->change();
                $table->integer('measure_id')->nullable()->change();
                });
        else
            // Drop columns
            Schema::table('controls', function (Blueprint $table) {
                $table->dropColumn(['domain_id']);
                $table->dropColumn(['measure_id']);
                });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('control_measure');

        Schema::table('controls', function (Blueprint $table) {
            $table->integer('domain_id')->unsigned();
            $table->integer('measure_id')->unsigned();
            // Wokrs only if DB is empty
            // $table->foreign('domain_id')->references('id')->on('domains');
            // $table->foreign('measure_id')->references('id')->on('measures');
            });
    }
};

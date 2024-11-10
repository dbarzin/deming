<?php

use App\Models\Action;
use App\Models\Control;
use App\Models\User;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\Log;

return new class extends Migration
{

    /**
     * Run the migrations.
     */
    public function up(): void
    {

        // Add actions tables
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('reference', 32)->nullable();
            $table->string('type', 32)->nullable();
            $table->integer('criticity')->dafault(0);
            $table->integer('status')->dafault(0);
            $table->string('scope', 32)->nullable();
            $table->string('name')->nullable();
            $table->text('cause')->nullable();
            $table->text('remediation')->nullable();
            $table->integer('control_id')->unsigned()->nullable();
            $table->foreign('control_id')->references('id')->on('controls');
            $table->date('creation_date')->nullable();
            $table->date('due_date')->nullable();
            $table->date('close_date')->nullable();
            $table->text('justification')->nullable();

            $table->timestamps();
        });

        // Link between actions and users
        Schema::create('action_user', function (Blueprint $table) {
            $table->integer('action_id')->unsigned();
            $table->foreign('action_id')->references('id')->on('actions');
            $table->biginteger('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

        // Link between actions and measure
        Schema::create('action_measure', function (Blueprint $table) {
            $table->integer('action_id')->unsigned();
            $table->foreign('action_id')->references('id')->on('actions');
            $table->integer('measure_id')->unsigned();
            $table->foreign('measure_id')->references('id')->on('measures');
        });

        // Move data from Controls to Actions
        $controls = DB::table('controls as c1')
            ->select(
                [
                    'c1.id',
                    'c1.name',
                    'c1.action_plan',
                    'c1.score',
                    'c1.scope',
                    'c1.observations',
                    'c1.realisation_date',
                    'c2.plan_date'
                ])
            ->leftjoin('controls as c2', 'c1.next_id', '=', 'c2.id')
            ->where(function ($query) {
                $query->where('c1.score', '=', 1)
                    ->orWhere('c1.score', '=', 2);
            })
            ->whereIn('c2.status', [0,1])
            ->get();

        foreach($controls as $control) {

            // Create Action
            $action = new Action();

            // Fill fields
            $action->control_id = $control->id;
            $action->name = $control->name;
            $action->criticity = $control->score;
            $action->scope = $control->scope;
            $action->cause = $control->observations;
            $action->remediation = $control->action_plan;
            $action->creation_date = $control->realisation_date;
            $action->due_date = $control->plan_date;

            // Save it
            $action->save();

            // Sync onwers
            $owners = DB::table('control_user')
                ->select('user_id')
                ->where('control_id',$control->id)
                ->pluck('user_id')->toArray();

            $action->owners()->sync($owners);

            // Sync measures
            $measures = DB::table('control_measure')
                ->select('measure_id')
                ->where('control_id',$control->id)
                ->pluck('measure_id')->toArray();

            $action->measures()->sync($measures);
        }

        // remove fields from controls
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_user');
        Schema::dropIfExists('action_measure');

        Schema::dropIfExists('actions');
    }
};

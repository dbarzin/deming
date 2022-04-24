<?php

namespace App\Console\Commands;
 
use Illuminate\Console\Command;

use App\Measure;
use App\Control;

use \Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deming:generateTests';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup all database and generate test data';
 
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {        
        // remove all measurements
        $this->info("Remove all controls and documents");
        DB::table('documents')->delete();
        DB::table('controls')->delete();

        // period in month
        $period = 12;

        // Start date
        $curDate=Carbon::now()->addMonth(-$period)->day(1);
        // Log::Alert("startDate=" . $curDate->toDateString());

        // get all controls
        $measures = Measure::All();
        $cntMeasure = DB::table('measures')->count();
        // Log::Alert("controld count=" . $cntMeasure);

        // controls per period
        $perPeriod = (int)($cntMeasure / $period);

        // Log::Alert("control per period=" . $perPeriod);

        // loop on measures
        $curControl = 0; 
        $delta = rand(0,5);
        $this->info("Lopp on measures");
        foreach ($measures as $measure) {
            $this->info($measure->clause);
            // go to next period
            if (( ($curControl++) % ($perPeriod+$delta))==0) {
                $curDate->addMonth(1);
                $delta = rand(0,5);
            }

            // Log::Alert("Control " . $control->clause . " curDate=" . $curDate->toDateString());

            // create a control
            $control = new Control();
            $control->measure_id=$measure->id;
            $control->domain_id=$measure->domain_id;
            $control->name=$measure->name;
            $control->clause=$measure->clause;
            $control->objective = $measure->objective;
            $control->attributes = $measure->attributes;
            $control->model = $measure->model;
            $control->indicator = $measure->indicator;
            $control->action_plan = $measure->action_plan;
            $control->owner = $measure->owner;
            $control->periodicity = $measure->periodicity;
            $control->retention = $measure->retention;
            // do it
            $control->plan_date = (new Carbon($curDate))->day(rand(0, 28))->toDateString();
            $control->realisation_date = (new Carbon($curDate))->addDay(rand(0, 28))->toDateString();
            $control->note = rand(0, 10);
            $control->score = rand(0, 100)<90 ? 3 : (rand(0, 2)<2 ? 2 : 1);
            $control->save();
 
            // create next control
            $nextControl = new Control();
            $nextControl->measure_id=$measure->id;
            $nextControl->domain_id=$measure->domain_id;
            $nextControl->name=$measure->name;
            $nextControl->clause=$measure->clause;
            $nextControl->objective = $measure->objective;
            $nextControl->attributes = $measure->attributes;
            $nextControl->model = $measure->model;
            $nextControl->indicator = $measure->indicator;
            $nextControl->action_plan = $measure->action_plan;
            $nextControl->owner = $measure->owner;
            $nextControl->periodicity = $measure->periodicity;
            $nextControl->retention = $measure->retention;
            // next one            
            $nextControl->plan_date = (new Carbon($curDate))->day(rand(0, 28))->addMonth($measure->periodicity)->toDateString();
            // fix it
            $nextControl->realisation_date=null;
            $nextControl->note=null;
            $nextControl->score=null;
            // save it
            $nextControl->save();

            // link them
            $control->next_id=$nextControl->id;
            $control->update();
       }
        $this->info("Done.");
    }

}

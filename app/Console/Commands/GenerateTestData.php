<?php

namespace App\Console\Commands;

use App\Models\Control;
use App\Models\Measure;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

use Faker;

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
    protected $description = 'Generate test data';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->components->info('Generate test data');

        // Remove data in documents and controls tables
        DB::table('documents')->delete();
        DB::table('controls')->update(['next_id' => null]);
        DB::table('controls')->delete();

        // Get all attributes
        $attributes = [];
        $attributesDB = DB::table('attributes')
            ->select('values')
            ->get();
        foreach ($attributesDB as $attribute) {
            foreach (explode(' ', $attribute->values) as $value) {
                if (strlen($value) > 0) {
                    array_push($attributes, $value);
                }
            }
        }
        sort($attributes);

        // period in month
        $period = 12;

        // Start date
        $curDate = Carbon::now()->addMonth(-$period)->day(1);

        // get all controls
        $measures = Measure::All();
        $cntMeasure = DB::table('measures')->count();
        // Log::Alert("controld count=" . $cntMeasure);

        // controls per period
        $perPeriod = (int) ($cntMeasure / $period);

        // loop on measures
        $delta = $perPeriod - rand(-$perPeriod / 2, $perPeriod / 2);


        $faker = Faker\Factory::create();

        foreach ($measures as $measure) {
            $delta--;
            if ($delta <= 0) {
                // go to next period
                $curDate->addMonth(1);
                $delta = $perPeriod - rand(-$perPeriod / 3, $perPeriod / 3);
            }

            // create a control
            $control = new Control();
            $control->measure_id = $measure->id;
            $control->domain_id = $measure->domain_id;
            $control->name = $measure->name;
            $control->clause = $measure->clause;
            $control->objective = $measure->objective;
            $control->attributes = $measure->attributes;
            $control->model = $measure->model;
            $control->input = $measure->input;
            $control->indicator = $measure->indicator;
            $control->action_plan = $measure->action_plan;
            $control->periodicity = 12;
            $control->attributes = $measure->attributes;
            // do it
            $control->plan_date = (new Carbon($curDate))->day(rand(0, 28))->toDateString();
            $control->realisation_date = (new Carbon($curDate))->addDay(rand(0, 28))->toDateString();
            $control->observations = $faker->text(256);
            $control->note = rand(0, 10);
            $control->score = rand(0, 100) < 90 ? 3 : (rand(0, 2) < 2 ? 2 : 1);
            $control->save();

            // create a previous
            $prev_control = new Control();
            $prev_control->measure_id = $measure->id;
            $prev_control->domain_id = $measure->domain_id;
            $prev_control->name = $measure->name;
            $prev_control->clause = $measure->clause;
            $prev_control->objective = $measure->objective;
            $prev_control->attributes = $measure->attributes;
            $prev_control->input = $measure->input;
            $prev_control->model = $measure->model;
            $prev_control->indicator = $measure->indicator;
            $prev_control->action_plan = $measure->action_plan;
            $prev_control->periodicity = 12;
            $prev_control->attributes = $measure->attributes;
            // do it
            $prev_control->plan_date = (new Carbon($curDate))->addMonth(-$measure->periodicity)->day(rand(0, 28))->toDateString();
            $prev_control->realisation_date = (new Carbon($curDate))->addMonth(-$measure->periodicity)->addDay(rand(0, 28))->toDateString();
            $prev_control->observations = $faker->text(256);
            $prev_control->note = rand(0, 10);
            $prev_control->score = rand(0, 100) < 90 ? 3 : (rand(0, 2) < 2 ? 2 : 1);
            $prev_control->next_id = $control->id;
            $prev_control->save();

            // create next control
            $nextControl = new Control();
            $nextControl->measure_id = $measure->id;
            $nextControl->domain_id = $measure->domain_id;
            $nextControl->name = $measure->name;
            $nextControl->clause = $measure->clause;
            $nextControl->objective = $measure->objective;
            $nextControl->attributes = $measure->attributes;
            $nextControl->input = $measure->input;
            $nextControl->model = $measure->model;
            $nextControl->indicator = $measure->indicator;
            $nextControl->action_plan = $measure->action_plan;
            $nextControl->periodicity = 12;
            $nextControl->attributes = $control->attributes;
            // next one
            $nextControl->plan_date = (new Carbon($curDate))->day(rand(0, 28))->addMonth(12)->toDateString();
            // fix it
            $nextControl->realisation_date = null;
            $nextControl->note = null;
            $nextControl->score = null;
            // save it
            $nextControl->save();

            // link them
            $control->next_id = $nextControl->id;
            $control->update();
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Control;
use App\Models\Measure;
use Carbon\Carbon;
use Faker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateTestData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deming:generate-tests';

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
        DB::table('action_measure')->delete();
        DB::table('action_user')->delete();
        DB::table('actions')->delete();
        DB::table('controls')->update(['next_id' => null]);
        DB::table('control_user_group')->delete();
        DB::table('control_measure')->delete();
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
        $curDate = Carbon::now()->addMonths(-$period)->day(1);

        // get all controls
        $measures = Measure::All();
        $cntMeasure = DB::table('measures')->count();
        // Log::Alert("controld count=" . $cntMeasure);

        // controls per period
        $perPeriod = (int) ($cntMeasure / $period);

        // loop on measures
        $delta = $perPeriod - rand(-$perPeriod / 2, $perPeriod / 2);

        // get language for the faker
        $lang = getenv('LANG');
        if (strtolower($lang) === 'fr') {
            $locale = 'fr_FR';
        } else {
            $locale = 'en_US';
        }

        // Intialize faker
        $faker = Faker\Factory::create($locale);

        // Loop on measures
        foreach ($measures as $measure) {
            $delta--;
            if ($delta <= 0) {
                // go to next period
                $curDate->addMonth();
                $delta = $perPeriod - rand(-$perPeriod / 3, $perPeriod / 3);
            }

            // create a control
            $control = new Control();
            // $control->domain_id = $measure->domain_id;
            // $control->clause = $measure->clause;
            $control->name = $measure->name;
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
            $control->realisation_date = (new Carbon($curDate))->addDays(rand(0, 28))->toDateString();
            $control->observations = $faker->text(256);
            $control->note = rand(0, 10);
            $control->score = rand(0, 100) < 90 ? 3 : (rand(0, 2) < 2 ? 2 : 1);
            $control->status = 2;
            $control->save();
            $control->measures()->sync([$measure->id]);

            // create a previous
            $prev_control = new Control();
            // $prev_control->domain_id = $measure->domain_id;
            // $prev_control->clause = $measure->clause;
            $prev_control->name = $measure->name;
            $prev_control->objective = $measure->objective;
            $prev_control->attributes = $measure->attributes;
            $prev_control->input = $measure->input;
            $prev_control->model = $measure->model;
            $prev_control->indicator = $measure->indicator;
            $prev_control->action_plan = $measure->action_plan;
            $prev_control->periodicity = 12;
            $prev_control->attributes = $measure->attributes;
            // do it
            $prev_control->plan_date = (new Carbon($curDate))->addMonths(-$measure->periodicity)->day(rand(0, 28))->toDateString();
            $prev_control->realisation_date = (new Carbon($curDate))->addMonths(-$measure->periodicity)->addDays(rand(0, 28))->toDateString();
            $prev_control->observations = $faker->text(256);
            $prev_control->note = rand(0, 10);
            $prev_control->score = rand(0, 100) < 90 ? 3 : (rand(0, 2) < 2 ? 2 : 1);
            $prev_control->next_id = $control->id;
            $prev_control->status = 2;
            $prev_control->save();
            $prev_control->measures()->sync([$measure->id]);

            // create next control
            $nextControl = new Control();
            // $nextControl->measure_id = $measure->id;
            // $nextControl->domain_id = $measure->domain_id;
            // $nextControl->clause = $measure->clause;
            $nextControl->name = $measure->name;
            $nextControl->objective = $measure->objective;
            $nextControl->attributes = $measure->attributes;
            $nextControl->input = $measure->input;
            $nextControl->model = $measure->model;
            $nextControl->indicator = $measure->indicator;
            $nextControl->action_plan = $measure->action_plan;
            $nextControl->periodicity = 12;
            $nextControl->attributes = $control->attributes;
            // next one
            $nextControl->plan_date = (new Carbon($curDate))->day(rand(0, 28))->addMonths(12)->toDateString();
            // fix it
            $nextControl->realisation_date = null;
            $nextControl->note = null;
            $nextControl->score = null;
            $nextControl->status = 0;
            // save it
            $nextControl->save();
            $nextControl->measures()->sync([$measure->id]);

            // link them
            $control->next_id = $nextControl->id;
            $control->update();
        }
    }
}

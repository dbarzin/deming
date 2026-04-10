<?php

namespace Database\Seeders;

use App\Models\Measure;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MeasureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('control_measure')->delete();
            DB::table('controls')->delete();
            DB::table('measures')->delete();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $lang = getenv('LANG') ?: config('app.locale', 'en');
        $lang = strtolower(substr($lang, 0, 2));

        $filename = $lang === 'fr'
            ? 'database/data/measures.fr.csv'
            : 'database/data/measures.en.csv';

        $csvFile = fopen(base_path($filename), 'r');
        if ($csvFile === false) {
            throw new \RuntimeException("Cannot open seed file: {$filename}");
        }

        $firstline = true;
        try {
            while (($data = fgetcsv($csvFile, 8000, ',')) !== false) {
                if (!$firstline) {
                    Measure::create([
                        'domain_id'   => $data[0],
                        'clause'      => $data[1],
                        'name'        => $data[2],
                        'objective'   => str_replace('\\n', "\n", $data[3]),
                        'attributes'  => str_replace('\\n', "\n", $data[4]),
                        'input'       => str_replace('\\n', "\n", $data[5]),
                        'model'       => str_replace('\\n', "\n", $data[6]),
                        'indicator'   => str_replace('\\n', "\n", $data[7]),
                        'action_plan' => str_replace('\\n', "\n", $data[8]),
                        'created_at'  => now(),
                    ]);
                }
                $firstline = false;
            }
        } finally {
            fclose($csvFile);
        }
    }
}

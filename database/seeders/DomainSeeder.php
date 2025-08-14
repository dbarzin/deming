<?php

namespace Database\Seeders;

use App\Models\Domain;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DomainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('domains')->delete();

        // get language
        $lang = getenv('LANG') ?: config('app.locale', 'en');
        $lang = strtolower(substr($lang, 0, 2));

        // get filename
        if ($lang === 'fr')
            $filename="database/data/domains.fr.csv";
        else
            $filename="database/data/domains.en.csv";

        // Open CSV file
        $csvFile = fopen(base_path($filename), "r");

        // Loop on each line
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                DB::table('domains')->insert([
                    'id' => (int) $data[0],
                    'title' => $data[1],
                    'description' => $data[2],
                    'created_at' => now(),
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}

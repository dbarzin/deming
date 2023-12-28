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
        $lang = env('LANG', 1);

        // get filename
        if (strtolower($lang)==="fr")
            $filename="database/data/domains.fr.csv";
        else
            $filename="database/data/domains.en.csv";
        // Open CSV file
        $csvFile = fopen(base_path($filename), "r");

        // Loop on each line  
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Domain::create([
                    "id" => $data['0'],
                    "title" => $data['1'],
                    "description" => $data['2'],
                    "created_at" => now()
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}

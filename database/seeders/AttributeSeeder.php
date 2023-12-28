<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attribute;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // clear DB table
        Attribute::truncate();

        // get language
        $lang = env('LANG', 1);

        // get filename
        if (strtolower($lang)==="fr")
            $filename="database/data/attributes.fr.csv";
        else
            $filename="database/data/attributes.en.csv";

        // Open CSV file
        $csvFile = fopen(base_path($filename), "r");

        // Loop on each line
        $firstline = true;
        while (($data = fgetcsv($csvFile, 2000, ",")) !== FALSE) {
            if (!$firstline) {
                Attribute::create([
                    "name" => $data['0'],
                    "values" => $data['1'],
                    "created_at" => now()
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}

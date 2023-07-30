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
        DB::table('measures')->delete();

        $csvFile = fopen(base_path("database/data/measures.fr.csv"), "r");
  
        $firstline = true;
        while (($data = fgetcsv($csvFile, 8000, ",")) !== FALSE) {
            \Log::Debug($data);
            if (!$firstline) {
                Measure::create([
                    "domain_id" => $data['0'],
                    "clause" => $data['1'],
                    "name" => $data['2'],
                    "objective" => $data['3'],
                    "attributes" => $data['4'],
                    "input" => $data['5'],
                    "model" => $data['6'],
                    "indicator" => $data['7'],
                    "action_plan" => $data['8'],
                    "created_at" => now()
                ]);
            }
            $firstline = false;
        }
        fclose($csvFile);
    }
}

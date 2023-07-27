<?php
  
namespace Database\Seeders;
  
use App\Domain;

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

        $csvFile = fopen(base_path("database/data/domains.fr.csv"), "r");
  
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

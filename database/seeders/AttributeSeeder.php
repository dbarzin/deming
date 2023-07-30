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
        Attribute::truncate();
  
        $csvFile = fopen(base_path("database/data/attributes.fr.csv"), "r");
  
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

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
    public function run() : void
    {
        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            DB::table('domains')->delete();
        } finally {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $lang = getenv('LANG') ?: config('app.locale', 'en');
        $lang = strtolower(substr($lang, 0, 2));

        $filename = $lang === 'fr'
            ? 'database/data/domains.fr.csv'
            : 'database/data/domains.en.csv';

        $csvFile = fopen(base_path($filename), 'r');
        if ($csvFile === false) {
            throw new \RuntimeException("Cannot open seed file: {$filename}");
        }

        $firstline = true;
        try {
            while (($data = fgetcsv($csvFile, 2000, ',')) !== false) {
                if (!$firstline) {
                    DB::table('domains')->insert([
                        'id'          => (int) $data[0],
                        'title'       => $data[1],
                        'framework'   => $data[2] ?? null,  // à ajuster selon structure CSV
                        'description' => $data[3] ?? $data[2],
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

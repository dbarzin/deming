<?php

use Illuminate\Database\Seeder;

use Database\Seeders\UserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'login' => 'admin',
            'name' => 'Jacks Barrow',
            'title' => 'Pirate Captain',
            'role' => 1,
            'language' => 'en',
            'email' => 'admin@admin.localhost',
            'password' => bcrypt('admin'),
        ]);
    }
}

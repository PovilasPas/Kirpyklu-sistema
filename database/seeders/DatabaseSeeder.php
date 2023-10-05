<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MainSeeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            MainSeeder::class,
        ]);
        DB::table('statuses')->insert([
            'name' => 'manager'
        ]);
        DB::table('statuses')->insert([
            'name' => 'hairdresser'
        ]);
    }
}

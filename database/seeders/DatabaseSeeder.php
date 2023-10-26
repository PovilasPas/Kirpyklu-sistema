<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\MainSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            'name' => 'manager'
        ]);
        DB::table('statuses')->insert([
            'name' => 'hairdresser'
        ]);
        $this->call([
             MainSeeder::class
        ]);
        // DB::table('users')->insert([
        //     'name' => 'kirpėjas1',
        //     'surname' => 'kirpėjas1',
        //     'email' => 'KerpuPlaukus@gmail.com',
        //     'password' => Hash::make('kirpėjas_1'),
        //     'status_id' => 2,
        // ]);
        // DB::table('users')->insert([
        //     'name' => 'kirpėjas2',
        //     'surname' => 'kirpėjas2',
        //     'email' => 'NekerpuPlaukus@gmail.com',
        //     'password' => Hash::make('kirpėjas_2'),
        //     'status_id' => 2,
        // ]);
        // DB::table('users')->insert([
        //     'name' => 'vadovas1',
        //     'surname' => 'vadovas1',
        //     'email' => 'TuriuKirpykla@gmail.com',
        //     'password' => Hash::make('vadovas_1'),
        //     'status_id' => 1,
        // ]);
        // DB::table('users')->insert([
        //     'name' => 'vadovas2',
        //     'surname' => 'vadovas2',
        //     'email' => 'Kirpyklos@gmail.com',
        //     'password' => Hash::make('vadovas_2'),
        //     'status_id' => 1,
        // ]);
    }
}

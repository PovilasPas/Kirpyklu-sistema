<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\HairSalon;
use App\Models\Hairstyle;
use App\Models\Hairdresser;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        City::factory(10)->create()->each(function($city){
            HairSalon::factory(rand(0,2))->create(['city_id' => $city->id])->each(function($HairSalon){
                Hairdresser::factory(rand(1,4))->create(['hair_salon_id' => $HairSalon->id])->each(function($Hairdresser){
                    Hairstyle::factory(rand(1,3))->create(['hairdresser_id' => $Hairdresser->id]);
                });
            });
        });
    }
}

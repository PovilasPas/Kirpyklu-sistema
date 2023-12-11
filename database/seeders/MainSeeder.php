<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\User;
use App\Models\HairSalon;
use App\Models\Hairstyle;
use App\Models\Hairdresser;
use Illuminate\Support\Arr;
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
        $cities = City::factory()->count(10)->create();
        $ids = Arr::pluck($cities, 'id');

        User::factory(['status_id' => 1])->count(5)->create()->each(function($user) use($ids) {
            $count = rand(1,3);
            for($i = 0; $i < $count; $i++)
            {
                $salon = HairSalon::factory(['city_id' => $ids[rand(0,count($ids) - 1)], 'manager_id' => $user->id])->create();
                Hairdresser::factory(['hair_salon_id' => $salon->id])->count(rand(1,3))->create()->each(function($hairdresser) {
                    if($hairdresser->is_approved) Hairstyle::factory(['hairdresser_id' => $hairdresser->id])->count(rand(1,3))->create();
                });
            }
        });
    }
}

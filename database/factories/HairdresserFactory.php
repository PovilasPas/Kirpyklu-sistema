<?php

namespace Database\Factories;

use App\Models\HairSalon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hairdresser>
 */
class HairdresserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date = $this->faker->dateTimeThisDecade();
        $factory = \Faker\Factory::create('lt_LT');
        return [
            'phone_nr' => str_replace([" ","(",")"],["","",""], $factory->phoneNumber()),
            'is_approved' => $this->faker->randomElement([0, 1]),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}

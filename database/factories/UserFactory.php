<?php

namespace Database\Factories;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
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
        $gender = rand(0,1);
        return [
            'name' => $gender == 0 ? $factory->firstNameMale() : $factory->firstNameFemale(),
            'surname' => $gender == 0 ? $factory->lastNameMale() : $factory->lastNameFemale(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}

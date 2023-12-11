<?php

namespace Database\Factories;

use App\Models\Hairdresser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Hairstyle>
 */
class HairstyleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $date = $this->faker->dateTimeThisDecade();
        $name = $this->faker->randomElement([
            'Fade', 
            'Taper', 
            'Buzz Cut', 
            'Crew Cut', 
            'Long Top Short Sides',
            'French Crop',
            'Undercut',
            'Flow',
            'Side Part',
            'Slicked Back Hair',
            'Military',
            'Caesar',
            'Mohawk',
            'Blowout',
            'Bowl Cut',
            'Mullet',
            'Comb Over',
            'Curtain Bangs',
            'Flat Top',
        ]);
        return [
            'name' => $name,
            'price' => $this->faker->randomFloat(2,0.01,999.99),
            'image' => $this->faker->imageUrl($width = 640, $height = 480),
            'estimated_time_min' => $this->faker->numberBetween(1,120),
            'created_at' => $date,
            'updated_at' => $date
        ];
    }
}

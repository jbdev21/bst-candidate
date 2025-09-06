<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SpotPrice>
 */
class SpotPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'metal' => fake()->randomElement(['GOLD', 'SILVER']),
            'price_per_oz_cents' => fake()->randomNumber(),
            'as_of' => fake()->dateTimeBetween('-5 days', '+10 days'),
        ];
    }
}

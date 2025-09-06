<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => fake()->word(),
            'name' => fake()->name(),
            'metal' => fake()->randomElement(['GOLD', 'SILVER']),
            'weight_oz' => fake()->randomNumber(),
            'premium_cents' => fake()->randomNumber(),
            'active' => fake()->randomElement([true, false]),
        ];
    }
}

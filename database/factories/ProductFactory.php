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
            'weight_oz' => fake()->randomFloat(3, 0.1, 100), // 0.1 to 100 oz with 3 decimal places
            'premium_cents' => fake()->numberBetween(500, 50000), // $5 to $500 premium in cents
            'active' => true,
        ];
    }
}

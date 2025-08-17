<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => fake()->unique()->regexify('[A-Z]{3}[0-9]{4}'),
            'description' => fake()->optional()->sentence(),
            'price' => fake()->randomFloat(2, 10, 1000),
            'low_stock_threshold' => fake()->numberBetween(5, 50),
        ];
    }
}

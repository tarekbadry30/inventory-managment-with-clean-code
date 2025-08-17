<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockTransfer>
 */
class StockTransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'from_warehouse_id' => \App\Models\Warehouse::factory(),
            'to_warehouse_id' => \App\Models\Warehouse::factory(),
            'inventory_item_id' => \App\Models\InventoryItem::factory(),
            'quantity' => fake()->numberBetween(1, 100),
            'notes' => fake()->optional()->sentence(),
            'status' => fake()->randomElement(['pending', 'completed', 'cancelled']),
            'created_by' => \App\Models\User::factory(),
            'transferred_at' => fake()->optional()->dateTimeBetween('-1 month', 'now'),
        ];
    }
}

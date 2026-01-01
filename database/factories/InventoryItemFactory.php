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
            'name' => fake()->words(2, true),
            'sku' => strtoupper(fake()->bothify('???-####')),
            'description' => fake()->sentence(),
            'category' => fake()->randomElement(['detergent', 'fabric_softener', 'bleach', 'starch', 'hangers', 'bags', 'other']),
            'quantity' => fake()->numberBetween(10, 100),
            'unit' => fake()->randomElement(['bottle', 'box', 'piece', 'kg', 'liter']),
            'unit_price' => fake()->randomFloat(2, 5, 100),
            'reorder_level' => fake()->numberBetween(5, 20),
            'max_stock_level' => fake()->numberBetween(100, 500),
            'is_active' => true,
        ];
    }
}

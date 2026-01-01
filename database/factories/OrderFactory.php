<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Customer;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'status' => fake()->randomElement(['in_progress', 'ready', 'completed', 'cancelled']),
            'payment_status' => fake()->randomElement(['pending', 'partial', 'paid']),
            'subtotal' => $subtotal = fake()->randomFloat(2, 20, 500),
            'discount' => $discount = fake()->randomFloat(2, 0, 50),
            'tax' => $tax = ($subtotal - $discount) * 0.1,
            'total' => $subtotal - $discount + $tax,
            'paid_amount' => 0,
            'pickup_date' => fake()->dateTimeBetween('-1 week', 'now'),
            'delivery_date' => fake()->dateTimeBetween('now', '+1 week'),
            'notes' => fake()->optional()->sentence(),
            'special_instructions' => fake()->optional()->sentence(),
            'express_service' => fake()->boolean(20),
        ];
    }
}

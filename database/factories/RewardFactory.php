<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reward>
 */
class RewardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $usePercentage = fake()->boolean();
        
        return [
            'name' => fake()->words(3, true) . ' Reward',
            'description' => fake()->sentence(),
            'points_required' => fake()->randomElement([50, 100, 200, 500, 1000]),
            'discount_amount' => $usePercentage ? null : fake()->randomFloat(2, 5, 50),
            'discount_percentage' => $usePercentage ? fake()->randomElement([5, 10, 15, 20, 25]) : null,
            'is_active' => true,
        ];
    }
}

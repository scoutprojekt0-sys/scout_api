<?php

namespace Database\Factories;

use App\Models\Opportunity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OpportunityFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Opportunity::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->jobTitle() . ' Position Available',
            'description' => fake()->paragraphs(3, true),
            'type' => fake()->randomElement(['club_need', 'manager_need', 'trial', 'contract']),
            'location' => fake()->city() . ', ' . fake()->country(),
            'salary' => fake()->optional()->numberBetween(30000, 500000),
            'status' => 'active',
        ];
    }

    /**
     * Indicate that the opportunity is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }
}

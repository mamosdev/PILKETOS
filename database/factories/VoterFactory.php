<?php

namespace Database\Factories;

use App\Models\Voter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Voter>
 */
class VoterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cohort = fake()->randomElement(['X', 'XI', 'XII']);

        return [
            'identifier' => fake()->unique()->numerify('00########'),
            'name' => fake()->name(),
            'cohort' => $cohort,
            'group_name' => $cohort.' '.fake()->randomElement(['MIPA', 'IPS']).' '.fake()->numberBetween(1, 4),
        ];
    }
}

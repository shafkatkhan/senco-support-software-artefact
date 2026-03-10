<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pupil;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SchoolHistory>
 */
class SchoolHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pupil_id' => Pupil::factory(),
            'school_name' => fake()->company() . ' School',
            'school_type' => fake()->randomElement(['Mainstream State School', 'Special School', 'Grammar School', 'Independent School', 'Academy', 'PRU']),
            'class_type' => fake()->randomElement(['Mainstream Class', 'SEN Unit', 'Resource Base', 'Nurture Group', 'Alternative Provision']),
            'years_attended' => fake()->randomFloat(1, 0.5, 7),
            'transition_reason' => fake()->randomElement(['Moved to secondary school', 'Change of location', 'Required specialist provision', 'Parental choice', 'School closure', 'Behavioral difficulties']),
        ];
    }
}

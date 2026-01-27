<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pupil>
 */
class PupilFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dob' => fake()->dateTimeBetween('-16 years', '-10 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['Male', 'Female', 'Other']),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->secondaryAddress(),
            'locality' => fake()->city(),
            'postcode' => fake()->postcode(),
            'country' => 'United Kingdom',
            'joined_date' => fake()->dateTimeBetween('-6 years', 'now')->format('Y-m-d'),
            'initial_tutor_group' => fake()->bothify('#?'),
            'smoking_history' => fake()->boolean(10),
            'drug_abuse_history' => fake()->boolean(5),
        ];
    }
}

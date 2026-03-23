<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

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
            'auto_progression' => true,
            'onboarded_by' => User::inRandomOrder()->first()->id ?? User::factory(),
            'smoking_history' => fake()->boolean(10),
            'drug_abuse_history' => fake()->boolean(5),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'after_school_job' => fake()->boolean(20) ? fake()->jobTitle() : null,
            'has_special_needs' => $hasSpecialNeeds = fake()->boolean(30),
            'special_needs_details' => $hasSpecialNeeds ? fake()->sentence() : null,
            'attended_special_school' => $attendedSpecialSchool = fake()->boolean(20),
            'special_school_details' => $attendedSpecialSchool ? fake()->sentence() : null,
            'parental_description' => fake()->boolean(70) ? fake()->paragraph() : null,
            'treatment_plan' => fake()->boolean(70) ? fake()->paragraph() : null,
            'social_services_involvement' => fake()->boolean(15),
            'probation_officer_required' => fake()->boolean(15),
        ];
    }
}

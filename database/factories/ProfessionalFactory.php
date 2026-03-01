<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Professional>
 */
class ProfessionalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $roles = [
            'Educational Psychologist', 'Speech and Language Therapist', 'Social Worker', 'Physiotherapist', 'Occupational Therapist', 'Specialist Teacher', 'Pediatrician', 'Child Psychiatrist', 'Health Visitor', 'Counselor'
        ];

        $agencies = [
            'Local Authority', 'NHS', 'Child Services', 'CAMHS', 'School Trust', 'Private Practice', 'SENDIAS', 'Community Health'
        ];

        $domains = [
            'nhs.net', 'localauthority.gov.uk', 'school.sch.uk', 'childservices.gov.uk', 'clinic.co.uk'
        ];

        $first_name = $this->faker->firstName();
        $last_name = $this->faker->lastName();
        $domain = $this->faker->randomElement($domains);

        return [
            'title' => $this->faker->title(),
            'first_name' => $first_name,
            'last_name' => $last_name,
            'role' => $this->faker->randomElement($roles),
            'agency' => $this->faker->randomElement($agencies),
            'phone' => $this->faker->numerify('0#### ######'),
            'email' => strtolower($first_name . '.' . $last_name . '@' . $domain),
        ];
    }
}

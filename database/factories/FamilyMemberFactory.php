<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Pupil;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyMember>
 */
class FamilyMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $relations = ['Parent', 'Sibling', 'Guardian', 'Grandparent', 'Aunt', 'Uncle'];

        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'dob' => fake()->dateTimeBetween('-50 years', '-2 years')->format('Y-m-d'),
            'relation' => fake()->randomElement($relations),
            'pupil_id' => Pupil::factory(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'address_line_1' => fake()->streetAddress(),
            'address_line_2' => fake()->optional()->secondaryAddress(),
            'locality' => fake()->city(),
            'postcode' => fake()->postcode(),
            'country' => 'United Kingdom',
            'marital_status' => $this->faker->boolean(60) ? fake()->randomElement(['Married', 'Single', 'Divorced', 'Widowed', 'Separated']) : null,
            'highest_education' => $this->faker->boolean(60) ? fake()->randomElement(['GCSEs', 'A Levels', 'Bachelor\'s Degree', 'Master\'s Degree', 'PhD']) : null,
            'financial_status' => $this->faker->boolean(60) ? fake()->randomElement(['Stable', 'Struggling', 'Comfortable']) : null,
            'occupation' => $this->faker->boolean(40) ? fake()->jobTitle() : null,
            'state_support' => $this->faker->boolean(60) ? fake()->randomElement(['Universal Credit', 'Child Benefit', 'Housing Benefit']) : null,
        ];
    }
}

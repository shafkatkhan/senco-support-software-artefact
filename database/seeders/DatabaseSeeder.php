<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\TestForm;
use App\Models\Pupil;
use App\Models\Medication;
use App\Models\PupilFamilyMember;
use App\Models\Diagnosis;
use App\Models\Accommodation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // create groups
        $adminGroup = UserGroup::create([
            'name' => 'Admin',
            'description' => 'Full access to all system features.'
        ]);
        $standardGroup = UserGroup::create([
            'name' => 'Standard',
            'description' => 'Regular access to standard features.'
        ]);
        $readOnlyGroup = UserGroup::create([
            'name' => 'Read-Only',
            'description' => 'Can view data but cannot make changes.'
        ]);

        // create admin user
        $adminUser = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'user_group_id' => $adminGroup->id,
            'mobile' => '07777777777',
            'position' => 'Head Teacher',
            'joined_date' => now(),
        ]);

        // create 10 standard users
        User::factory(10)->create([
            'user_group_id' => $standardGroup->id,
            'added_by' => $adminUser->id,
        ]);

        // create 10 read-only users
        User::factory(10)->create([
            'user_group_id' => $readOnlyGroup->id,
            'added_by' => $adminUser->id,
        ]);

        // create test rows
        TestForm::factory(20)->create();

        // create pupils
        Pupil::factory(20)->create()->each(function ($pupil) {
            // add medications
            $count = rand(0, 4);
            if ($count > 0) {
                Medication::factory($count)->create(['pupil_id' => $pupil->id]);
            }
            
            // add family members
            $familyCount = rand(1, 3);
            $familyMembers = PupilFamilyMember::factory($familyCount)->create(['pupil_id' => $pupil->id]);

            // assign primary family member
            $pupil->update(['primary_family_member_id' => $familyMembers->random()->id]);

            // add diagnoses
            $diagnosisCount = rand(0, 3);
            if ($diagnosisCount > 0) {
                Diagnosis::factory($diagnosisCount)->create(['pupil_id' => $pupil->id]);
            }
        });

        // create accommodations
        $accommodations = [
            ['name' => 'Extra Time', 'detail' => '25% extra time in examinations.'],
            ['name' => 'Extended Formulae Sheet', 'detail' => 'Access to extended formulae sheet during maths exams.'],
            ['name' => 'Reading Pen', 'detail' => 'Use of a reading pen for text support.'],
            ['name' => 'Scribe', 'detail' => 'A scribe to write down answers dictated by the student.'],
            ['name' => 'Reader', 'detail' => 'A reader to read examination questions.'],
            ['name' => 'Disregard Spelling', 'detail' => 'Spelling and grammar errors are to be disregarded.'],
            ['name' => 'Adjusted Examination Format', 'detail' => 'Exams provided in large print or modified layout.'],
        ];
        foreach ($accommodations as $accommodation) {
            Accommodation::create($accommodation);
        }
    }
}

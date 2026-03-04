<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserGroup;
use App\Models\TestForm;
use App\Models\Pupil;
use App\Models\Medication;
use App\Models\FamilyMember;
use App\Models\Diagnosis;
use App\Models\Accommodation;
use App\Models\RecordType;
use App\Models\Professional;
use App\Models\Record;
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

        // create professionals
        Professional::factory(50)->create();

        // create pupils
        Pupil::factory(20)->create()->each(function ($pupil) {
            // add medications
            $count = rand(0, 4);
            if ($count > 0) {
                Medication::factory($count)->create(['pupil_id' => $pupil->id]);
            }
            
            // add family members
            $familyCount = rand(3, 5);
            $familyMembers = FamilyMember::factory($familyCount)->create(['pupil_id' => $pupil->id]);

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
            ['name' => 'Extra Time', 'description' => '25% extra time in examinations.'],
            ['name' => 'Extended Formulae Sheet', 'description' => 'Access to extended formulae sheet during maths exams.'],
            ['name' => 'Reading Pen', 'description' => 'Use of a reading pen for text support.'],
            ['name' => 'Scribe', 'description' => 'A scribe to write down answers dictated by the student.'],
            ['name' => 'Reader', 'description' => 'A reader to read examination questions.'],
            ['name' => 'Disregard Spelling', 'description' => 'Spelling and grammar errors are to be disregarded.'],
            ['name' => 'Adjusted Examination Format', 'description' => 'Exams provided in large print or modified layout.'],
        ];
        
        $createdAccommodations = collect();
        foreach ($accommodations as $accommodation) {
            $createdAccommodations->push(Accommodation::create($accommodation));
        }

        // assign accommodations to pupils
        Pupil::all()->each(function ($pupil) use ($createdAccommodations) {
            $count = rand(0, 3);
            if ($count > 0) {
                $randomAccommodations = $createdAccommodations->random($count)->pluck('id');
                $pupil->accommodations()->sync($randomAccommodations);
            }
        });

        // create record types
        $recordTypes = [
            ['name' => 'Medical', 'description' => 'Records relating to medical history, doctor visits, and health plans.'],
            ['name' => 'Criminal', 'description' => 'Records relating to police involvement, youth offending, or legal issues.'],
            ['name' => 'Educational', 'description' => 'Records relating to academic performance, IEPs, and learning plans.'],
            ['name' => 'Behavioral', 'description' => 'Records relating to behavior incidents, detentions, and interventions.'],
            ['name' => 'Safeguarding', 'description' => 'Sensitive records relating to child protection and safety concerns.'],
        ];
        foreach ($recordTypes as $type) {
            RecordType::create($type);
        }

        // create records
        Record::factory(70)->create();
    }
}

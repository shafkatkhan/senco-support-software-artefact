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
use App\Models\MeetingType;
use App\Models\Meeting;
use App\Models\Event;
use App\Models\Subject;
use App\Models\Major;
use App\Models\Proficiency;
use App\Models\Diet;
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
            'email' => 'admin@example.com',
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
            // add onboarded event
            Event::create([
                'pupil_id' => $pupil->id,
                'title' => 'Pupil Onboarded',
                'date' => $pupil->joined_date,
                'description' => 'Pupil profile created and onboarded into the system.',
            ]);

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

        // create record types
        $recordTypes = [
            ['name' => 'Medical', 'description' => 'Records relating to medical history, doctor visits, and health plans.'],
            ['name' => 'Criminal', 'description' => 'Records relating to police involvement, youth offending, or legal issues.'],
            ['name' => 'Educational', 'description' => 'Records relating to academic performance, IEPs, and learning plans.'],
            ['name' => 'Behavioral', 'description' => 'Records relating to behavior incidents, detentions, and interventions.'],
            ['name' => 'Safeguarding', 'description' => 'Sensitive records relating to child protection and safety concerns.'],
            ['name' => 'Other', 'description' => 'Other records.'],
        ];
        foreach ($recordTypes as $type) {
            RecordType::create($type);
        }

        // create records
        Record::factory(70)->create();

        // create meeting types
        $meetingTypes = [
            ['name' => 'EHCP Review', 'description' => 'Annual review of Education, Health and Care Plan.'],
            ['name' => 'Parent Meeting', 'description' => 'Meeting with parents to discuss progress or concerns.'],
            ['name' => 'Staff Case Conference', 'description' => 'Internal staff meeting to coordinate support.'],
            ['name' => 'External Agency Meeting', 'description' => 'Meeting involving external professionals (e.g. SALT, EdPsych).'],
            ['name' => 'Emergency Intervention', 'description' => 'Urgent meeting convened due to a critical incident.'],
            ['name' => 'Other', 'description' => 'Other meetings.'],
        ];
        foreach ($meetingTypes as $type) {
            MeetingType::create($type);
        }

        // create meetings
        Meeting::factory(50)->create();

        // create events
        Event::factory(50)->create();

        // create proficiencies
        $proficiencies = [
            ['name' => 'Foundation', 'description' => 'Core level subject pathway.'],
            ['name' => 'Higher', 'description' => 'Advanced level subject pathway.'],
        ];
        foreach ($proficiencies as $proficiency) {
            Proficiency::create($proficiency);
        }

        // create subjects
        $subjects = [
            ['name' => 'English', 'code' => 'ENG'],
            ['name' => 'Mathematics', 'code' => 'MAT'],
            ['name' => 'Science', 'code' => 'SCI'],
            ['name' => 'History', 'code' => 'HIS'],
            ['name' => 'Geography', 'code' => 'GEO'],
            ['name' => 'Religious Education', 'code' => 'RE'],
            ['name' => 'Art & Design', 'code' => 'ART'],
            ['name' => 'Music', 'code' => 'MUS'],
            ['name' => 'Physical Education', 'code' => 'PE'],
            ['name' => 'Computing', 'code' => 'COM'],
            ['name' => 'Design & Technology', 'code' => 'DT'],
            ['name' => 'Modern Foreign Languages', 'code' => 'MFL'],
            ['name' => 'PSHE', 'code' => 'PSHE'],
            ['name' => 'Drama', 'code' => 'DRA'],
            ['name' => 'Business Studies', 'code' => 'BUS'],
        ];
        foreach ($subjects as $subject) {
            Subject::create($subject);
        }

        // assign accommodations and proficiencies to subjects
        $proficiencyIds = Proficiency::pluck('id');
        Subject::all()->each(function ($subject) use ($createdAccommodations, $proficiencyIds) {
            $count = rand(2, 5);
            if ($count > 0) {
                $randomAccommodations = $createdAccommodations->random($count)->pluck('id');
                $subject->accommodations()->syncWithoutDetaching($randomAccommodations);
            }

            // 40% chance to assign all proficiencies
            if (rand(1, 100) <= 40) {
                $subject->proficiencies()->syncWithoutDetaching($proficiencyIds->all());
            }
        });

        // create majors
        $majors = [
            ['name' => 'Car Electronics', 'code' => 'CE'],
            ['name' => 'Carpentry', 'code' => 'CAR'],
            ['name' => 'Hairdressing', 'code' => 'HD'],
            ['name' => 'Plumbing', 'code' => 'PLU'],
            ['name' => 'Electrical Installation', 'code' => 'EI'],
            ['name' => 'Health & Social Care', 'code' => 'HSC'],
            ['name' => 'Childcare', 'code' => 'CC'],
            ['name' => 'Beauty Therapy', 'code' => 'BT'],
            ['name' => 'Animal Care', 'code' => 'AC'],
            ['name' => 'Computer Science', 'code' => 'CS'],
            ['name' => 'Engineering', 'code' => 'ENG'],
            ['name' => 'Nursing', 'code' => 'NUR'],
            ['name' => 'Architecture', 'code' => 'ARC'],
            ['name' => 'Accounting & Finance', 'code' => 'AF'],
            ['name' => 'Education', 'code' => 'EDU'],
            ['name' => 'Social Work', 'code' => 'SW'],
        ];
        foreach ($majors as $major) {
            Major::create($major);
        }

        // assign subjects to majors
        $subjectIds = Subject::pluck('id');
        Major::all()->each(function ($major) use ($subjectIds) {
            $count = rand(0, 3);
            if ($count > 0) {
                $randomSubjects = $subjectIds->random($count)->all();
                $major->subjects()->syncWithoutDetaching($randomSubjects);
            }
        });

        // create diets
        $pupils = Pupil::all();
        $statuses = ['Recommended', 'Approved'];
        foreach ($pupils as $pupil) {
            $count = rand(1, 4);
            $randomSubjects = Subject::inRandomOrder()->take($count)->get();

            foreach ($randomSubjects as $subject) {
                // assign a proficiency if the subject has any
                $proficiency = $subject->proficiencies()->inRandomOrder()->first();
                $proficiencyId = $proficiency ? $proficiency->id : null;

                $diet = Diet::create([
                    'pupil_id' => $pupil->id,
                    'subject_id' => $subject->id,
                    'proficiency_id' => $proficiencyId,
                ]);

                // assign some random accommodations with status and details
                $subjectAccomms = $subject->accommodations;
                if ($subjectAccomms->count() > 0) {
                    $accommCount = rand(1, min(3, $subjectAccomms->count()));
                    $randomAccomms = $subjectAccomms->random($accommCount);
                    $attachData = [];
                    foreach ($randomAccomms as $accomm) {
                        $attachData[$accomm->id] = [
                            'status' => collect($statuses)->random(),
                            'details' => fake()->sentence()
                        ];
                    }
                    $diet->accommodations()->attach($attachData);
                }
            }
        }
    }
}

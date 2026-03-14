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
use App\Models\Permission;
use App\Models\SchoolHistory;
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

        // define and create permissions
        $permissions = [
            // Pupil Management
            ['name' => 'View Pupils', 'slug' => 'view-pupils', 'description' => 'Can view the list of pupils and their details.'],
            ['name' => 'Create Pupils', 'slug' => 'create-pupils', 'description' => 'Can add new pupils.'],
            ['name' => 'Edit Pupils', 'slug' => 'edit-pupils', 'description' => 'Can edit existing pupils.'],
            ['name' => 'Delete Pupils', 'slug' => 'delete-pupils', 'description' => 'Can delete pupils.'],

            // Medication Management
            ['name' => 'View Medications', 'slug' => 'view-medications', 'description' => 'Can view a pupil\'s list of medications and their details.'],
            ['name' => 'Create Medications', 'slug' => 'create-medications', 'description' => 'Can add new medications.'],
            ['name' => 'Edit Medications', 'slug' => 'edit-medications', 'description' => 'Can edit existing medications.'],
            ['name' => 'Delete Medications', 'slug' => 'delete-medications', 'description' => 'Can delete medications.'],

            // Diagnosis Management
            ['name' => 'View Diagnoses', 'slug' => 'view-diagnoses', 'description' => 'Can view a pupil\'s list of diagnoses and their details.'],
            ['name' => 'Create Diagnoses', 'slug' => 'create-diagnoses', 'description' => 'Can add new diagnoses.'],
            ['name' => 'Edit Diagnoses', 'slug' => 'edit-diagnoses', 'description' => 'Can edit existing diagnoses.'],
            ['name' => 'Delete Diagnoses', 'slug' => 'delete-diagnoses', 'description' => 'Can delete diagnoses.'],

            // Record Management
            ['name' => 'View Records', 'slug' => 'view-records', 'description' => 'Can view a pupil\'s list of records and their details.'],
            ['name' => 'Create Records', 'slug' => 'create-records', 'description' => 'Can create new records.'],
            ['name' => 'Edit Records', 'slug' => 'edit-records', 'description' => 'Can edit existing records.'],
            ['name' => 'Delete Records', 'slug' => 'delete-records', 'description' => 'Can delete records.'],

            // Event Management
            ['name' => 'View Events', 'slug' => 'view-events', 'description' => 'Can view a pupil\'s list of events and their details.'],
            ['name' => 'Create Events', 'slug' => 'create-events', 'description' => 'Can create new events.'],
            ['name' => 'Edit Events', 'slug' => 'edit-events', 'description' => 'Can edit existing events.'],
            ['name' => 'Delete Events', 'slug' => 'delete-events', 'description' => 'Can delete events.'],

            // School History Management
            ['name' => 'View School Histories', 'slug' => 'view-school-histories', 'description' => 'Can view a pupil\'s list of school histories and their details.'],
            ['name' => 'Create School Histories', 'slug' => 'create-school-histories', 'description' => 'Can create new school histories.'],
            ['name' => 'Edit School Histories', 'slug' => 'edit-school-histories', 'description' => 'Can edit existing school histories.'],
            ['name' => 'Delete School Histories', 'slug' => 'delete-school-histories', 'description' => 'Can delete school histories.'],

            // Meeting Management
            ['name' => 'View Meetings', 'slug' => 'view-meetings', 'description' => 'Can view a pupil\'s list of meetings and their details.'],
            ['name' => 'Create Meetings', 'slug' => 'create-meetings', 'description' => 'Can create new meetings.'],
            ['name' => 'Edit Meetings', 'slug' => 'edit-meetings', 'description' => 'Can edit existing meetings.'],
            ['name' => 'Delete Meetings', 'slug' => 'delete-meetings', 'description' => 'Can delete meetings.'],

            // Diet Management
            ['name' => 'View Diets', 'slug' => 'view-diets', 'description' => 'Can view a pupil\'s list of diets and their details.'],
            ['name' => 'Add to Diets', 'slug' => 'add-to-diets', 'description' => 'Can add subjects to pupil diets.'],
            ['name' => 'Edit Diets', 'slug' => 'edit-diets', 'description' => 'Can edit existing diets.'],
            ['name' => 'Delete Diets', 'slug' => 'delete-diets', 'description' => 'Can delete diets.'],

            // Family Member Management
            ['name' => 'View Family Members', 'slug' => 'view-family-members', 'description' => 'Can view a pupil\'s list of family members and their details.'],
            ['name' => 'Create Family Members', 'slug' => 'create-family-members', 'description' => 'Can add new family members.'],
            ['name' => 'Edit Family Members', 'slug' => 'edit-family-members', 'description' => 'Can edit existing family members.'],
            ['name' => 'Delete Family Members', 'slug' => 'delete-family-members', 'description' => 'Can delete family members.'],

            // Accommodation Management
            ['name' => 'View Accommodations', 'slug' => 'view-accommodations', 'description' => 'Can view the list of accommodations and their details.'],
            ['name' => 'Create Accommodations', 'slug' => 'create-accommodations', 'description' => 'Can create new accommodations.'],
            ['name' => 'Edit Accommodations', 'slug' => 'edit-accommodations', 'description' => 'Can edit existing accommodations.'],
            ['name' => 'Delete Accommodations', 'slug' => 'delete-accommodations', 'description' => 'Can delete accommodations.'],

            // Major Management
            ['name' => 'View Majors', 'slug' => 'view-majors', 'description' => 'Can view the list of majors and their details.'],
            ['name' => 'Create Majors', 'slug' => 'create-majors', 'description' => 'Can create new majors.'],
            ['name' => 'Edit Majors', 'slug' => 'edit-majors', 'description' => 'Can edit existing majors.'],
            ['name' => 'Delete Majors', 'slug' => 'delete-majors', 'description' => 'Can delete majors.'],

            // Proficiency Management
            ['name' => 'View Proficiencies', 'slug' => 'view-proficiencies', 'description' => 'Can view the list of proficiencies and their details.'],
            ['name' => 'Create Proficiencies', 'slug' => 'create-proficiencies', 'description' => 'Can create new proficiencies.'],
            ['name' => 'Edit Proficiencies', 'slug' => 'edit-proficiencies', 'description' => 'Can edit existing proficiencies.'],
            ['name' => 'Delete Proficiencies', 'slug' => 'delete-proficiencies', 'description' => 'Can delete proficiencies.'],

            // Subject Management
            ['name' => 'View Subjects', 'slug' => 'view-subjects', 'description' => 'Can view the list of subjects and their details.'],
            ['name' => 'Create Subjects', 'slug' => 'create-subjects', 'description' => 'Can create new subjects.'],
            ['name' => 'Edit Subjects', 'slug' => 'edit-subjects', 'description' => 'Can edit existing subjects.'],
            ['name' => 'Delete Subjects', 'slug' => 'delete-subjects', 'description' => 'Can delete subjects.'],

            // Record Type Management
            ['name' => 'View Record Types', 'slug' => 'view-record-types', 'description' => 'Can view the list of record types and their details.'],
            ['name' => 'Create Record Types', 'slug' => 'create-record-types', 'description' => 'Can create new record types.'],
            ['name' => 'Edit Record Types', 'slug' => 'edit-record-types', 'description' => 'Can edit existing record types.'],
            ['name' => 'Delete Record Types', 'slug' => 'delete-record-types', 'description' => 'Can delete record types.'],

            // Meeting Type Management
            ['name' => 'View Meeting Types', 'slug' => 'view-meeting-types', 'description' => 'Can view the list of meeting types and their details.'],
            ['name' => 'Create Meeting Types', 'slug' => 'create-meeting-types', 'description' => 'Can create new meeting types.'],
            ['name' => 'Edit Meeting Types', 'slug' => 'edit-meeting-types', 'description' => 'Can edit existing meeting types.'],
            ['name' => 'Delete Meeting Types', 'slug' => 'delete-meeting-types', 'description' => 'Can delete meeting types.'],

            // Professional Management
            ['name' => 'View Professionals', 'slug' => 'view-professionals', 'description' => 'Can view the list of professionals and their details.'],
            ['name' => 'Create Professionals', 'slug' => 'create-professionals', 'description' => 'Can create new professionals.'],
            ['name' => 'Edit Professionals', 'slug' => 'edit-professionals', 'description' => 'Can edit existing professionals.'],
            ['name' => 'Delete Professionals', 'slug' => 'delete-professionals', 'description' => 'Can delete professionals.'],

            // Attachment Management
            ['name' => 'Manage Attachments', 'slug' => 'manage-attachments', 'description' => 'Can view, edit and delete attachments in dedicated attachments page.'],

            // User Group Management
            ['name' => 'View User Groups', 'slug' => 'view-user-groups', 'description' => 'Can view the list of user groups and their details.'],
            ['name' => 'Create User Groups', 'slug' => 'create-user-groups', 'description' => 'Can create new user groups.'],
            ['name' => 'Edit User Groups', 'slug' => 'edit-user-groups', 'description' => 'Can edit existing user groups.'],
            ['name' => 'Delete User Groups', 'slug' => 'delete-user-groups', 'description' => 'Can delete user groups.'],

            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'description' => 'Can view the list of users and their details.'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'description' => 'Can create new users.'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'description' => 'Can edit existing users.'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'description' => 'Can delete users.'],            

            // Settings Management
            ['name' => 'Manage Email Settings', 'slug' => 'manage-email-settings', 'description' => 'Can view and modify email settings.'],
            ['name' => 'Manage MFA Settings', 'slug' => 'manage-mfa-settings', 'description' => 'Can view and modify MFA settings.'],
            ['name' => 'Manage Permissions', 'slug' => 'manage-permissions', 'description' => 'Can view and modify permissions.'],

            // Backup Management
            ['name' => 'View & Download Backups', 'slug' => 'view-download-backups', 'description' => 'Can view and download backups.'],
            ['name' => 'Create Backups', 'slug' => 'create-backups', 'description' => 'Can create backups.'],
            ['name' => 'Delete Backups', 'slug' => 'delete-backups', 'description' => 'Can delete backups.'],
        ];

        $createdPermissions = collect();
        foreach ($permissions as $permission) {
            $createdPermissions->push(Permission::create($permission));
        }

        // assign permissions to Admin Group (all permissions)
        $adminGroup->permissions()->attach(
            $createdPermissions->pluck('id')->all()
        );

        // assign permissions to Standard Group (all non-delete, non-settings)
        $standardGroup->permissions()->attach(
            $createdPermissions
                ->reject(fn ($permission) => str_contains($permission->slug, 'delete') || str_contains($permission->slug, 'settings') || str_contains($permission->slug, 'permissions') || str_contains($permission->slug, 'backups') || str_contains($permission->slug, 'user'))
                ->pluck('id')
                ->all()
        );

        // assign permissions to Read-Only Group (only view permissions)
        $readOnlyGroup->permissions()->attach(
            $createdPermissions
                ->filter(fn ($permission) => str_contains($permission->slug, 'view') && !str_contains($permission->slug, 'backups') && !str_contains($permission->slug, 'user'))
                ->pluck('id')
                ->all()
        );

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
            // update pupil number
            $pupil->update([
                'pupil_number' => 'PUP-' . str_pad($pupil->id, 6, '0', STR_PAD_LEFT),
            ]);

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

            // add previous schools
            $schoolHistoryCount = rand(0, 3);
            if ($schoolHistoryCount > 0) {
                SchoolHistory::factory($schoolHistoryCount)->create(['pupil_id' => $pupil->id]);
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

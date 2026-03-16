<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pupil;
use App\Models\RecordType;
use App\Models\Professional;
use App\Models\MeetingType;
use App\Models\Subject;
use App\Models\Proficiency;
use App\Models\FamilyMember;
use App\Models\SchoolHistory;
use App\Models\Diagnosis;
use App\Models\Medication;
use App\Models\Record;
use App\Models\Event;
use App\Services\LlmService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Barryvdh\DomPDF\Facade\Pdf;

class PupilController extends Controller
{
    public function extractFromFile(Request $request)
    {
        $recordTypes = RecordType::pluck('name')->implode(', ');

        $response_format_instructions = "
            pupil_number (pupil's student number),
            first_name (pupil's first name),
            last_name (pupil's last name),
            dob (pupil's date of birth, format YYYY-MM-DD),
            gender (pupil's gender, Male, Female, Other),
            joined_date (date pupil joined the school, format YYYY-MM-DD),
            initial_tutor_group (pupil's initial tutor group),
            phone (pupil's phone number),
            email (pupil's email address),
            after_school_job (pupil's after school job),
            address_line_1 (pupil's address line 1),
            address_line_2 (pupil's address line 2),
            locality (pupil's town/city),
            postcode (pupil's postcode),
            country (pupil's country),
            sen_and_background (object with exact keys: 
                parental_description (description of student by parents),
                has_special_needs (boolean, true if pupil has special needs), 
                special_needs_details (details of the special needs), 
                attended_special_school (boolean, true if pupil has attended a special school), 
                special_school_details (details of the special school), 
                smoking_history (boolean, true if pupil has smoking history), 
                drug_abuse_history (boolean, true if pupil has drug abuse history)
            ),
            safeguarding_and_probation (object with exact keys: 
                social_services_involvement (boolean, true if social services are involved with the pupil), 
                social_worker (object with exact keys:
                    prof_title (professional's title e.g. Dr, Mr, Mrs),
                    prof_first_name (professional's first name),
                    prof_last_name (professional's last name),
                    prof_role (professional's role),
                    prof_agency (professional's agency/organisation),
                    prof_phone (professional's phone),
                    prof_email (professional's email)
                ),
                probation_officer_required (boolean, true if a probation officer is required for the pupil),
                probation_officer (object with exact keys:
                    prof_title (professional's title e.g. Dr, Mr, Mrs),
                    prof_first_name (professional's first name),
                    prof_last_name (professional's last name),
                    prof_role (professional's role),
                    prof_agency (professional's agency/organisation),
                    prof_phone (professional's phone),
                    prof_email (professional's email)
                )
            ),
            family_members (array of objects with exact keys: 
                first_name (family member's first name), 
                last_name (family member's last name), 
                relation (family member's relationship to the pupil), 
                dob (family member's date of birth, format YYYY-MM-DD), 
                phone (family member's phone number), 
                email (family member's email address), 
                address_line_1 (family member's address line 1), 
                address_line_2 (family member's address line 2), 
                locality (family member's town/city), 
                postcode (family member's postcode), 
                country (family member's country), 
                marital_status (family member's marital status), 
                highest_education (family member's highest level of education), 
                financial_status (family member's financial status), 
                occupation (family member's occupation or job title), 
                state_support (family member's state support or benefits, e.g. jobseeker's allowance, disability benefits, universal credit, etc.), 
                next_of_kin (boolean, true if this is the pupil's next of kin)
            ),
            school_histories (array of objects with exact keys: 
                school_name (school name),
                school_type (institution type, e.g. state school, grammar school, special school, private school, etc.),
                class_type (type of class),
                years_attended (number of years attended, format: number with optional decimal point),
                transition_reason (reason for transition)
            ),
            diagnoses (array of objects with exact keys: 
                name (the diagnosis name),
                date (date diagnosed, format YYYY-MM-DD), 
                professional (object with exact keys:
                    prof_title (professional's title e.g. Dr, Mr, Mrs),
                    prof_first_name (professional's first name),
                    prof_last_name (professional's last name),
                    prof_role (professional's role),
                    prof_agency (professional's agency/organisation),
                    prof_phone (professional's phone),
                    prof_email (professional's email)
                ),
                description (description of the diagnosis), 
                recommendations (recommended actions)
            ),
            medications (array of objects with exact keys: 
                name (the medication name),
                dosage (e.g. 50mg, 5ml), 
                frequency (e.g. Twice Daily, As Needed), 
                time_of_day (e.g. Morning, Night, 1:30pm), 
                administration_method (e.g. Oral, Injection), 
                start_date (date started, format YYYY-MM-DD), 
                end_date (date to end, format YYYY-MM-DD), 
                expiry_date (expiry date, format YYYY-MM-DD), 
                storage_instructions (any specific storage requirements), 
                self_administer (boolean, true if the pupil self-administers)
            ),
            records (array of objects with exact keys: 
                record_type (type of the record, choose one of the following that best fits the record: [{$recordTypes}], or return null if none fit),
                title (record title),
                date (record date, format YYYY-MM-DD),
                reference_number (record reference number),
                professional (object with exact keys:
                    prof_title (professional's title e.g. Dr, Mr, Mrs),
                    prof_first_name (professional's first name),
                    prof_last_name (professional's last name),
                    prof_role (professional's role),
                    prof_agency (professional's agency/organisation),
                    prof_phone (professional's phone),
                    prof_email (professional's email)
                ),
                description (record description),
                outcome (outcome or next steps)
            ).
        ";

        return LlmService::extractAndRespond($request, $response_format_instructions);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        Gate::authorize('view-pupils');
        
        $pupils = Pupil::with('attachments', 'medications', 'onboardedBy', 'primaryFamilyMember', 'diagnoses')->get();
        $title = "Pupils";
        return view('pupils', compact('pupils', 'title'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        Gate::authorize('create-pupils');

        $professionals = Professional::orderBy('last_name')->get();
        $record_types = RecordType::orderBy('name')->get();
        $title = "Onboard New Pupil";
        return view('pupils.create', compact('title', 'professionals', 'record_types'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create-pupils');

        $validated = $request->validate([
            'pupil_number' => 'required|string|max:255|unique:pupils,pupil_number',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'locality' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'after_school_job' => 'nullable|string|max:255',
            'joined_date' => 'nullable|date',
            'initial_tutor_group' => 'nullable|string|max:255',

            'has_special_needs' => 'boolean',
            'special_needs_details' => 'nullable|string',
            'attended_special_school' => 'boolean',
            'special_school_details' => 'nullable|string',
            'smoking_history' => 'boolean',
            'drug_abuse_history' => 'boolean',
            
            'parental_description' => 'nullable|string',

            'social_services_involvement' => 'boolean',
            'social_worker' => 'nullable|array',
            'social_worker.professional_id' => 'nullable|exists:professionals,id',
            'social_worker.is_new_professional' => 'nullable|boolean',
            'social_worker.prof_first_name' => 'nullable|string|max:255',
            'social_worker.prof_last_name' => 'nullable|string|max:255',
            'social_worker.prof_title' => 'nullable|string|max:255',
            'social_worker.prof_role' => 'nullable|string|max:255',
            'social_worker.prof_agency' => 'nullable|string|max:255',
            'social_worker.prof_phone' => 'nullable|string|max:255',
            'social_worker.prof_email' => 'nullable|email|max:255',
            
            'probation_officer_required' => 'boolean',
            'probation_officer' => 'nullable|array',
            'probation_officer.professional_id' => 'nullable|exists:professionals,id',
            'probation_officer.is_new_professional' => 'nullable|boolean',
            'probation_officer.prof_first_name' => 'nullable|string|max:255',
            'probation_officer.prof_last_name' => 'nullable|string|max:255',
            'probation_officer.prof_title' => 'nullable|string|max:255',
            'probation_officer.prof_role' => 'nullable|string|max:255',
            'probation_officer.prof_agency' => 'nullable|string|max:255',
            'probation_officer.prof_phone' => 'nullable|string|max:255',
            'probation_officer.prof_email' => 'nullable|email|max:255',

            'family_members' => 'array',
            'family_members.*.first_name' => 'nullable|string|max:255',
            'family_members.*.last_name' => 'nullable|string|max:255',
            'family_members.*.relation' => 'nullable|string|max:255',
            'family_members.*.dob' => 'nullable|date',
            'family_members.*.phone' => 'nullable|string|max:255',
            'family_members.*.email' => 'nullable|email|max:255',
            'family_members.*.marital_status' => 'nullable|string|max:255',
            'family_members.*.highest_education' => 'nullable|string|max:255',
            'family_members.*.financial_status' => 'nullable|string|max:255',
            'family_members.*.occupation' => 'nullable|string|max:255',
            'family_members.*.state_support' => 'nullable|string|max:255',
            'family_members.*.address_line_1' => 'nullable|string|max:255',
            'family_members.*.address_line_2' => 'nullable|string|max:255',
            'family_members.*.locality' => 'nullable|string|max:255',
            'family_members.*.postcode' => 'nullable|string|max:255',
            'family_members.*.country' => 'nullable|string|max:255',

            'school_histories' => 'array',
            'school_histories.*.school_name' => 'nullable|string|max:255',
            'school_histories.*.school_type' => 'nullable|string',
            'school_histories.*.class_type' => 'nullable|string',
            'school_histories.*.years_attended' => 'nullable|numeric',
            'school_histories.*.transition_reason' => 'nullable|string',

            'diagnoses' => 'array',
            'diagnoses.*.name' => 'nullable|string|max:255',
            'diagnoses.*.date' => 'nullable|date',
            'diagnoses.*.professional_id' => 'nullable|exists:professionals,id',
            'diagnoses.*.description' => 'nullable|string',
            'diagnoses.*.recommendations' => 'nullable|string',
            'diagnoses.*.is_new_professional' => 'nullable|boolean',
            'diagnoses.*.prof_title' => 'nullable|string|max:255',
            'diagnoses.*.prof_first_name' => 'nullable|string|max:255',
            'diagnoses.*.prof_last_name' => 'nullable|string|max:255',
            'diagnoses.*.prof_role' => 'nullable|string|max:255',
            'diagnoses.*.prof_agency' => 'nullable|string|max:255',
            'diagnoses.*.prof_phone' => 'nullable|string|max:255',
            'diagnoses.*.prof_email' => 'nullable|email|max:255',

            'medications' => 'array',
            'medications.*.name' => 'nullable|string|max:255',
            'medications.*.dosage' => 'nullable|string|max:255',
            'medications.*.frequency' => 'nullable|string|max:255',
            'medications.*.time_of_day' => 'nullable|string|max:255',
            'medications.*.administration_method' => 'nullable|string|max:255',
            'medications.*.start_date' => 'nullable|date',
            'medications.*.end_date' => 'nullable|date',
            'medications.*.expiry_date' => 'nullable|date',
            'medications.*.storage_instructions' => 'nullable|string',
            'medications.*.self_administer' => 'boolean',

            'records' => 'array',
            'records.*.record_type_id' => 'required_with:records|exists:record_types,id',
            'records.*.title' => 'nullable|string|max:255',
            'records.*.date' => 'nullable|date',
            'records.*.reference_number' => 'nullable|string|max:255',
            'records.*.professional_id' => 'nullable|exists:professionals,id',
            'records.*.description' => 'nullable|string',
            'records.*.outcome' => 'nullable|string',
            'records.*.is_new_professional' => 'nullable|boolean',
            'records.*.prof_title' => 'nullable|string|max:255',
            'records.*.prof_first_name' => 'nullable|string|max:255',
            'records.*.prof_last_name' => 'nullable|string|max:255',
            'records.*.prof_role' => 'nullable|string|max:255',
            'records.*.prof_agency' => 'nullable|string|max:255',
            'records.*.prof_phone' => 'nullable|string|max:255',
            'records.*.prof_email' => 'nullable|email|max:255',

            'llm_attachment' => 'nullable|file',
            'llm_transcript' => 'nullable|string',
            'additional_attachments' => 'nullable|array',
            'additional_attachments.*' => 'file',
        ]);
        

        // wrap in transaction for protection against data corruption and to ensure data integrity
        \DB::beginTransaction();

        try {
            // helper to process inline professionals
            $processInlineProfessional = function($item) {
                if (isset($item['is_new_professional']) && $item['is_new_professional'] == '1') {
                    $professional = Professional::create([
                        'title' => $item['prof_title'] ?? null,
                        'first_name' => $item['prof_first_name'],
                        'last_name' => $item['prof_last_name'],
                        'role' => $item['prof_role'] ?? null,
                        'agency' => $item['prof_agency'] ?? null,
                        'phone' => $item['prof_phone'] ?? null,
                        'email' => $item['prof_email'] ?? null,
                    ]);
                    $item['professional_id'] = $professional->id;
                }
                return $item;
            };

            // create pupil
            $pupilData = collect($validated)->only([
                'pupil_number', 'first_name', 'last_name', 'dob', 'gender', 'address_line_1', 'address_line_2', 'locality', 'postcode', 'country', 'phone', 'email', 'after_school_job', 'joined_date', 'initial_tutor_group', 'special_needs_details', 'special_school_details', 'parental_description'
            ])->toArray();            
            $pupilData['smoking_history'] = $request->has('smoking_history');
            $pupilData['drug_abuse_history'] = $request->has('drug_abuse_history');
            $pupilData['has_special_needs'] = $request->has('has_special_needs');
            $pupilData['attended_special_school'] = $request->has('attended_special_school');
            $pupilData['social_services_involvement'] = $request->has('social_services_involvement');
            $pupilData['probation_officer_required'] = $request->has('probation_officer_required');
            $pupilData['onboarded_by'] = auth()->id();
            $pupil = Pupil::create($pupilData);

            // create family members
            $primaryFamilyMemberId = null;
            foreach ($validated['family_members'] ?? [] as $index => $familyMemberData) {
                $familyMemberData['pupil_id'] = $pupil->id;
                $family_member = FamilyMember::create($familyMemberData);
                if ($index === 0) {
                    $primaryFamilyMemberId = $family_member->id;
                }
            }
            // update primary family member
            if ($primaryFamilyMemberId) {
                $pupil->update(['primary_family_member_id' => $primaryFamilyMemberId]);
            }

            // create school histories
            foreach ($validated['school_histories'] ?? [] as $schoolHistoryData) {
                $schoolHistoryData['pupil_id'] = $pupil->id;
                SchoolHistory::create($schoolHistoryData);
            }

            // create diagnoses
            foreach ($validated['diagnoses'] ?? [] as $diagnosisData) {
                $diagnosisData = $processInlineProfessional($diagnosisData);
                $diagnosisData['pupil_id'] = $pupil->id;
                Diagnosis::create($diagnosisData);
            }

            // create medications
            foreach ($validated['medications'] ?? [] as $medicationData) {
                $medicationData['pupil_id'] = $pupil->id;
                $medicationData['self_administer'] = $medicationData['self_administer'] ?? false;
                Medication::create($medicationData);
            }

            // create records
            foreach ($validated['records'] ?? [] as $recordData) {
                $recordData = $processInlineProfessional($recordData);
                $recordData['pupil_id'] = $pupil->id;
                Record::create($recordData);
            }

            // process social services
            if ($pupil->social_services_involvement && !empty($validated['social_worker'])) {
                if (empty($validated['social_worker']['prof_role'])) {
                    $validated['social_worker']['prof_role'] = 'Social Worker';
                }
                $socialWorkerData = $processInlineProfessional($validated['social_worker']);
                if (!empty($socialWorkerData['professional_id'])) {
                    $pupil->social_services_professional_id = $socialWorkerData['professional_id'];
                }
            }

            // process probation officer
            if ($pupil->probation_officer_required && !empty($validated['probation_officer'])) {
                if (empty($validated['probation_officer']['prof_role'])) {
                    $validated['probation_officer']['prof_role'] = 'Probation Officer';
                }
                $probationOfficerData = $processInlineProfessional($validated['probation_officer']);
                if (!empty($probationOfficerData['professional_id'])) {
                    $pupil->probation_officer_professional_id = $probationOfficerData['professional_id'];
                }
            }

            $pupil->save();

            // create onboarding event
            Event::create([
                'pupil_id' => $pupil->id,
                'title' => 'Pupil Onboarded',
                'date' => now(),
                'description' => 'Pupil profile created and onboarded into the system.'
            ]);

            $pupil->saveLlmAttachment($request->file('llm_attachment'), $request->input('llm_transcript'));
            $pupil->saveAttachments($request->file('additional_attachments'));

            \DB::commit();

            return redirect()->route('pupils.show', $pupil->id)->with('success', 'Pupil successfully onboarded!');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'An error occurred during onboarding: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Pupil $pupil)
    {
        Gate::authorize('view-pupils');

        $pupil->load('attachments', 'medications', 'onboardedBy', 'primaryFamilyMember', 'diagnoses.professional', 'records.professional', 'records.recordType', 'socialServicesProfessional', 'probationOfficerProfessional');
        
        $involvements = $pupil->involvements;

        $title = $pupil->first_name . " " . $pupil->last_name . "'s Details";
        return view('pupils.show', compact('pupil', 'title', 'involvements'));
    }

    public function medications(Pupil $pupil)
    {
        Gate::authorize('view-medications');

        $pupil->load('medications.attachments');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Medications";
        return view('pupils.medications', compact('pupil', 'title'));
    }

    public function diagnoses(Pupil $pupil)
    {
        Gate::authorize('view-diagnoses');

        $pupil->load('diagnoses.professional', 'diagnoses.attachments');
        $professionals = Professional::orderBy('last_name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Diagnoses";
        return view('pupils.diagnoses', compact('pupil', 'title', 'professionals'));
    }

    public function records(Pupil $pupil)
    {
        Gate::authorize('view-records');

        $pupil->load(['records.recordType', 'records.professional', 'records.attachments']);
        $record_types = RecordType::all();
        $professionals = Professional::orderBy('last_name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Records";
        return view('pupils.records', compact('pupil', 'title', 'record_types', 'professionals'));
    }

    public function events(Pupil $pupil)
    {
        Gate::authorize('view-events');

        $pupil->load('events.attachments');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Events";
        return view('pupils.events', compact('pupil', 'title'));
    }

    public function familyMembers(Pupil $pupil)
    {
        Gate::authorize('view-family-members');

        $pupil->load('familyMembers.attachments');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Family Members";
        return view('pupils.family_members', compact('pupil', 'title'));
    }

    public function meetings(Pupil $pupil)
    {
        Gate::authorize('view-meetings');

        $pupil->load(['meetings.meetingType', 'meetings.attachments']);
        $meeting_types = MeetingType::all();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Meetings";
        return view('pupils.meetings', compact('pupil', 'title', 'meeting_types'));
    }

    public function diets(Pupil $pupil)
    {
        Gate::authorize('view-diets');

        $pupil->load(['diets.subject', 'diets.proficiency', 'diets.accommodations']);
        $subjects = Subject::with(['proficiencies', 'accommodations'])->orderBy('name')->get();
        $title = $pupil->first_name . " " . $pupil->last_name . "'s Diet";
        return view('pupils.diets', compact('pupil', 'title', 'subjects'));
    }

    public function schoolHistories(Pupil $pupil)
    {
        Gate::authorize('view-school-histories');

        $pupil->load('schoolHistories.attachments');
        $title = $pupil->first_name . " " . $pupil->last_name . "'s School History";
        return view('pupils.school_histories', compact('pupil', 'title'));
    }

    public function attachments(Pupil $pupil)
    {
        Gate::authorize('manage-attachments');

        $pupil->load([
            'attachments.transcription',
            'attachments.attachable',
            'medications.attachments.transcription',
            'medications.attachments.attachable',
            'diagnoses.attachments.transcription',
            'diagnoses.attachments.attachable',
            'records.recordType',
            'records.attachments.transcription',
            'records.attachments.attachable',
            'events.attachments.transcription',
            'events.attachments.attachable',
            'familyMembers.attachments.transcription',
            'familyMembers.attachments.attachable',
            'meetings.attachments.transcription',
            'meetings.attachments.attachable',
            'schoolHistories.attachments.transcription',
            'schoolHistories.attachments.attachable'
        ]);

        $allAttachments = collect()
            ->concat($pupil->attachments)
            ->concat($pupil->medications->flatMap->attachments)
            ->concat($pupil->diagnoses->flatMap->attachments)
            ->concat($pupil->records->flatMap->attachments)
            ->concat($pupil->events->flatMap->attachments)
            ->concat($pupil->familyMembers->flatMap->attachments)
            ->concat($pupil->meetings->flatMap->attachments)
            ->concat($pupil->schoolHistories->flatMap->attachments);

        $title = $pupil->first_name . " " . $pupil->last_name . "'s Attachments";
        return view('pupils.attachments', compact('pupil', 'title', 'allAttachments'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pupil $pupil)
    {
        Gate::authorize('edit-pupils');

        $pupil->load('familyMembers');
        $professionals = Professional::orderBy('last_name')->get();
        $title = 'Edit ' . $pupil->first_name . ' ' . $pupil->last_name;

        return view('pupils.edit', compact('pupil', 'professionals', 'title'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pupil $pupil)
    {
        Gate::authorize('edit-pupils');

        $validated = $request->validate([
            'pupil_number' => 'required|string|max:255|unique:pupils,pupil_number,' . $pupil->id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|max:255',
            'address_line_1' => 'nullable|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'locality' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'after_school_job' => 'nullable|string|max:255',
            'joined_date' => 'nullable|date',
            'initial_tutor_group' => 'nullable|string|max:255',
            'primary_family_member_id' => [
                'nullable',
                Rule::exists('family_members', 'id')->where('pupil_id', $pupil->id)
            ],
            'parental_description' => 'nullable|string',

            'has_special_needs' => 'boolean',
            'special_needs_details' => 'nullable|string',
            'attended_special_school' => 'boolean',
            'special_school_details' => 'nullable|string',
            'smoking_history' => 'boolean',
            'drug_abuse_history' => 'boolean',

            'social_services_involvement' => 'boolean',
            'social_services_professional_id' => 'nullable|exists:professionals,id',
            'probation_officer_required' => 'boolean',
            'probation_officer_professional_id' => 'nullable|exists:professionals,id',
        ]);

        $data = collect($validated)->only([
            'first_name',
            'pupil_number',
            'last_name',
            'dob',
            'gender',
            'joined_date',
            'initial_tutor_group',
            'phone',
            'email',
            'after_school_job',
            'primary_family_member_id',
            'address_line_1',
            'address_line_2',
            'locality',
            'postcode',
            'country',
            'parental_description',
        ])->toArray();

        $data['smoking_history'] = $request->boolean('smoking_history');
        $data['drug_abuse_history'] = $request->boolean('drug_abuse_history');
        $data['has_special_needs'] = $request->boolean('has_special_needs');
        $data['special_needs_details'] = $data['has_special_needs'] ? ($validated['special_needs_details'] ?? null) : null;
        $data['attended_special_school'] = $request->boolean('attended_special_school');
        $data['special_school_details'] = $data['attended_special_school'] ? ($validated['special_school_details'] ?? null) : null;
        $data['social_services_involvement'] = $request->boolean('social_services_involvement');
        $data['social_services_professional_id'] = $data['social_services_involvement'] ? ($validated['social_services_professional_id'] ?? null) : null;
        $data['probation_officer_required'] = $request->boolean('probation_officer_required');
        $data['probation_officer_professional_id'] = $data['probation_officer_required'] ? ($validated['probation_officer_professional_id'] ?? null) : null;

        $pupil->update($data);

        return redirect()->route('pupils.show', $pupil->id)->with('success', 'Pupil Updated Successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pupil $pupil)
    {
        Gate::authorize('delete-pupils');

        try {
            $pupil->delete();
            return redirect()->route('pupils.index')->with('success', 'Pupil Deleted Successfully!');
        } catch (QueryException $e) {
            return redirect()->route('pupils.index')->with('error', 'Something went wrong.');
        }
    }

    /**
     * Export the specified pupil's data to a PDF.
     */
    public function export(Pupil $pupil)
    {
        Gate::authorize('export-pupil-data');

        $pupil->load([
            'primaryFamilyMember', 
            'socialServicesProfessional',
            'probationOfficerProfessional',
            'onboardedBy',
            'medications',
            'diagnoses.professional',
            'records.professional',
            'records.recordType',
            'diets.subject'
        ]);

        $involvements = $pupil->involvements;

        $title = 'Pupil Profile Summary - ' . $pupil->first_name . ' ' . $pupil->last_name . ' (' . $pupil->pupil_number . ')';
        $pdf = Pdf::loadView('pdfs.pupil_profile_summary', compact('pupil', 'title', 'involvements'));
        $filename = str_replace(' ', '-', $pupil->pupil_number . '-' . $pupil->first_name . '-' . $pupil->last_name) . '-Profile-Summary.pdf';

        return $pdf->download($filename);
    }
}

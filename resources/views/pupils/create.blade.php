@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
        </div>
        <div class="row settings_wrap onboarding_wrap">
            <div class="col-md-8 d-flex flex-column">                
                <div class="settings_section">
                    <div class="title">
                        <i class="fas fa-user-plus me-2"></i>Onboard New Pupil
                    </div>
                    <div class="description">
                        Please fill in the comprehensive pupil information below.
                    </div>
                    <hr>
                    <form action="{{ route('pupils.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @include('components.file_extraction_box')
                        <div class="title">
                            <i class="fas fa-id-card me-2"></i>Core Pupil Information
                        </div>
                        <div class="description">
                            Enter the primary details for the pupil.
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pupil Number <span class="text-danger">*</span></label>
                                <input type="text" name="pupil_number" class="form-control" value="{{ old('pupil_number') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label>
                                <select name="gender" class="form-select" required>
                                    <option value="" selected disabled></option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Joined Date</label>
                                <input type="date" name="joined_date" class="form-control" value="{{ old('joined_date', date('Y-m-d')) }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Year Group <span class="text-danger">*</span></label>
                                <input type="number" name="year_group" class="form-control" value="{{ old('year_group') }}" min="1" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">Tutor Group</label>
                                <input type="text" name="tutor_group" class="form-control" value="{{ old('tutor_group') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label class="form-label">After School Job</label>
                                <input type="text" name="after_school_job" class="form-control" value="{{ old('after_school_job') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address Line 1</label>
                                <input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Address Line 2</label>
                                <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Town/City</label>
                                <input type="text" name="locality" class="form-control" value="{{ old('locality') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Postcode</label>
                                <input type="text" name="postcode" class="form-control" value="{{ old('postcode') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Country</label>
                                <input type="text" name="country" class="form-control" value="{{ old('country') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="title">
                            <i class="fas fa-folder-open me-2"></i>Additional Information (Optional)
                        </div>
                        <div class="description">
                            Attach related family members, medical details, and other records to the pupil's profile.
                        </div>
                        <div id="onboardingAccordion" class="accordion">
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingFamilyMembers">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFamilyMembers">
                                        <i class="fas fa-users"></i> Family Members
                                    </button>
                                </h2>
                                <div id="collapseFamilyMembers" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="family_member_rows">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addFamilyMemberRow()">+ Add Family Member</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSchools">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSchools">
                                        <i class="fas fa-graduation-cap"></i> School History
                                    </button>
                                </h2>
                                <div id="collapseSchools" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="school_rows">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addSchoolRow()">+ Add School History</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingBackground">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBackground">
                                        <i class="fas fa-wheelchair"></i> SEN & Background
                                    </button>
                                </h2>
                                <div id="collapseBackground" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Description of student by parents (or legal guardian)</label>
                                                <textarea name="parental_description" class="form-control" rows="4"></textarea>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="has_special_needs" name="has_special_needs" value="1" onchange="toggleField('special_needs_details_wrap', this.checked)">
                                                    <label class="form-check-label" for="has_special_needs">Does the pupil have special needs?</label>
                                                </div>
                                                <div id="special_needs_details_wrap" class="mt-2" style="display: none;">
                                                    <textarea name="special_needs_details" class="form-control" rows="2" placeholder="If ticked, provide details here..."></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="attended_special_school" name="attended_special_school" value="1" onchange="toggleField('special_school_details_wrap', this.checked)">
                                                    <label class="form-check-label" for="attended_special_school">Did the pupil attend a special needs school in the past?</label>
                                                </div>
                                                <div id="special_school_details_wrap" class="mt-2" style="display: none;">
                                                    <textarea name="special_school_details" class="form-control" rows="2" placeholder="If ticked, provide details here..."></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="smoking_history" name="smoking_history" value="1">
                                                    <label class="form-check-label" for="smoking_history">Does the pupil have a smoking history?</label>
                                                </div>
                                            </div>
                                            <div class="col-md-12 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" id="drug_abuse_history" name="drug_abuse_history" value="1">
                                                    <label class="form-check-label" for="drug_abuse_history">Does the pupil have a drug abuse history?</label>
                                                </div>
                                            </div>
                                            <div class="col-12 mb-3">
                                                <label class="form-label">Treatment Plan</label>
                                                <textarea name="treatment_plan" class="form-control" rows="4"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingDiagnoses">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseDiagnoses">
                                        <i class="fas fa-stethoscope"></i> Diagnoses
                                    </button>
                                </h2>
                                <div id="collapseDiagnoses" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="diagnosis_rows">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addDiagnosisRow()">+ Add Diagnosis</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingMedications">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedications">
                                        <i class="fas fa-medkit"></i> Medications
                                    </button>
                                </h2>
                                <div id="collapseMedications" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="medication_rows">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addMedicationRow()">+ Add Medication</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingRecords">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRecords">
                                        <i class="fas fa-file-alt"></i> Records
                                    </button>
                                </h2>
                                <div id="collapseRecords" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="record_rows">
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addRecordRow()">+ Add Record</button>
                                    </div>
                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingSafeguardingProbation">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSafeguardingProbation">
                                        <i class="fas fa-shield-alt"></i> Safeguarding & Probation
                                    </button>
                                </h2>
                                <div id="collapseSafeguardingProbation" class="accordion-collapse collapse" data-bs-parent="#onboardingAccordion">
                                    <div class="accordion-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="onboarding_inner_form">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="social_services_involvement" name="social_services_involvement" value="1" onchange="toggleField('social_services_wrap', this.checked)">
                                                        <label class="form-check-label" for="social_services_involvement">Involvement of social services?</label>
                                                    </div>
                                                    <div id="social_services_wrap" class="mt-2" style="display: none;">
                                                        <div id="social_worker_form_container">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="onboarding_inner_form">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" id="probation_officer_required" name="probation_officer_required" value="1" onchange="toggleField('probation_officer_wrap', this.checked)">
                                                        <label class="form-check-label" for="probation_officer_required">Is visiting probation officer required?</label>
                                                    </div>
                                                    <div id="probation_officer_wrap" class="mt-2" style="display: none;">
                                                        <div id="probation_officer_form_container">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>                        
                        <hr>
                        @include('components.attachments_input', ['for_create' => true])
                        <div class="settings_actions">
                            <button type="submit" class="btn btn-success">
                                Onboard Pupil
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-md-4 d-flex flex-column">
                <div class="settings_section" style="background-color: #ffffff91;">
                    <div class="title">
                        <i class="fas fa-info-circle me-2"></i>{{ __('Instructions') }}
                    </div>
                    <div class="description">
                        Use this comprehensive form to register a new pupil into the system. Some of the core information is required, while the additional sections allow you to optionally attach related records immediately.
                    </div>
                    <ul class="small text-muted list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Core Details:</strong> Ensure name, DOB, and address are accurate. These form the primary profile identity.
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Family Members:</strong> You can add multiple parents, guardians, or siblings. Use the dropdown to define their relation. The first family member added will be set as the primary family member (next of kin).
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Medical & Records:</strong> Relevant history, diagnoses, and tracked support records can be logged inline and will instantly populate the pupil's respective data tables.
                        </li>
                        <li>
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>Adding Rows:</strong> You must explicitly click the "+ Add" buttons to reveal the form fields for any optional subsections.
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        window.professionalOptions = `@foreach($professionals as $prof)<option value="{{ $prof->id }}">{{ $prof->first_name }} {{ $prof->last_name }}</option>@endforeach`;
        window.recordTypeOptions = `@foreach($record_types as $type)<option value="{{ $type->id }}">{{ $type->name }}</option>@endforeach`;
    </script>
    <script src="{{ asset('js/onboarding.js') }}"></script>
    <script>
        setupFileExtraction('{{ route("pupils.extract-file") }}', '{{ csrf_token() }}', function(d) {
            function getProfessionalPayload(item) {
                if (!item || typeof item !== 'object') {
                    return null;
                }
                if (item.prof_first_name || item.prof_last_name || item.prof_role || item.prof_agency || item.prof_phone || item.prof_email || item.prof_title) {
                    return item;
                }
                if (item.professional && typeof item.professional === 'object') {
                    return item.professional;
                }
                return null;
            }

            function fillInlineProfessional(prefix, professional) {
                const payload = getProfessionalPayload(professional);
                if (!payload) {
                    return;
                }

                const firstName = payload.prof_first_name || '';
                const lastName = payload.prof_last_name || '';

                const toggleButton = $(`[name="${prefix}[professional_id]"]`).closest('.onboarding_inner_form').find('.toggle_professional_btn').first();

                if (toggleButton.length && !$(`[name="${prefix}[is_new_professional]"]`).length) {
                    toggleButton.trigger('click');
                } else if (toggleButton.length && $(`[name="${prefix}[is_new_professional]"]`).val() !== '1') {
                    toggleButton.trigger('click');
                }

                $(`[name="${prefix}[prof_title]"]`).val(payload.prof_title || '');
                $(`[name="${prefix}[prof_first_name]"]`).val(firstName);
                $(`[name="${prefix}[prof_last_name]"]`).val(lastName);
                $(`[name="${prefix}[prof_role]"]`).val(payload.prof_role || '');
                $(`[name="${prefix}[prof_agency]"]`).val(payload.prof_agency || '');
                $(`[name="${prefix}[prof_phone]"]`).val(payload.prof_phone || '');
                $(`[name="${prefix}[prof_email]"]`).val(payload.prof_email || '');
                $(`[name="${prefix}[is_new_professional]"]`).val('1');
            }

            // core
            if (d.pupil_number) $('input[name="pupil_number"]').val(d.pupil_number);
            if (d.first_name) $('input[name="first_name"]').val(d.first_name);
            if (d.last_name) $('input[name="last_name"]').val(d.last_name);
            if (d.dob) $('input[name="dob"]').val(d.dob);
            if (d.joined_date) $('input[name="joined_date"]').val(d.joined_date);
            if (d.year_group) $('input[name="year_group"]').val(d.year_group);
            if (d.tutor_group) $('input[name="tutor_group"]').val(d.tutor_group);
            if (d.phone) $('input[name="phone"]').val(d.phone);
            if (d.email) $('input[name="email"]').val(d.email);
            if (d.after_school_job) $('input[name="after_school_job"]').val(d.after_school_job);

            if (d.gender) {
                var normalised_gender = d.gender.toString().trim().toLowerCase();
                $('select[name="gender"] option').each(function() {
                    if ($(this).text().trim().toLowerCase() == normalised_gender) {
                        $('select[name="gender"]').val($(this).val());
                        return false;
                    }
                });
            }

            // address
            if (d.address_line_1) $('input[name="address_line_1"]').val(d.address_line_1);
            if (d.address_line_2) $('input[name="address_line_2"]').val(d.address_line_2);
            if (d.locality) $('input[name="locality"]').val(d.locality);
            if (d.postcode) $('input[name="postcode"]').val(d.postcode);
            if (d.country) $('input[name="country"]').val(d.country);
            
            // updates checkboxes and triggers change event for checkboxes that open up other fields
            function checkAndToggle(id, value) {
                if (value !== undefined) {
                    let checked = value == true || value == 1 || value == 'true';
                    let element = $('#' + id);
                    if (element.prop('checked') !== checked) {
                        element.prop('checked', checked).trigger('change');
                    }
                }
            }

            // Family members
            if (Array.isArray(d.family_members) && d.family_members.length > 0) {
                $('#collapseFamilyMembers').collapse('show');
                d.family_members.forEach(item => {
                    addFamilyMemberRow();
                    let idx = familyMemberIdx - 1;
                    if (item.first_name) $(`input[name="family_members[${idx}][first_name]"]`).val(item.first_name);
                    if (item.last_name) $(`input[name="family_members[${idx}][last_name]"]`).val(item.last_name);
                    if (item.relation) $(`input[name="family_members[${idx}][relation]"]`).val(item.relation);
                    if (item.dob) $(`input[name="family_members[${idx}][dob]"]`).val(item.dob);
                    if (item.phone) $(`input[name="family_members[${idx}][phone]"]`).val(item.phone);
                    if (item.email) $(`input[name="family_members[${idx}][email]"]`).val(item.email);
                    if (item.address_line_1) $(`input[name="family_members[${idx}][address_line_1]"]`).val(item.address_line_1);
                    if (item.address_line_2) $(`input[name="family_members[${idx}][address_line_2]"]`).val(item.address_line_2);
                    if (item.locality) $(`input[name="family_members[${idx}][locality]"]`).val(item.locality);
                    if (item.postcode) $(`input[name="family_members[${idx}][postcode]"]`).val(item.postcode);
                    if (item.country) $(`input[name="family_members[${idx}][country]"]`).val(item.country);
                    if (item.marital_status) $(`input[name="family_members[${idx}][marital_status]"]`).val(item.marital_status);
                    if (item.highest_education) $(`input[name="family_members[${idx}][highest_education]"]`).val(item.highest_education);
                    if (item.financial_status) $(`input[name="family_members[${idx}][financial_status]"]`).val(item.financial_status);
                    if (item.occupation) $(`input[name="family_members[${idx}][occupation]"]`).val(item.occupation);
                    if (item.state_support) $(`input[name="family_members[${idx}][state_support]"]`).val(item.state_support);
                });
            }

            // School Histories
            if (Array.isArray(d.school_histories) && d.school_histories.length > 0) {
                $('#collapseSchools').collapse('show');
                d.school_histories.forEach(item => {
                    addSchoolRow();
                    let idx = schoolIdx - 1;
                    if (item.school_name) $(`input[name="school_histories[${idx}][school_name]"]`).val(item.school_name);
                    if (item.school_type) $(`input[name="school_histories[${idx}][school_type]"]`).val(item.school_type);
                    if (item.class_type) $(`input[name="school_histories[${idx}][class_type]"]`).val(item.class_type);
                    if (item.years_attended) $(`input[name="school_histories[${idx}][years_attended]"]`).val(item.years_attended);
                    if (item.transition_reason) $(`input[name="school_histories[${idx}][transition_reason]"]`).val(item.transition_reason);
                });
            }

            // SEN & Background
            if (Object.values(d.sen_and_background || {}).some(v => v)) {
                $('#collapseBackground').collapse('show');

                if (d.sen_and_background.parental_description) $('textarea[name="parental_description"]').val(d.sen_and_background.parental_description);
                if (d.sen_and_background.treatment_plan) $('textarea[name="treatment_plan"]').val(d.sen_and_background.treatment_plan);

                checkAndToggle('has_special_needs', d.sen_and_background.has_special_needs);
                if (d.sen_and_background.special_needs_details) $('textarea[name="special_needs_details"]').val(d.sen_and_background.special_needs_details);
    
                checkAndToggle('attended_special_school', d.sen_and_background.attended_special_school);
                if (d.sen_and_background.special_school_details) $('textarea[name="special_school_details"]').val(d.sen_and_background.special_school_details);

                checkAndToggle('smoking_history', d.sen_and_background.smoking_history);

                checkAndToggle('drug_abuse_history', d.sen_and_background.drug_abuse_history);
            }

            // Diagnoses
            if (Array.isArray(d.diagnoses) && d.diagnoses.length > 0) {
                $('#collapseDiagnoses').collapse('show');
                d.diagnoses.forEach(item => {
                    addDiagnosisRow();
                    let idx = diagnosisIdx - 1;
                    if (item.name) $(`input[name="diagnoses[${idx}][name]"]`).val(item.name);
                    if (item.date) $(`input[name="diagnoses[${idx}][date]"]`).val(item.date);
                    fillInlineProfessional(`diagnoses[${idx}]`, item);
                    if (item.description) $(`textarea[name="diagnoses[${idx}][description]"]`).val(item.description);
                    if (item.recommendations) $(`textarea[name="diagnoses[${idx}][recommendations]"]`).val(item.recommendations);
                });
            }

            // Medications
            if (Array.isArray(d.medications) && d.medications.length > 0) {
                $('#collapseMedications').collapse('show');
                d.medications.forEach(item => {
                    addMedicationRow();
                    let idx = medicationIdx - 1;
                    if (item.name) $(`input[name="medications[${idx}][name]"]`).val(item.name);
                    if (item.dosage) $(`input[name="medications[${idx}][dosage]"]`).val(item.dosage);
                    if (item.frequency) $(`input[name="medications[${idx}][frequency]"]`).val(item.frequency);
                    if (item.time_of_day) $(`input[name="medications[${idx}][time_of_day]"]`).val(item.time_of_day);
                    if (item.administration_method) $(`input[name="medications[${idx}][administration_method]"]`).val(item.administration_method);
                    if (item.start_date) $(`input[name="medications[${idx}][start_date]"]`).val(item.start_date);
                    if (item.end_date) $(`input[name="medications[${idx}][end_date]"]`).val(item.end_date);
                    if (item.expiry_date) $(`input[name="medications[${idx}][expiry_date]"]`).val(item.expiry_date);
                    if (item.storage_instructions) $(`textarea[name="medications[${idx}][storage_instructions]"]`).val(item.storage_instructions);
                    checkAndToggle(`self_administer_${idx}`, item.self_administer);
                });
            }

            // Records
            if (Array.isArray(d.records) && d.records.length > 0) {
                $('#collapseRecords').collapse('show');
                d.records.forEach(item => {
                    addRecordRow();
                    let idx = recordIdx - 1;
                    if (item.title) $(`input[name="records[${idx}][title]"]`).val(item.title);
                    if (item.date) $(`input[name="records[${idx}][date]"]`).val(item.date);
                    if (item.reference_number) $(`input[name="records[${idx}][reference_number]"]`).val(item.reference_number);
                    fillInlineProfessional(`records[${idx}]`, item);
                    if (item.description) $(`textarea[name="records[${idx}][description]"]`).val(item.description);
                    if (item.outcome) $(`input[name="records[${idx}][outcome]"]`).val(item.outcome);
                    if (item.record_type) {
                        var normalisedType = item.record_type.toString().trim().toLowerCase();
                        $(`select[name="records[${idx}][record_type_id]"] option`).each(function() {
                            if ($(this).text().trim().toLowerCase() === normalisedType) {
                                $(`select[name="records[${idx}][record_type_id]"]`).val($(this).val());
                                return false;
                            }
                        });
                    }
                });
            }

            // Safeguarding & Probation
            if (Object.values(d.safeguarding_and_probation || {}).some(v => v)) {
                $('#collapseSafeguardingProbation').collapse('show');

                checkAndToggle('social_services_involvement', d.safeguarding_and_probation.social_services_involvement);
                checkAndToggle('probation_officer_required', d.safeguarding_and_probation.probation_officer_required);
                fillInlineProfessional('social_worker', d.safeguarding_and_probation.social_worker || d.social_worker);
                fillInlineProfessional('probation_officer', d.safeguarding_and_probation.probation_officer || d.probation_officer);
            }
        });
    </script>
@endpush

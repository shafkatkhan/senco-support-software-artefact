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
                    <form action="{{ route('pupils.store') }}" method="POST">
                        @csrf
                        <div class="title">
                            <i class="fas fa-id-card me-2"></i>Core Pupil Information
                        </div>
                        <div class="description">
                            Enter the primary details for the pupil.
                        </div>
                        <div class="row">
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
                                <select name="gender" class="form-control" required>
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
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Initial Tutor Group</label>
                                <input type="text" name="initial_tutor_group" class="form-control" value="{{ old('initial_tutor_group') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                            </div>
                            <div class="col-md-4 mb-3">
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
                        <div id="onboardingAccordion" class="accordion" >
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
@endpush

@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.show', $pupil->id) }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupil details
        </div>
        <div class="row settings_wrap onboarding_wrap">
            <div class="settings_section">
                <div class="title">
                    <i class="fas fa-user-edit me-2"></i>Edit Pupil
                </div>
                <div class="description">
                    Update the pupil's profile fields. Related records such as medications, diagnoses, family members, and meetings are managed separately via the top navigation bar.
                </div>
                <hr>
                <form action="{{ route('pupils.update', $pupil->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="title">
                        <i class="fas fa-id-card me-2"></i>Core Pupil Information
                    </div>
                    <div class="description">
                        Update the primary details for the pupil.
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pupil Number <span class="text-danger">*</span></label>
                            <input type="text" name="pupil_number" class="form-control" value="{{ old('pupil_number', $pupil->pupil_number) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $pupil->first_name) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $pupil->last_name) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <input type="date" name="dob" class="form-control" value="{{ old('dob', optional($pupil->dob)->format('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Gender <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="" disabled {{ old('gender', $pupil->gender) ? '' : 'selected' }}></option>
                                <option value="Male" {{ old('gender', $pupil->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('gender', $pupil->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('gender', $pupil->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Joined Date</label>
                            <input type="date" name="joined_date" class="form-control" value="{{ old('joined_date', optional($pupil->joined_date)->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Initial Tutor Group</label>
                            <input type="text" name="initial_tutor_group" class="form-control" value="{{ old('initial_tutor_group', $pupil->initial_tutor_group) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $pupil->phone) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $pupil->email) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">After School Job</label>
                            <input type="text" name="after_school_job" class="form-control" value="{{ old('after_school_job', $pupil->after_school_job) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Next of Kin</label>
                            <select name="primary_family_member_id" class="form-control">
                                <option value="">None / Not Set</option>
                                @foreach($pupil->familyMembers as $familyMember)
                                    <option value="{{ $familyMember->id }}" {{ (string) old('primary_family_member_id', $pupil->primary_family_member_id) === (string) $familyMember->id ? 'selected' : '' }}>
                                        {{ $familyMember->first_name }} {{ $familyMember->last_name }}{{ $familyMember->relation ? ' (' . $familyMember->relation . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address Line 1</label>
                            <input type="text" name="address_line_1" class="form-control" value="{{ old('address_line_1', $pupil->address_line_1) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Address Line 2</label>
                            <input type="text" name="address_line_2" class="form-control" value="{{ old('address_line_2', $pupil->address_line_2) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Town/City</label>
                            <input type="text" name="locality" class="form-control" value="{{ old('locality', $pupil->locality) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Postcode</label>
                            <input type="text" name="postcode" class="form-control" value="{{ old('postcode', $pupil->postcode) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Country</label>
                            <input type="text" name="country" class="form-control" value="{{ old('country', $pupil->country) }}">
                        </div>
                    </div>
                    <hr>
                    <div class="title" style="margin-bottom: 25px;">
                        <i class="fas fa-wheelchair me-2"></i>SEN and Background
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="form-label">Description of student by parents (or legal guardian)</label>
                            <textarea name="parental_description" class="form-control" rows="4">{{ old('parental_description', $pupil->parental_description) }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="has_special_needs" name="has_special_needs" value="1" onchange="toggleField('special_needs_details_wrap', this.checked)" {{ old('has_special_needs', $pupil->has_special_needs) ? 'checked' : '' }}>
                                <label class="form-check-label" for="has_special_needs">Does the pupil have special needs?</label>
                            </div>
                            <div id="special_needs_details_wrap" class="mt-2" style="display: none;">
                                <textarea name="special_needs_details" class="form-control" rows="2" placeholder="If ticked, provide details here...">{{ old('special_needs_details', $pupil->special_needs_details) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="attended_special_school" name="attended_special_school" value="1" onchange="toggleField('special_school_details_wrap', this.checked)" {{ old('attended_special_school', $pupil->attended_special_school) ? 'checked' : '' }}>
                                <label class="form-check-label" for="attended_special_school">Did the pupil attend a special needs school in the past?</label>
                            </div>
                            <div id="special_school_details_wrap" class="mt-2" style="display: none;">
                                <textarea name="special_school_details" class="form-control" rows="2" placeholder="If ticked, provide details here...">{{ old('special_school_details', $pupil->special_school_details) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="smoking_history" name="smoking_history" value="1" {{ old('smoking_history', $pupil->smoking_history) ? 'checked' : '' }}>
                                <label class="form-check-label" for="smoking_history">Does the pupil have a smoking history?</label>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="drug_abuse_history" name="drug_abuse_history" value="1" {{ old('drug_abuse_history', $pupil->drug_abuse_history) ? 'checked' : '' }}>
                                <label class="form-check-label" for="drug_abuse_history">Does the pupil have a drug abuse history?</label>
                            </div>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Treatment Plan</label>
                            <textarea name="treatment_plan" class="form-control" rows="4">{{ old('treatment_plan', $pupil->treatment_plan) }}</textarea>
                        </div>
                    </div>
                    <hr>
                    <div class="title" style="margin-bottom: 25px;">
                        <i class="fas fa-shield-alt me-2"></i>Safeguarding and Probation
                    </div>
                    <div class="row">
                        <div class="col-md-12 mb-3 onboarding_inner_form">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="social_services_involvement" name="social_services_involvement" value="1" onchange="toggleField('social_services_wrap', this.checked)" {{ old('social_services_involvement', $pupil->social_services_involvement) ? 'checked' : '' }}>
                                <label class="form-check-label" for="social_services_involvement">Involvement of social services?</label>
                            </div>
                            <div id="social_services_wrap" class="mt-2" style="display: none;">
                                <label class="form-label">Social Worker</label>
                                <select name="social_services_professional_id" class="form-control">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($professionals as $professional)
                                        <option value="{{ $professional->id }}" {{ (string) old('social_services_professional_id', $pupil->social_services_professional_id) === (string) $professional->id ? 'selected' : '' }}>
                                            {{ trim($professional->title . ' ' . $professional->first_name . ' ' . $professional->last_name) }}{{ $professional->role ? ' (' . $professional->role . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 mb-3 onboarding_inner_form">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="probation_officer_required" name="probation_officer_required" value="1" onchange="toggleField('probation_officer_wrap', this.checked)" {{ old('probation_officer_required', $pupil->probation_officer_required) ? 'checked' : '' }}>
                                <label class="form-check-label" for="probation_officer_required">Is visiting probation officer required?</label>
                            </div>
                            <div id="probation_officer_wrap" class="mt-2" style="display: none;">
                                <label class="form-label">Probation Officer</label>
                                <select name="probation_officer_professional_id" class="form-control">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($professionals as $professional)
                                        <option value="{{ $professional->id }}" {{ (string) old('probation_officer_professional_id', $pupil->probation_officer_professional_id) === (string) $professional->id ? 'selected' : '' }}>
                                            {{ trim($professional->title . ' ' . $professional->first_name . ' ' . $professional->last_name) }}{{ $professional->role ? ' (' . $professional->role . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="settings_actions">
                        <button type="submit" class="btn btn-success">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/onboarding.js') }}"></script>        
    <script>
        toggleField('special_needs_details_wrap', document.getElementById('has_special_needs').checked);
        toggleField('special_school_details_wrap', document.getElementById('attended_special_school').checked);
        toggleField('social_services_wrap', document.getElementById('social_services_involvement').checked);
        toggleField('probation_officer_wrap', document.getElementById('probation_officer_required').checked);
    </script>
@endpush

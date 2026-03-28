@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> {{ __('Return back to pupils') }}
        </div>
        <div class="row settings_wrap dashboard">
            <div class="col-md-5 d-flex flex-column dashboard_section">
                <div class="settings_section">
                    <div class="title">
                        <i class="fas fa-user"></i>{{ __('Personal Details') }}
                    </div>
                    <div class="description">
                        {{ __('Basic information of the pupil.') }}
                    </div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-id-badge"></i>
                            {{ __('Pupil No.') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->pupil_number }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-calendar-alt"></i>
                            {{ __('Date of Birth') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->dob->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-venus-mars"></i>
                            {{ __('Gender') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->gender }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-briefcase"></i>
                            {{ __('After School Job') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->after_school_job ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-graduation-cap"></i>
                            {{ __('Major') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->major->name ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-house-chimney-user"></i>
                            {{ __('Next of Kin') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->primaryFamilyMember ? $pupil->primaryFamilyMember->first_name . ' ' . $pupil->primaryFamilyMember->last_name : '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                </div>
                <div class="settings_section">
                    <div class="title">
                        <i class="fas fa-envelope"></i>{{ __('Contact Information') }}
                    </div>
                    <div class="description">
                        {{ __('Contact information for the pupil.') }}
                    </div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-phone"></i>
                            {{ __('Phone') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->phone ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-envelope"></i>
                            {{ __('Email') }}:
                        </div>
                        <div class="value">
                            @if($pupil->email)
                                <a href="mailto:{{ $pupil->email }}" class="nice_link">{{ $pupil->email }}</a>
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-map-location-dot"></i>
                            {{ __('Address') }}:
                        </div>
                        <div class="value">
                            {!! collect([$pupil->address_line_1, $pupil->address_line_2])->filter()->implode(', <br>') ?: '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-city"></i>
                            {{ __('Town/City') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->locality ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ __('Postcode') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->postcode ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-earth-europe"></i>
                            {{ __('Country') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->country ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                </div>
                <div class="settings_section" style="grid-template-columns: clamp(130px, 50%, 260px) minmax(0, 1fr);">
                    <div class="title">
                        <i class="fa-solid fa-shield-heart"></i>{{ __('Safeguarding and Needs') }}
                    </div>
                    <div class="description">
                        {{ __('Information about the pupil\'s welfare, support needs, and any involvement with external safeguarding or care services.') }}
                    </div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-smoking"></i>
                            {{ __('Smoking History?') }}
                        </div>
                        <div class="value">
                            {{ $pupil->smoking_history ? __('Yes') : __('No') }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-cannabis"></i>
                            {{ __('Drug Abuse History?') }}
                        </div>
                        <div class="value">
                            {{ $pupil->drug_abuse_history ? __('Yes') : __('No') }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-wheelchair"></i>
                            {{ __('Special Needs') }}:
                        </div>
                        <div class="value">
                            @if($pupil->has_special_needs)
                                {!! $pupil->special_needs_details ? nl2br(e($pupil->special_needs_details)) : __('Yes') !!}
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-school"></i>
                            {{ __('Attended Special Needs School in Past') }}:
                        </div>
                        <div class="value">
                            @if($pupil->attended_special_school)
                                {!! $pupil->special_school_details ? nl2br(e($pupil->special_school_details)) : __('Yes') !!}
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-users"></i>
                            {{ __('Social Services Involvement') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->social_services_involvement ? __('Yes') : __('No') }}
                        </div>
                    </div>
                    @if($pupil->social_services_involvement)
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-group"></i>
                            {{ __('Social Worker') }}:
                        </div>
                        <div class="value">
                            @if($pupil->socialServicesProfessional)
                                {{ collect([
                                    $pupil->socialServicesProfessional->title,
                                    $pupil->socialServicesProfessional->first_name,
                                    $pupil->socialServicesProfessional->last_name,
                                ])->filter()->implode(' ') }}
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-shield"></i>
                            {{ __('Visiting Probation Officer Required') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->probation_officer_required ? __('Yes') : __('No') }}
                        </div>
                    </div>
                    @if($pupil->probation_officer_required)
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-tie"></i>
                            {{ __('Probation Officer') }}:
                        </div>
                        <div class="value">
                            @if($pupil->probationOfficerProfessional)
                                {{ collect([
                                    $pupil->probationOfficerProfessional->title,
                                    $pupil->probationOfficerProfessional->first_name,
                                    $pupil->probationOfficerProfessional->last_name,
                                ])->filter()->implode(' ') }}
                            @else
                                <span class="text-muted">{{ __('N/A') }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                <div class="settings_section">
                    <div class="title">
                        <i class="fas fa-envelope"></i>{{ __('Administrative Information') }}
                    </div>
                    <div class="description">
                        {{ __('Administrative information for the pupil.') }}
                    </div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-calendar-alt"></i>
                            {{ __('Joined Date') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->joined_date?->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-group"></i>
                            {{ __('Initial Tutor Group') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->initial_tutor_group ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-people-group"></i>
                            {{ __('Current Year Group') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->current_year_group ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-group"></i>
                            {{ __('Current Tutor Group') }}:
                        </div>
                        <div class="value">
                            {!! $pupil->current_tutor_group ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-user-circle"></i>
                            {{ __('Onboarded by') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->onboardedBy->first_name }} {{ $pupil->onboardedBy->last_name }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-clock"></i>
                            {{ __('Last Edited') }}:
                        </div>
                        <div class="value">
                            {{ $pupil->updated_at->format('d/m/Y') }},                             
                            {{ $pupil->updated_at->format('H:i') }}
                        </div>
                    </div>  
                </div>
            </div>
            <div class="col-md-7 d-flex flex-column dashboard_section">
                <div class="settings_section" style="display: flex; align-items: center; justify-content: space-between;">
                    <div class="title" style="margin-bottom: 0px;">
                        <i class="fa-solid fa-gear"></i>{{ __('Actions') }}
                    </div>
                    <div class="sen_icon_wrap">
                        @can('export-pupil-data')
                        <a href="{{ route('pupils.export', $pupil->id) }}" class="sen_icon download_icon button_styled" style="width: auto; padding: 10px 15px;">
                            <i class="fa-solid fa-file-pdf" style="margin-right: 5px;"></i>
                            {{ __('Export') }}
                        </a>
                        @endcan
                        @can('edit-pupils')
                        <a href="{{ route('pupils.edit', $pupil->id) }}" class="sen_icon sen_edit_icon button_styled">
                            <i class="far fa-edit"></i>
                        </a>
                        @endcan
                        @can('delete-pupils')
                        <button class="sen_icon sen_delete_icon delete_icon button_styled"
                            data-bs-toggle="modal"
                            data-bs-target="#delete"
                            data-url="{{ route('pupils.destroy', $pupil->id) }}"
                            data-name="{{ $pupil->first_name }} {{ $pupil->last_name }}">
                            <i class="far fa-trash-alt"></i>
                        </button>
                        @endcan
                    </div>
                </div>
                <div class="related_professionals">
                    <div class="table_title">
                        {{ __('Related Professionals') }}
                    </div>
                    <table class="table sen_table-striped no-datatable-filters">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Role') }}</th>
                                <th>{{ __('Involvement') }}</th>
                                <th>{{ __('Contact') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($involvements as $entry)
                                <tr>
                                    <td>{{ $entry['professional']->title }} {{ $entry['professional']->first_name }} {{ $entry['professional']->last_name }}</td>
                                    <td>{{ $entry['professional']->role }}</td>
                                    <td>
                                        @foreach($entry['involvements'] as $item)
                                            <span class="badge bg-secondary">{{ $item }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($entry['professional']->phone) {{ $entry['professional']->phone }} <br> @endif
                                        @if($entry['professional']->email) {{ $entry['professional']->email }} @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="empty_table_message">{{ __('No related professionals found for :name', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="settings_section">
                    <div class="title">
                        <i class="fa-solid fa-comment-dots"></i>{{ __('Parental Description of Student') }}
                    </div>
                    <div class="description">
                        {{ __('Description of the pupil provided by their parents or legal guardians.') }}
                    </div>
                    <div class="big_text">
                        {!! $pupil->parental_description ? nl2br(e($pupil->parental_description)) : '<span class="text-muted">'.__('N/A').'</span>' !!}
                    </div>
                </div>
                <div class="settings_section">
                    <div class="title">
                        <i class="fa-solid fa-table-list"></i>{{ __('Data Summary') }}
                    </div>
                    <div class="description">
                        {{ __('Summary of related pupil data.') }}
                    </div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-pills"></i>
                            {{ __('Medications') }}:
                        </div>
                        <div class="value">
                            <div class="label_cards">
                                @forelse ($pupil->medications as $medication)
                                    <div>{{ $medication->name }}</div>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-diagnoses"></i>
                            {{ __('Diagnoses') }}:
                        </div>
                        <div class="value">
                            <div class="label_cards">
                                @forelse ($pupil->diagnoses as $diagnosis)
                                    <div>{{ $diagnosis->name }}</div>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-book"></i>
                            {{ __('Subjects') }}:
                        </div>
                        <div class="value">
                            <div class="label_cards">
                                @forelse ($pupil->diets as $diet)
                                    <div>{{ $diet->subject->name }}</div>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
                <div class="settings_section" style="display: block;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <div class="title">
                                <i class="fas fa-notes-medical"></i>{{ __('Treatment Plan') }}
                            </div>
                            <div class="description">
                                {{ __('The pupil\'s treatment plan, together with any follow-up updates.') }}
                            </div>
                        </div>
                        @can('edit-pupils')
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addTreatmentPlanUpdateModal">
                            <i class="fas fa-plus"></i> {{ __('Add Update') }}
                        </button>
                        @endcan
                    </div>
                    <div class="big_text">
                        {!! $pupil->treatment_plan ? nl2br(e($pupil->treatment_plan)) : '<span class="text-muted">'.__('No primary treatment plan specified.').'</span>' !!}
                    </div>
                    @if($pupil->treatmentPlanUpdates->isNotEmpty())
                        <div class="dashboard_item_divider" style="margin: 15px 0px;"></div>
                        <div class="title_label">
                            {{ __('Updates') }}:
                        </div>                        
                        <div class="treatment_plan_updates">
                            @foreach($pupil->treatmentPlanUpdates as $update)
                                @if($loop->iteration == 4)
                                    <div id="hidden_treatment_plan_updates">
                                        <div class="fade"></div>
                                @endif
                                
                                <div class="treatment_plan_update {{ $loop->iteration > 3 ? 'hidden_update' : '' }}">
                                    <div class="meta">
                                        <div><i class="far fa-calendar-alt"></i> {{ $update->date->format('d/m/Y') }}</div>
                                        <div><i class="far fa-user"></i> {{ $update->user->first_name . ' ' . $update->user->last_name }}</div>
                                    </div>
                                    <div class="content">
                                        {!! nl2br(e($update->description)) !!}
                                    </div>
                                </div>
                                
                                @if($loop->last && $pupil->treatmentPlanUpdates->count() > 3)
                                    </div>
                                @endif
                            @endforeach
                            @if($pupil->treatmentPlanUpdates->count() > 3)
                                <input type="hidden" id="treatment_plan_updates_count" value="{{ $pupil->treatmentPlanUpdates->count() }}">
                                <button type="button" class="btn btn-link p-0 mt-2" id="toggle_treatment_plan_updates_btn" style="text-decoration: none; font-size: 14px; font-weight: 500;">
                                    {{ __('See all :count updates', ['count' => $pupil->treatmentPlanUpdates->count()]) }} 
                                    <i class="fas fa-chevron-down ms-1"></i>
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>            
		</div>
    </section>

    @can('delete-pupils')
    @include('components.delete_modal', ['type' => 'Pupil'])
    @endcan

    @can('edit-pupils')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])

    <div class="modal fade" id="addTreatmentPlanUpdateModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('pupils.treatment_plan_updates.store', $pupil->id) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Add Treatment Plan Update') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Date') }}</label>
                            <input type="date" class="form-control" id="update_date" name="date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Update Content') }}</label>
                            <textarea class="form-control" id="update_description" name="description" rows="5" placeholder="{{ __('Add follow up details...') }}" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Add Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan
@endsection

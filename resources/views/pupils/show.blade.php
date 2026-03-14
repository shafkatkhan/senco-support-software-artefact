@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
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
                            Pupil ID:
                        </div>
                        <div class="value">
                            {{ $pupil->id }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-calendar-alt"></i>
                            Date of Birth:
                        </div>
                        <div class="value">
                            {{ $pupil->dob->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-regular fa-user"></i>
                            Gender:
                        </div>
                        <div class="value">
                            {{ $pupil->gender }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-briefcase"></i>
                            After School Job:
                        </div>
                        <div class="value">
                            {{ $pupil->after_school_job ?? 'N/A' }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-house-chimney-user"></i>
                            Next of Kin:
                        </div>
                        <div class="value">
                            {{ $pupil->primaryFamilyMember ? $pupil->primaryFamilyMember->first_name . ' ' . $pupil->primaryFamilyMember->last_name : 'N/A' }}
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
                            Phone:
                        </div>
                        <div class="value">
                            {!! $pupil->phone ?? '<span class="text-muted">N/A</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-envelope"></i>
                            Email:
                        </div>
                        <div class="value">
                            @if($pupil->email)
                                <a href="mailto:{{ $pupil->email }}" class="nice_link">{{ $pupil->email }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-map-location-dot"></i>
                            Address:
                        </div>
                        <div class="value">
                            {!! collect([$pupil->address_line_1, $pupil->address_line_2])->filter()->implode(', <br>') ?: 'N/A' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-city"></i>
                            Town/City:
                        </div>
                        <div class="value">
                            {!! $pupil->locality ?? '<span class="text-muted">N/A</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-map-marker-alt"></i>
                            Postcode:
                        </div>
                        <div class="value">
                            {!! $pupil->postcode ?? '<span class="text-muted">N/A</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-earth-europe"></i>
                            Country:
                        </div>
                        <div class="value">
                            {!! $pupil->country ?? '<span class="text-muted">N/A</span>' !!}
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
                            Smoking History?
                        </div>
                        <div class="value">
                            {{ $pupil->smoking_history ? 'Yes' : 'No' }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-cannabis"></i>
                            Drug Abuse History?
                        </div>
                        <div class="value">
                            {{ $pupil->drug_abuse_history ? 'Yes' : 'No' }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-wheelchair"></i>
                            Special Needs:
                        </div>
                        <div class="value">
                            @if($pupil->has_special_needs)
                                {!! $pupil->special_needs_details ? nl2br(e($pupil->special_needs_details)) : 'Yes' !!}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-school"></i>
                            Attended Special Needs School in Past:
                        </div>
                        <div class="value">
                            @if($pupil->attended_special_school)
                                {!! $pupil->special_school_details ? nl2br(e($pupil->special_school_details)) : 'Yes' !!}
                            @else
                                N/A
                            @endif
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-users"></i>
                            Social Services Involvement:
                        </div>
                        <div class="value">
                            {{ $pupil->social_services_involvement ? 'Yes' : 'No' }}
                        </div>
                    </div>
                    @if($pupil->social_services_involvement)
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-group"></i>
                            Social Worker:
                        </div>
                        <div class="value">
                            @if($pupil->socialServicesProfessional)
                                {{ collect([
                                    $pupil->socialServicesProfessional->title,
                                    $pupil->socialServicesProfessional->first_name,
                                    $pupil->socialServicesProfessional->last_name,
                                ])->filter()->implode(' ') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    @endif
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-shield"></i>
                            Visiting Probation Officer Required:
                        </div>
                        <div class="value">
                            {{ $pupil->probation_officer_required ? 'Yes' : 'No' }}
                        </div>
                    </div>
                    @if($pupil->probation_officer_required)
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-tie"></i>
                            Probation Officer:
                        </div>
                        <div class="value">
                            @if($pupil->probationOfficerProfessional)
                                {{ collect([
                                    $pupil->probationOfficerProfessional->title,
                                    $pupil->probationOfficerProfessional->first_name,
                                    $pupil->probationOfficerProfessional->last_name,
                                ])->filter()->implode(' ') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-7 d-flex flex-column dashboard_section">
                <div class="settings_section" style="display: flex; align-items: center; justify-content: space-between;">
                    <div class="title" style="margin-bottom: 0px;">
                        <i class="fa-solid fa-gear"></i>{{ __('Actions') }}
                    </div>
                    <div class="sen_icon_wrap">
                        @can('edit-pupils')
                        <button class="sen_icon sen_edit_icon button_styled">
                            <i class="far fa-edit"></i>
                        </button>
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
                        Related Professionals
                    </div>
                    <table class="table sen_table-striped no-datatable-filters">
                        <thead class="thead-light">
                            <tr>
                                <th>Name</th>
                                <th>Role</th>
                                <th>Involvement</th>
                                <th>Contact</th>
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
                                    <td colspan="4" class="empty_table_message">No related professionals found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
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
                    <div class="parental_description">
                        {!! $pupil->parental_description ? nl2br(e($pupil->parental_description)) : '<span class="text-muted">N/A</span>' !!}
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
                            Medications:
                        </div>
                        <div class="value">
                            <div class="label_cards">
                                @forelse ($pupil->medications as $medication)
                                    <div>{{ $medication->name }}</div>
                                @empty
                                    <span class="text-muted">N/A</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-diagnoses"></i>
                            Diagnoses:
                        </div>
                        <div class="value">
                            <div class="label_cards">
                                @forelse ($pupil->diagnoses as $diagnosis)
                                    <div>{{ $diagnosis->name }}</div>
                                @empty
                                    <span class="text-muted">N/A</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fas fa-book"></i>
                            Subjects:
                        </div>
                        <div class="value">
                            <div class="label_cards">
                                @forelse ($pupil->diets as $diet)
                                    <div>{{ $diet->subject->name }}</div>
                                @empty
                                    <span class="text-muted">N/A</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
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
                            Joined Date:
                        </div>
                        <div class="value">
                            {{ $pupil->joined_date?->format('d/m/Y') }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="fa-solid fa-user-group"></i>
                            Initial Tutor Group:
                        </div>
                        <div class="value">
                            {!! $pupil->initial_tutor_group ?? '<span class="text-muted">N/A</span>' !!}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-user-circle"></i>
                            Onboarded by:
                        </div>
                        <div class="value">
                            {{ $pupil->onboardedBy->first_name }} {{ $pupil->onboardedBy->last_name }}
                        </div>
                    </div>
                    <div class="dashboard_item_divider"></div>
                    <div class="item">
                        <div class="label">
                            <i class="far fa-clock"></i>
                            Last Edited:
                        </div>
                        <div class="value">
                            {{ $pupil->updated_at->format('d/m/Y') }},                             
                            {{ $pupil->updated_at->format('H:i') }}
                        </div>
                    </div>  
                </div>
            </div>            
		</div>
    </section>

    @can('delete-pupils')
    @include('components.delete_modal', ['type' => 'Pupil'])
    @endcan

    @can('edit-pupils')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])
    @endcan
@endsection

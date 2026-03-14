@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
        </div>
        <div class="dashboard">
            <div class="sen_cards">
                <div class="sen_card" style="flex-grow: 1;">
                    <div class="top">
                        <div class="label">
                            {{ $pupil->first_name }} {{ $pupil->last_name }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-pupils')
                            <button class="sen_icon sen_edit_icon button_styled">
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-pupils')
                            <button class="sen_icon sen_delete_icon button_styled">
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-3 border_right-md">
                                <div class="label">DOB:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $pupil->dob->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-3 border_right-md">
                                <div class="label">Gender:</div>
                                <div class="value">
                                    {{ $pupil->gender }}
                                </div>
                            </div>
                            <div class="item col-md-3 border_right-md">
                                <div class="label">Joined Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $pupil->joined_date?->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-3">
                                <div class="label">Initial Tutor Group:</div>
                                <div class="value">
                                    {{ $pupil->initial_tutor_group ?? 'N/A' }}
                                </div>
                            </div>                        
                            <hr>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">Phone:</div>
                                <div class="value">
                                    <i class="fas fa-phone"></i>
                                    {{ $pupil->phone ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">Email:</div>
                                <div class="value">
                                    <i class="fas fa-envelope"></i>
                                    @if($pupil->email)
                                        <a href="mailto:{{ $pupil->email }}" class="nice_link">{{ $pupil->email }}</a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <div class="item col-md-4">
                                <div class="label">After School Job:</div>
                                <div class="value">
                                    {{ $pupil->after_school_job ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Medications:</div>
                                <div class="value">
                                    <div class="label_cards">
                                        @forelse ($pupil->medications as $medication)
                                            <div>{{ $medication->name }}</div>
                                        @empty
                                            N/A
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Diagnoses:</div>
                                <div class="value">
                                    <div class="label_cards">
                                        @forelse ($pupil->diagnoses as $diagnosis)
                                            <div>{{ $diagnosis->name }}</div>
                                        @empty
                                            N/A
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Smoking History?</div>
                                <div class="value">
                                    {{ $pupil->smoking_history ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Drug Abuse History?</div>
                                <div class="value">
                                    {{ $pupil->drug_abuse_history ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Special Needs:</div>
                                <div class="value">
                                    @if($pupil->has_special_needs)
                                        {!! $pupil->special_needs_details ? nl2br(e($pupil->special_needs_details)) : 'Yes' !!}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Attended Special Needs School in Past:</div>
                                <div class="value">
                                    @if($pupil->attended_special_school)
                                        {!! $pupil->special_school_details ? nl2br(e($pupil->special_school_details)) : 'Yes' !!}
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Parental Description of Student:</div>
                                <div class="value">
                                    {!! $pupil->parental_description ? nl2br(e($pupil->parental_description)) : 'N/A' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Social Services Involvement:</div>
                                <div class="value">
                                    {{ $pupil->social_services_involvement ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Visiting Probation Officer Required:</div>
                                <div class="value">
                                    {{ $pupil->probation_officer_required ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Next of Kin:</div>
                                <div class="value">
                                    {{ $pupil->primaryFamilyMember ? $pupil->primaryFamilyMember->first_name . ' ' . $pupil->primaryFamilyMember->last_name : 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Address:</div>
                                <div class="value">
                                    {!! collect([$pupil->address_line_1, $pupil->address_line_2, $pupil->locality, $pupil->postcode, $pupil->country])->filter()->implode(', <br>') ?: 'N/A' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Onboarded by:</div>
                                <div class="value">
                                    <i class="far fa-user-circle"></i>
                                    {{ $pupil->onboardedBy->first_name }} {{ $pupil->onboardedBy->last_name }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $pupil->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $pupil->updated_at->format('H:i') }}
                                </div>
                            </div>                        
                            @include('components.attachments_list', ['attachments' => $pupil->attachments, 'card' => true, 'delete_permission' => 'edit-pupils',])
                        </div>
                    </div>
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
		</div>
    </section>

    @can('edit-pupils')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])
    @endcan
@endsection
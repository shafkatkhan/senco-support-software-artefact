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
                                    {{ $pupil->joined_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-3">
                                <div class="label">Initial Tutor Group:</div>
                                <div class="value">
                                    {{ $pupil->initial_tutor_group }}
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
                                <div class="label">Next of Kin:</div>
                                <div class="value">
                                    {{ $pupil->primaryFamilyMember ? $pupil->primaryFamilyMember->first_name . ' ' . $pupil->primaryFamilyMember->last_name : 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Address:</div>
                                <div class="value">
                                    {{ $pupil->address_line_1 }},<br>
                                    {{ $pupil->address_line_2 }},<br>
                                    {{ $pupil->locality }},<br>
                                    {{ $pupil->postcode }},<br>
                                    {{ $pupil->country }}
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
@endsection
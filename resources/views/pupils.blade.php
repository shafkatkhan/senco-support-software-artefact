@extends('layouts.app')

@section('content')
    <section id="content">
        <div style="display: flex; gap: 10px; margin-bottom: 20px; justify-content: flex-end;">
            <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                View More Information
            </button>
            <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                Onboard Pupil
            </button>
        </div>

        <div id="pupilsGrid" class="sen_cards" style="display: none;">
            @foreach($pupils as $pupil)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $pupil->first_name }} {{ $pupil->last_name }}
                        </div>
                        <div class="sen_icon_wrap">
                            <a href="{{ route('pupils.show', $pupil->id) }}" class="more_details button_styled">
                                More Details
                            </a>
                            <button class="sen_icon sen_edit_icon button_styled">
                                <i class="far fa-edit"></i>
                            </button>
                            <button class="sen_icon sen_delete_icon button_styled">
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">DOB:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $pupil->dob->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Gender:</div>
                                <div class="value">
                                    {{ $pupil->gender }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Joined Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $pupil->joined_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Initial Tutor Group:</div>
                                <div class="value">
                                    {{ $pupil->initial_tutor_group }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-12">
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
                            <hr>
                            <div class="item col-12">
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
            @endforeach
        </div>

        <div id="pupilsTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Date of Birth</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pupils as $pupil)
                        <tr>
                            <th scope="row">{{ $pupil->id }}</th>
                            <td>{{ $pupil->first_name }} {{ $pupil->last_name }}</td>
                            <td>{{ $pupil->dob->format('d/m/Y') }}</td>
                            <td>{{ $pupil->gender }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon"><i class="fa fa-edit"></i></button>
                                <button class="icon delete_icon"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>    
@endsection
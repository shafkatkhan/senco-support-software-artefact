@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas fa-arrow-circle-left"></i></a> Return back to pupils
        </div>
        <div class="pupils">
            <div class="pupil">
                <div class="top">
                    <div class="label">
                        {{ $pupil->first_name }} {{ $pupil->last_name }}
                    </div>
                    <div class="sen_icon_wrap">
                        <button class="sen_icon sen_edit_icon">
                            <i class="far fa-edit"></i>
                        </button>
                        <button class="sen_icon sen_delete_icon">
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
                                {{ $pupil->primaryFamilyMember->first_name }} {{ $pupil->primaryFamilyMember->last_name }}
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
    </section>
@endsection
@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                View More Information
            </button>
            @can('create-pupils')
            <a href="{{ route('pupils.create') }}" class="new_button">
                Onboard Pupil
            </a>
            @endcan
        </div>

        <div id="pupilsGrid" class="sen_cards" style="display: none;">
            @forelse($pupils as $pupil)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $pupil->first_name }} {{ $pupil->last_name }}
                        </div>
                        <div class="sen_icon_wrap">
                            <a href="{{ route('pupils.show', $pupil->id) }}" class="more_details button_styled">
                                More Details
                            </a>
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
                                    {{ $pupil->joined_date?->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Initial Tutor Group:</div>
                                <div class="value">
                                    {{ $pupil->initial_tutor_group ?? 'N/A' }}
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
                                <div class="label">Phone:</div>
                                <div class="value">
                                    <i class="fas fa-phone"></i>
                                    {{ $pupil->phone ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
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
                            <hr>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">Special Needs?</div>
                                <div class="value">
                                    {{ $pupil->has_special_needs ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">Social Services?</div>
                                <div class="value">
                                    {{ $pupil->social_services_involvement ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <div class="item col-md-4">
                                <div class="label">Visiting Probation Officer Required?</div>
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
            @empty
                <div class="empty_grid_message">No pupils found.</div>
            @endforelse
        </div>

        <div id="pupilsTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Date of Birth</th>
                        <th scope="col">Gender</th>
                        <th scope="col">Special Needs</th>
                        <th scope="col">Attachments</th>
                        @canany(['edit-pupils', 'delete-pupils'])
                        <th scope="col">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupils as $pupil)
                        <tr>
                            <th scope="row">{{ $pupil->id }}</th>
                            <td>{{ $pupil->first_name }} {{ $pupil->last_name }}</td>
                            <td data-order="{{ optional($pupil->dob)->format('Y-m-d') ?? '' }}">{{ $pupil->dob->format('d/m/Y') }}</td>
                            <td>{{ $pupil->gender }}</td>
                            <td>
                                {{ $pupil->has_special_needs ? 'Yes' : '' }}
                            </td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $pupil->attachments])
                            </td>
                            @canany(['edit-pupils', 'delete-pupils'])
                            <td class="icon_wrap">
                                @can('edit-pupils')
                                <a href="{{ route('pupils.edit', $pupil->id) }}" class="icon edit_icon"><i class="fa fa-edit"></i></a>
                                @endcan
                                @can('delete-pupils')
                                <button class="icon delete_icon"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete"
                                    data-url="{{ route('pupils.destroy', $pupil->id) }}"
                                    data-name="{{ $pupil->first_name }} {{ $pupil->last_name }}">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-pupils', 'delete-pupils']) ? '7' : '6' }}" class="empty_table_message">No pupils found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('delete-pupils')
    @include('components.delete_modal', ['type' => 'Pupil'])
    @endcan

    @can('edit-pupils')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])
    @endcan
@endsection
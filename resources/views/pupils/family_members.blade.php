@extends('layouts.app')

@section('content')
    <section id="content">
        @include('components.pupil_page_top_header', [
            'route_name' => 'family-members',
            'new_button_text' => __('Add New Family Member')
        ])

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->familyMembers as $familyMember)
                <div class="sen_card" @if($pupil->primary_family_member_id == $familyMember->id) style="background-color: #fffbec;" @endif>
                    <div class="top">
                        <div class="label">
                            {{ $familyMember->first_name }} {{ $familyMember->last_name }}
                            @if ($pupil->primary_family_member_id == $familyMember->id)
                                <div class="sub_label" style="color: #aa1b1b;font-weight:600;">
                                    {{ __('Next of Kin') }}
                                </div>
                            @endif
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-family-members')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('family-members.update', $familyMember->id) }}" 
                                data-first_name="{{ $familyMember->first_name }}" 
                                data-last_name="{{ $familyMember->last_name }}" 
                                data-dob="{{ optional($familyMember->dob)->format('Y-m-d') }}"
                                data-relation="{{ $familyMember->relation }}"
                                data-phone="{{ $familyMember->phone }}"
                                data-email="{{ $familyMember->email }}"
                                data-address_line_1="{{ $familyMember->address_line_1 }}"
                                data-address_line_2="{{ $familyMember->address_line_2 }}"
                                data-locality="{{ $familyMember->locality }}"
                                data-postcode="{{ $familyMember->postcode }}"
                                data-country="{{ $familyMember->country }}"
                                data-marital_status="{{ $familyMember->marital_status }}"
                                data-highest_education="{{ $familyMember->highest_education }}"
                                data-financial_status="{{ $familyMember->financial_status }}"
                                data-occupation="{{ $familyMember->occupation }}"
                                data-state_support="{{ $familyMember->state_support }}"
                                data-is_primary="{{ $pupil->primary_family_member_id == $familyMember->id ? '1' : '0' }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-family-members')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('family-members.destroy', $familyMember->id) }}" 
                                data-name="{{ $familyMember->first_name }} {{ $familyMember->last_name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">{{ __('Relation') }}:</div>
                                <div class="value">
                                    {!! $familyMember->relation ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">{{ __('Date of Birth') }}:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {!! optional($familyMember->dob)->format('d/m/Y') ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">{{ __('Phone') }}:</div>
                                <div class="value">
                                    <i class="fas fa-phone"></i>
                                    {!! $familyMember->phone ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">{{ __('Email') }}:</div>
                                <div class="value">
                                    <i class="fas fa-envelope"></i>
                                    @if($familyMember->email)
                                        <a class="nice_link" href="mailto:{{ $familyMember->email }}">{{ $familyMember->email }}</a>
                                    @else
                                        <span class="text-muted">{{ __('N/A') }}</span>
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12 mb-2">
                                <div class="label">{{ __('Address') }}:</div>
                                <div class="value">
                                    {!! collect([$familyMember->address_line_1, $familyMember->address_line_2, $familyMember->locality, $familyMember->postcode, $familyMember->country])->filter()->implode(', <br>') ?: '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">{{ __('Marital Status') }}:</div>
                                <div class="value">{!! $familyMember->marital_status ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</div>
                            </div>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">{{ __('Highest Education') }}:</div>
                                <div class="value">{!! $familyMember->highest_education ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</div>
                            </div>
                            <div class="item col-md-4">
                                <div class="label">{{ __('Occupation') }}:</div>
                                <div class="value">{!! $familyMember->occupation ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">{{ __('Financial Status') }}:</div>
                                <div class="value">{!! $familyMember->financial_status ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">{{ __('Government/State Support') }}:</div>
                                <div class="value">{!! $familyMember->state_support ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Last Edited') }}:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $familyMember->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $familyMember->updated_at->format('H:i') }}
                                </div>
                            </div>
                            @include('components.attachments_list', ['attachments' => $familyMember->attachments, 'card' => true, 'delete_permission' => 'edit-family-members',])
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">{{ __('No family members found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Relation') }}</th>
                        <th scope="col">{{ __('Date of Birth') }}</th>
                        <th scope="col">{{ __('Phone') }}</th>
                        <th scope="col">{{ __('Attachments') }}</th>
                        @canany(['edit-family-members', 'delete-family-members'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->familyMembers as $familyMember)
                        <tr @if($pupil->primary_family_member_id == $familyMember->id) class="primary_family_member" @endif>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>
                                {{ $familyMember->first_name }} {{ $familyMember->last_name }}
                                @if ($pupil->primary_family_member_id == $familyMember->id)
                                    <span class="badge" style="background-color: #aa1b1b; margin-left:10px;">{{ __('Next of Kin') }}</span>
                                @endif
                            </td>
                            <td>{{ $familyMember->relation }}</td>
                            <td data-order="{{ optional($familyMember->dob)->format('Y-m-d') ?? '' }}">{!! optional($familyMember->dob)->format('d/m/Y') ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</td>
                            <td>{!! $familyMember->phone ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $familyMember->attachments])
                            </td>
                            @canany(['edit-family-members', 'delete-family-members'])
                            <td class="icon_wrap">
                                @can('edit-family-members')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('family-members.update', $familyMember->id) }}" 
                                    data-first_name="{{ $familyMember->first_name }}" 
                                    data-last_name="{{ $familyMember->last_name }}" 
                                    data-dob="{{ optional($familyMember->dob)->format('Y-m-d') }}"
                                    data-relation="{{ $familyMember->relation }}"
                                    data-phone="{{ $familyMember->phone }}"
                                    data-email="{{ $familyMember->email }}"
                                    data-address_line_1="{{ $familyMember->address_line_1 }}"
                                    data-address_line_2="{{ $familyMember->address_line_2 }}"
                                    data-locality="{{ $familyMember->locality }}"
                                    data-postcode="{{ $familyMember->postcode }}"
                                    data-country="{{ $familyMember->country }}"
                                    data-marital_status="{{ $familyMember->marital_status }}"
                                    data-highest_education="{{ $familyMember->highest_education }}"
                                    data-financial_status="{{ $familyMember->financial_status }}"
                                    data-occupation="{{ $familyMember->occupation }}"
                                    data-state_support="{{ $familyMember->state_support }}"
                                    data-is_primary="{{ $pupil->primary_family_member_id == $familyMember->id ? '1' : '0' }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-family-members')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('family-members.destroy', $familyMember->id) }}" 
                                    data-name="{{ $familyMember->first_name }} {{ $familyMember->last_name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-family-members', 'delete-family-members']) ? '6' : '5' }}" class="empty_table_message">{{ __('No family members found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-family-members')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Add New Family Member') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('family-members.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        @include('components.file_extraction_box')
                         <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('First Name') }}*</label>
                                <input type="text" class="form-control" name="first_name" required placeholder="{{ __('First Name') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Last Name') }}*</label>
                                <input type="text" class="form-control" name="last_name" required placeholder="{{ __('Last Name') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Relation') }}</label>
                                <input type="text" class="form-control" name="relation" placeholder="{{ __('e.g. Mother, Guardian') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Date of Birth') }}</label>
                                <input type="date" class="form-control" name="dob">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Phone') }}</label>
                                <input type="text" class="form-control" name="phone" placeholder="{{ __('Phone') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Email') }}</label>
                                <input type="email" class="form-control" name="email" placeholder="{{ __('Email') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">{{ __('Address') }}</div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Address Line 1') }}</label>
                                <input type="text" class="form-control" name="address_line_1" placeholder="{{ __('Address Line 1') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Address Line 2') }}</label>
                                <input type="text" class="form-control" name="address_line_2" placeholder="{{ __('Address Line 2') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Town/City') }}</label>
                                <input type="text" class="form-control" name="locality" placeholder="{{ __('Town/City') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Postcode') }}</label>
                                <input type="text" class="form-control" name="postcode" placeholder="{{ __('Postcode') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Country') }}</label>
                                <input type="text" class="form-control" name="country" placeholder="{{ __('Country') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">{{ __('Demographics & Other Info') }}</div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Marital Status') }}</label>
                                <input type="text" class="form-control" name="marital_status" placeholder="{{ __('e.g. Married, Single') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Highest Education') }}</label>
                                <input type="text" class="form-control" name="highest_education" placeholder="{{ __('e.g. Bachelors Degree') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Financial Status') }}</label>
                                <input type="text" class="form-control" name="financial_status">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Occupation') }}</label>
                                <input type="text" class="form-control" name="occupation" placeholder="{{ __('Current profession') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('State Support/Benefits') }}</label>
                                <input type="text" class="form-control" name="state_support" placeholder="{{ __('e.g. Universal Credit') }}">
                            </div>
                        </div>
                        <div class="form-check mt-2 mb-4">
                            <input class="form-check-input" type="checkbox" name="next_of_kin" value="1" id="new_next_of_kin">
                            <label class="form-check-label" for="new_next_of_kin">
                                {{ __("Pupil's next of kin?") }}
                            </label>
                        </div>
                        @include('components.attachments_input', ['for_create' => true])
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-family-members')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Edit Family Member') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('First Name') }}*</label>
                                <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="{{ __('First Name') }}" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Last Name') }}*</label>
                                <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="{{ __('Last Name') }}" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Relation') }}</label>
                                <input type="text" class="form-control" name="relation" id="edit_relation" placeholder="{{ __('e.g. Mother, Guardian') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Date of Birth') }}</label>
                                <input type="date" class="form-control" name="dob" id="edit_dob">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone" placeholder="{{ __('Phone') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Email') }}</label>
                                <input type="email" class="form-control" name="email" id="edit_email" placeholder="{{ __('Email') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">{{ __('Address Details') }}</div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Address Line 1') }}</label>
                                <input type="text" class="form-control" name="address_line_1" id="edit_address_line_1" placeholder="{{ __('Address Line 1') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Address Line 2') }}</label>
                                <input type="text" class="form-control" name="address_line_2" id="edit_address_line_2" placeholder="{{ __('Address Line 2') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Town/City') }}</label>
                                <input type="text" class="form-control" name="locality" id="edit_locality" placeholder="{{ __('Town/City') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Postcode') }}</label>
                                <input type="text" class="form-control" name="postcode" id="edit_postcode" placeholder="{{ __('Postcode') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Country') }}</label>
                                <input type="text" class="form-control" name="country" id="edit_country" placeholder="{{ __('Country') }}">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">{{ __('Demographics & Other Info') }}</div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Marital Status') }}</label>
                                <input type="text" class="form-control" name="marital_status" id="edit_marital_status" placeholder="{{ __('e.g. Married, Single') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Highest Education') }}</label>
                                <input type="text" class="form-control" name="highest_education" id="edit_highest_education" placeholder="{{ __('e.g. Bachelors Degree') }}">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>{{ __('Financial Status') }}</label>
                                <input type="text" class="form-control" name="financial_status" id="edit_financial_status">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Occupation') }}</label>
                                <input type="text" class="form-control" name="occupation" id="edit_occupation" placeholder="{{ __('Current profession') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('State Support/Benefits') }}</label>
                                <input type="text" class="form-control" name="state_support" id="edit_state_support" placeholder="{{ __('e.g. Universal Credit') }}">
                            </div>
                        </div>
                        <div class="form-check mt-2 mb-4">
                            <input class="form-check-input" type="checkbox" name="next_of_kin" value="1" id="edit_next_of_kin">
                            <label class="form-check-label" for="edit_next_of_kin">
                                {{ __("Pupil's next of kin?") }}
                            </label>
                        </div>
                        @include('components.attachments_input')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-family-members')
    @include('components.delete_modal', ['type' => __('Family Member')])
    @endcan

    @can('edit-family-members')
    @include('components.delete_modal', ['type' => __('Attachment'), 'id' => 'deleteAttachment'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_first_name').val($(this).data('first_name'));
        $('#edit_last_name').val($(this).data('last_name'));
        $('#edit_dob').val($(this).data('dob'));
        $('#edit_relation').val($(this).data('relation'));
        $('#edit_phone').val($(this).data('phone'));
        $('#edit_email').val($(this).data('email'));
        $('#edit_address_line_1').val($(this).data('address_line_1'));
        $('#edit_address_line_2').val($(this).data('address_line_2'));
        $('#edit_locality').val($(this).data('locality'));
        $('#edit_postcode').val($(this).data('postcode'));
        $('#edit_country').val($(this).data('country'));
        $('#edit_marital_status').val($(this).data('marital_status'));
        $('#edit_highest_education').val($(this).data('highest_education'));
        $('#edit_financial_status').val($(this).data('financial_status'));
        $('#edit_occupation').val($(this).data('occupation'));
        $('#edit_state_support').val($(this).data('state_support'));

        var isPrimary = $(this).data('is_primary');
        $('#edit_next_of_kin').prop('checked', isPrimary == 1);
    });

    // setup file extraction
    setupFileExtraction('{{ route("family-members.extract-file") }}', '{{ csrf_token() }}', function(d) {
        if (d.first_name) $('#new input[name="first_name"]').val(d.first_name);
        if (d.last_name) $('#new input[name="last_name"]').val(d.last_name);
        if (d.dob) $('#new input[name="dob"]').val(d.dob);
        if (d.relation) $('#new input[name="relation"]').val(d.relation);
        if (d.phone) $('#new input[name="phone"]').val(d.phone);
        if (d.email) $('#new input[name="email"]').val(d.email);
        if (d.address_line_1) $('#new input[name="address_line_1"]').val(d.address_line_1);
        if (d.address_line_2) $('#new input[name="address_line_2"]').val(d.address_line_2);
        if (d.locality) $('#new input[name="locality"]').val(d.locality);
        if (d.postcode) $('#new input[name="postcode"]').val(d.postcode);
        if (d.country) $('#new input[name="country"]').val(d.country);
        if (d.marital_status) $('#new input[name="marital_status"]').val(d.marital_status);
        if (d.highest_education) $('#new input[name="highest_education"]').val(d.highest_education);
        if (d.financial_status) $('#new input[name="financial_status"]').val(d.financial_status);
        if (d.occupation) $('#new input[name="occupation"]').val(d.occupation);
        if (d.state_support) $('#new input[name="state_support"]').val(d.state_support);
        if (d.next_of_kin !== undefined) {
            $('#new #new_next_of_kin').prop('checked', d.next_of_kin == true || d.next_of_kin == 1 || d.next_of_kin == 'true');
        }
    });
</script>
@endpush

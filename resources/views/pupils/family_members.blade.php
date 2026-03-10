@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
            <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                    Toggle Card View
                </button>
                @can('create-family-members')
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Family Member
                </button> 
                @endcan
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->familyMembers as $familyMember)
                <div class="sen_card" @if($pupil->primary_family_member_id == $familyMember->id) style="background-color: #fffbec;" @endif>
                    <div class="top">
                        <div class="label">
                            {{ $familyMember->first_name }} {{ $familyMember->last_name }}
                            @if ($pupil->primary_family_member_id == $familyMember->id)
                                <div class="sub_label" style="color: #aa1b1b;font-weight:600;">
                                    Next of Kin
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
                                <div class="label">Relation:</div>
                                <div class="value">
                                    {{ $familyMember->relation ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Date of Birth:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($familyMember->dob)->format('d/m/Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Phone:</div>
                                <div class="value">
                                    <i class="fas fa-phone"></i>
                                    {{ $familyMember->phone ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Email:</div>
                                <div class="value">
                                    <i class="fas fa-envelope"></i>
                                    @if($familyMember->email)
                                        <a class="value_link" href="mailto:{{ $familyMember->email }}">{{ $familyMember->email }}</a>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12 mb-2">
                                <div class="label">Address:</div>
                                <div class="value">
                                    {!! collect([$familyMember->address_line_1, $familyMember->address_line_2, $familyMember->locality, $familyMember->postcode, $familyMember->country])->filter()->implode(', <br>') ?: 'N/A' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">Marital Status:</div>
                                <div class="value">{{ $familyMember->marital_status ?? 'N/A' }}</div>
                            </div>
                            <div class="item col-md-4 border_right-md">
                                <div class="label">Highest Education:</div>
                                <div class="value">{{ $familyMember->highest_education ?? 'N/A' }}</div>
                            </div>
                            <div class="item col-md-4">
                                <div class="label">Occupation:</div>
                                <div class="value">{{ $familyMember->occupation ?? 'N/A' }}</div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Financial Status:</div>
                                <div class="value">{{ $familyMember->financial_status ?? 'N/A' }}</div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Government/State Support:</div>
                                <div class="value">{{ $familyMember->state_support ?? 'N/A' }}</div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $familyMember->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $familyMember->updated_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No family members found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Relation</th>
                        <th scope="col">DOB</th>
                        <th scope="col">Phone</th>
                        @canany(['edit-family-members', 'delete-family-members'])
                        <th scope="col">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->familyMembers as $familyMember)
                        <tr @if($pupil->primary_family_member_id == $familyMember->id) class="primary_family_member" @endif>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $familyMember->first_name }} {{ $familyMember->last_name }}</td>
                            <td>{{ $familyMember->relation }}</td>
                            <td data-order="{{ optional($familyMember->dob)->format('Y-m-d') ?? '' }}">{{ optional($familyMember->dob)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ $familyMember->phone ?? 'N/A' }}</td>
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
                            <td colspan="{{ auth()->user()->canAny(['edit-family-members', 'delete-family-members']) ? '5' : '4' }}" class="empty_table_message">No family members found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
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
                    <h1 class="modal-title fs-5">Add New Family Member</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('family-members.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>First Name*</label>
                                <input type="text" class="form-control" name="first_name" required placeholder="First Name">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Last Name*</label>
                                <input type="text" class="form-control" name="last_name" required placeholder="Last Name">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Relation</label>
                                <input type="text" class="form-control" name="relation" placeholder="e.g. Mother, Guardian">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control" name="dob">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" placeholder="Phone">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" placeholder="Email">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">Address</div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Address Line 1</label>
                                <input type="text" class="form-control" name="address_line_1" placeholder="Address Line 1">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Address Line 2</label>
                                <input type="text" class="form-control" name="address_line_2" placeholder="Address Line 2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Town/City</label>
                                <input type="text" class="form-control" name="locality" placeholder="Town/City">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Postcode</label>
                                <input type="text" class="form-control" name="postcode" placeholder="Postcode">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Country</label>
                                <input type="text" class="form-control" name="country" placeholder="Country">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">Demographics & Other info</div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Marital Status</label>
                                <input type="text" class="form-control" name="marital_status" placeholder="e.g. Married, Single">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Highest Education</label>
                                <input type="text" class="form-control" name="highest_education" placeholder="e.g. Bachelors Degree">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Financial Status</label>
                                <input type="text" class="form-control" name="financial_status">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Occupation</label>
                                <input type="text" class="form-control" name="occupation" placeholder="Current profession">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>State Support/Benefits</label>
                                <input type="text" class="form-control" name="state_support" placeholder="e.g. Universal Credit">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="next_of_kin" value="1" id="new_next_of_kin">
                                <label class="form-check-label" for="new_next_of_kin">
                                    Pupil's next of kin?
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
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
                    <h1 class="modal-title fs-5">Edit Family Member</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>First Name*</label>
                                <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="First Name" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Last Name*</label>
                                <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="Last Name" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Relation</label>
                                <input type="text" class="form-control" name="relation" id="edit_relation" placeholder="e.g. Mother, Guardian">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control" name="dob" id="edit_dob">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Phone</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone" placeholder="Phone">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" id="edit_email" placeholder="Email">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">Address Details</div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Address Line 1</label>
                                <input type="text" class="form-control" name="address_line_1" id="edit_address_line_1" placeholder="Address Line 1">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Address Line 2</label>
                                <input type="text" class="form-control" name="address_line_2" id="edit_address_line_2" placeholder="Address Line 2">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Town/City</label>
                                <input type="text" class="form-control" name="locality" id="edit_locality" placeholder="Town/City">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Postcode</label>
                                <input type="text" class="form-control" name="postcode" id="edit_postcode" placeholder="Postcode">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Country</label>
                                <input type="text" class="form-control" name="country" id="edit_country" placeholder="Country">
                            </div>
                        </div>
                        <hr>
                        <div class="form_sub_title">Demographics & Other info</div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Marital Status</label>
                                <input type="text" class="form-control" name="marital_status" id="edit_marital_status" placeholder="e.g. Married, Single">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Highest Education</label>
                                <input type="text" class="form-control" name="highest_education" id="edit_highest_education" placeholder="e.g. Bachelors Degree">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Financial Status</label>
                                <input type="text" class="form-control" name="financial_status" id="edit_financial_status">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Occupation</label>
                                <input type="text" class="form-control" name="occupation" id="edit_occupation" placeholder="Current profession">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>State Support/Benefits</label>
                                <input type="text" class="form-control" name="state_support" id="edit_state_support" placeholder="e.g. Universal Credit">
                            </div>
                        </div>
                        <div class="form-group mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="next_of_kin" value="1" id="edit_next_of_kin">
                                <label class="form-check-label" for="edit_next_of_kin">
                                    Pupil's next of kin?
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-family-members')
    @include('components.delete_modal', ['type' => 'Family Member'])
    @endcan
@endsection

@push('scripts')
<script>
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
</script>
@endpush

@extends('layouts.app')

@section('content')
    <section id="content">
        <div style="display: flex; justify-content: space-between;">
           <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas fa-arrow-circle-left"></i></a> Return back to pupils
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                    Toggle Card View
                </button>
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Family Member
                </button> 
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @foreach($pupil->familyMembers as $familyMember)
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
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('family-members.update', $familyMember->id) }}" 
                                data-first_name="{{ $familyMember->first_name }}" 
                                data-last_name="{{ $familyMember->last_name }}" 
                                data-dob="{{ optional($familyMember->dob)->format('Y-m-d') }}"
                                data-relation="{{ $familyMember->relation }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('family-members.destroy', $familyMember->id) }}" 
                                data-name="{{ $familyMember->first_name }} {{ $familyMember->last_name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
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
                            <div class="item col-md-6">
                                <div class="label">Date of Birth:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($familyMember->dob)->format('d/m/Y') ?? 'N/A' }}
                                </div>
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
            @endforeach
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Relation</th>
                        <th scope="col">DOB</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pupil->familyMembers as $familyMember)
                        <tr @if($pupil->primary_family_member_id == $familyMember->id) class="primary_family_member" @endif>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $familyMember->first_name }} {{ $familyMember->last_name }}</td>
                            <td>{{ $familyMember->relation }}</td>
                            <td>{{ optional($familyMember->dob)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('family-members.update', $familyMember->id) }}" 
                                    data-first_name="{{ $familyMember->first_name }}" 
                                    data-last_name="{{ $familyMember->last_name }}" 
                                    data-dob="{{ optional($familyMember->dob)->format('Y-m-d') }}"
                                    data-relation="{{ $familyMember->relation }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('family-members.destroy', $familyMember->id) }}" 
                                    data-name="{{ $familyMember->first_name }} {{ $familyMember->last_name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

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
                            <div class="col-md-6 form-group mb-3">
                                <label>First Name*</label>
                                <input type="text" class="form-control" name="first_name" required placeholder="First Name">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Last Name*</label>
                                <input type="text" class="form-control" name="last_name" required placeholder="Last Name">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Relation</label>
                                <input type="text" class="form-control" name="relation" placeholder="e.g. Mother, Guardian">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control" name="dob">
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
                            <div class="col-md-6 form-group mb-3">
                                <label>First Name*</label>
                                <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="First Name" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Last Name*</label>
                                <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="Last Name" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Relation</label>
                                <input type="text" class="form-control" name="relation" id="edit_relation" placeholder="e.g. Mother, Guardian">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Date of Birth</label>
                                <input type="date" class="form-control" name="dob" id="edit_dob">
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

    @include('components.delete_modal', ['type' => 'Family Member'])
@endsection

@push('scripts')
<script>
    $(document).on('click', '.edit_icon, .sen_edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_first_name').val($(this).data('first_name'));
        $('#edit_last_name').val($(this).data('last_name'));
        $('#edit_dob').val($(this).data('dob'));
        $('#edit_relation').val($(this).data('relation'));
    });
</script>
@endpush

@extends('layouts.app')

@section('content')
    <section id="content">
        <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
			Create User
		</button>
        <div class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Username</th>
                        <th scope="col">Mobile</th>
                        <th scope="col">Position</th>
                        <th scope="col">Added By</th>
                        <th scope="col">Joined Date</th>
                        <th scope="col">Expiry Date</th>
                        <th scope="col">Group</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <th scope="row">{{ $user->id }}</th>
                            <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                            <td>{{ $user->username }}</td>
                            <td>{{ $user->mobile }}</td>
                            <td>{{ $user->position }}</td>
                            <td>{{ $user->addedBy ? $user->addedBy->first_name . ' ' . $user->addedBy->last_name : 'System' }}</td>
                            <td>{{ $user->joined_date ? $user->joined_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $user->expiry_date ? $user->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $user->group ? $user->group->name : 'N/A' }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" 
                                    data-id="{{ $user->id }}"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('users.destroy', $user->id) }}" 
                                    data-name="{{ $user->first_name }} {{ $user->last_name }}">
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
                <form action="{{ route('users.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Create User</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">            
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="first_name" placeholder="First Name" required />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name" placeholder="Last Name" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Username</label>
                                <input type="text" class="form-control" name="username" placeholder="Username" required />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Mobile</label>
                                <input type="text" class="form-control" name="mobile" placeholder="Mobile" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Position</label>
                                <input type="text" class="form-control" name="position" placeholder="Position" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>User Group</label>
                                <select class="form-control" name="user_group_id" required>
                                    <option value="">Select Group</option>
                                    @foreach($user_groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Joined Date</label>
                                <input type="date" class="form-control" name="joined_date" value="{{ date('Y-m-d') }}" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date" />
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Password</label>
                            <input type="password" class="form-control" name="password" placeholder="Password" required />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Edit User</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="First Name" required />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="Last Name" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Username</label>
                                <input type="text" class="form-control" name="username" id="edit_username" placeholder="Username" required />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Mobile</label>
                                <input type="text" class="form-control" name="mobile" id="edit_mobile" placeholder="Mobile" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Position</label>
                                <input type="text" class="form-control" name="position" id="edit_position" placeholder="Position" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>User Group</label>
                                <select class="form-control" name="user_group_id" id="edit_user_group_id" required>
                                    <option value="">Select Group</option>
                                    @foreach($user_groups as $group)
                                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Joined Date</label>
                                <input type="date" class="form-control" name="joined_date" id="edit_joined_date" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date" id="edit_expiry_date" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'User'])
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.edit_icon', function () {
            var id = $(this).data('id');
            var url = '/users/' + id;

            $.get(url, function (data) {
                $('#editForm').attr('action', url);
                $('#edit_first_name').val(data.first_name);
                $('#edit_last_name').val(data.last_name);
                $('#edit_username').val(data.username);
                $('#edit_mobile').val(data.mobile);
                $('#edit_position').val(data.position);
                $('#edit_user_group_id').val(data.user_group_id);
                if (data.joined_date) {
                    $('#edit_joined_date').val(data.joined_date.split('T')[0]);
                } else {
                    $('#edit_joined_date').val('');
                }

                if (data.expiry_date) {
                    $('#edit_expiry_date').val(data.expiry_date.split('T')[0]);
                } else {
                    $('#edit_expiry_date').val('');
                }
            });
        });
    </script>
@endpush
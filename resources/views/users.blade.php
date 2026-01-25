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
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>{{ $user->expiry_date ? $user->expiry_date->format('d/m/Y') : 'N/A' }}</td>
                            <td>{{ $user->group ? $user->group->name : 'N/A' }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection

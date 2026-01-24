@extends('layouts.app')

@section('content')
    <section id="content">
        <button type="button" class="new_button" data-toggle="modal" data-target="#new">
			Create User Group
		</button>
        <div class="table_wrap">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user_groups as $user_group)
                        <tr>
                            <th scope="row">{{ $user_group->id }}</th>
                            <td>{{ $user_group->name }}</td>
                            <td>{{ $user_group->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="new" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <form action="{{ route('user-groups.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Create User Group</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">            
                        <div class="form-group">
                            <label>User Group Name</label>
                            <input type="text" class="form-control" name="name" placeholder="User Group Name" required />
                        </div>
                        <div class="form-group">
                            <label>Description</label>
                            <input type="text" class="form-control" name="description" placeholder="Description" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary" name="save">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
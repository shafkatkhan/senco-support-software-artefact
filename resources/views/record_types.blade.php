@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                Create Record Type
            </button>
        </div>
        <div class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($record_types as $record_type)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $record_type->name }}</td>
                            <td>{{ $record_type->description }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" data-bs-toggle="modal" data-bs-target="#edit" data-url="{{ route('record-types.update', $record_type->id) }}" data-name="{{ $record_type->name }}" data-description="{{ $record_type->description }}"><i class="fa fa-edit"></i></button>
                                <button class="icon delete_icon" data-bs-toggle="modal" data-bs-target="#delete" data-url="{{ route('record-types.destroy', $record_type->id) }}" data-name="{{ $record_type->name }}"><i class="fa fa-trash-alt"></i></button>
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
                <form action="{{ route('record-types.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Create Record Type</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" placeholder="Name" required />
                        </div>
                        <div class="form-group mb-3">
                            <label>Description</label>
                            <input type="text" class="form-control" name="description" placeholder="Description" />
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
                        <h1 class="modal-title fs-5">Edit Record Type</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Name</label>
                            <input type="text" class="form-control" name="name" id="edit_name" placeholder="Name" required />
                        </div>
                        <div class="form-group mb-3">
                            <label>Description</label>
                            <input type="text" class="form-control" name="description" id="edit_description" placeholder="Description" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'Record Type'])
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.edit_icon', function () {
            var url = $(this).data('url');
            var name = $(this).data('name');
            var description = $(this).data('description');

            $('#editForm').attr('action', url);
            $('#edit_name').val(name);
            $('#edit_description').val(description);
        });
    </script>
@endpush
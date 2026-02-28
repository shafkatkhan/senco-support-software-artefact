@extends('layouts.app')

@section('content')
    <section id="content">
        <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
			Create Accommodation
		</button>
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
                    @forelse($accommodations as $accommodation)
                        <tr>
                            <th scope="row">{{ $accommodation->id }}</th>
                            <td>{{ $accommodation->name }}</td>
                            <td>{{ $accommodation->description }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" data-bs-toggle="modal" data-bs-target="#edit" data-url="{{ route('accommodations.update', $accommodation->id) }}" data-name="{{ $accommodation->name }}" data-description="{{ $accommodation->description }}"><i class="fa fa-edit"></i></button>
                                <button class="icon delete_icon" data-bs-toggle="modal" data-bs-target="#delete" data-url="{{ route('accommodations.destroy', $accommodation->id) }}" data-name="{{ $accommodation->name }}"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty_table_message">No accommodations found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('accommodations.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">Create Accommodation</h1>
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
                        <h1 class="modal-title fs-5">Edit Accommodation</h1>
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

    @include('components.delete_modal', ['type' => 'Accommodation'])
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
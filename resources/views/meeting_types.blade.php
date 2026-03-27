@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            @can('create-meeting-types')
            <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
                {{ __('Create Meeting Type') }}
            </button>
            @endcan
        </div>
        <div class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Description') }}</th>
                        @canany(['edit-meeting-types', 'delete-meeting-types'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($meeting_types as $meeting_type)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $meeting_type->name }}</td>
                            <td>{{ $meeting_type->description }}</td>
                            @canany(['edit-meeting-types', 'delete-meeting-types'])
                            <td class="icon_wrap">
                                @can('edit-meeting-types')
                                <button class="icon edit_icon" data-bs-toggle="modal" data-bs-target="#edit" data-url="{{ route('meeting-types.update', $meeting_type->id) }}" data-name="{{ $meeting_type->name }}" data-description="{{ $meeting_type->description }}"><i class="fa fa-edit"></i></button>
                                @endcan
                                @can('delete-meeting-types')
                                <button class="icon delete_icon" data-bs-toggle="modal" data-bs-target="#delete" data-url="{{ route('meeting-types.destroy', $meeting_type->id) }}" data-name="{{ $meeting_type->name }}"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-meeting-types', 'delete-meeting-types']) ? '4' : '3' }}" class="empty_table_message">{{ __('No meeting types found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-meeting-types')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('meeting-types.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Create Meeting Type') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" placeholder="{{ __('Name') }}" required />
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Description') }}</label>
                            <input type="text" class="form-control" name="description" placeholder="{{ __('Description') }}" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-meeting-types')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Edit Meeting Type') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" id="edit_name" placeholder="{{ __('Name') }}" required />
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Description') }}</label>
                            <input type="text" class="form-control" name="description" id="edit_description" placeholder="{{ __('Description') }}" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-meeting-types')
    @include('components.delete_modal', ['type' => __('Meeting Type')])
    @endcan
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
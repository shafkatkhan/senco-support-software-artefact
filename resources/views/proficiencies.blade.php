@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            @can('create-proficiencies')
            <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
                {{ __('Create Proficiency') }}
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
                        @canany(['edit-proficiencies', 'delete-proficiencies'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($proficiencies as $proficiency)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $proficiency->name }}</td>
                            <td>{{ $proficiency->description }}</td>
                            @canany(['edit-proficiencies', 'delete-proficiencies'])
                            <td class="icon_wrap">
                                @can('edit-proficiencies')
                                <button class="icon edit_icon" aria-label="{{ __('Edit') }}" data-bs-toggle="modal" data-bs-target="#edit" data-url="{{ route('proficiencies.update', $proficiency->id) }}" data-name="{{ $proficiency->name }}" data-description="{{ $proficiency->description }}"><i class="fa fa-edit"></i></button>
                                @endcan
                                @can('delete-proficiencies')
                                <button class="icon delete_icon" aria-label="{{ __('Delete') }}" data-bs-toggle="modal" data-bs-target="#delete" data-url="{{ route('proficiencies.destroy', $proficiency->id) }}" data-name="{{ $proficiency->name }}"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-proficiencies', 'delete-proficiencies']) ? '4' : '3' }}" class="empty_table_message">{{ __('No proficiencies found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-proficiencies')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('proficiencies.store') }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Create Proficiency') }}</h1>
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

    @can('edit-proficiencies')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Edit Proficiency') }}</h1>
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

    @can('delete-proficiencies')
    @include('components.delete_modal', ['type' => __('Proficiency')])
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
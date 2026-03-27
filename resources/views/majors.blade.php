@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            @can('create-majors')
            <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
                {{ __('Create Major') }}
            </button>
            @endcan
        </div>
        <div class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Code') }}</th>
                        <th scope="col" class="dt-left">{{ __('Subjects') }}</th>
                        @canany(['edit-majors', 'delete-majors'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($majors as $major)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $major->name }}</td>
                            <td>{{ $major->code }}</td>
                            <td data-order="{{ $major->subjects->count() }}" class="dt-left">
                                @forelse($major->subjects as $subject)
                                    <span class="badge bg-secondary">{{ $subject->name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </td>
                            @canany(['edit-majors', 'delete-majors'])
                            <td class="icon_wrap">
                                @can('edit-majors')
                                <button class="icon edit_icon" data-bs-toggle="modal" data-bs-target="#edit" data-url="{{ route('majors.update', $major->id) }}" data-name="{{ $major->name }}" data-code="{{ $major->code }}" data-subjects='@json($major->subjects->pluck("id"))'><i class="fa fa-edit"></i></button>
                                @endcan
                                @can('delete-majors')
                                <button class="icon delete_icon" data-bs-toggle="modal" data-bs-target="#delete" data-url="{{ route('majors.destroy', $major->id) }}" data-name="{{ $major->name }}"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-majors', 'delete-majors']) ? '5' : '4' }}" class="empty_table_message">{{ __('No majors found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-majors')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('majors.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Create Major') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" placeholder="{{ __('Name') }}" required />
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Code') }}</label>
                            <input type="text" class="form-control" name="code" placeholder="{{ __('Code') }}" />
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Subjects') }}</label>
                            <select class="form-control select2_multi_select" name="subject_ids[]" id="new_subject_ids" multiple data-placeholder="{{ __('Select subjects') }}" data-dropdown_parent="new">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
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

    @can('edit-majors')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Edit Major') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Name') }}</label>
                            <input type="text" class="form-control" name="name" id="edit_name" placeholder="{{ __('Name') }}" required />
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Code') }}</label>
                            <input type="text" class="form-control" name="code" id="edit_code" placeholder="{{ __('Code') }}" />
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Subjects') }}</label>
                            <select class="form-control select2_multi_select" name="subject_ids[]" id="edit_subject_ids" multiple data-placeholder="{{ __('Select subjects') }}" data-dropdown_parent="edit">
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
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

    @can('delete-majors')
    @include('components.delete_modal', ['type' => __('Major')])
    @endcan
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.edit_icon', function () {
            var url = $(this).data('url');
            var name = $(this).data('name');
            var code = $(this).data('code');
            var subjects = $(this).data('subjects');

            $('#editForm').attr('action', url);
            $('#edit_name').val(name);
            $('#edit_code').val(code);
            $('#edit_subject_ids')
                .val(subjects ? subjects.map(String) : [])
                .trigger('change');
        });
    </script>
@endpush

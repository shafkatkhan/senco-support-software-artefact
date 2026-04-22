@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            @can('create-subjects')
            <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
                {{ __('Create Subject') }}
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
                        <th scope="col" class="dt-left">{{ __('Proficiencies') }}</th>
                        <th scope="col" class="dt-left">{{ __('Accommodations') }}</th>
                        @canany(['edit-subjects', 'delete-subjects'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($subjects as $subject)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $subject->name }}</td>
                            <td>{{ $subject->code }}</td>
                            <td data-order="{{ $subject->proficiencies->count() }}" class="dt-left">
                                @forelse($subject->proficiencies as $proficiency)
                                    <span class="badge bg-secondary">{{ $proficiency->name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </td>
                            <td data-order="{{ $subject->accommodations->count() }}" class="dt-left">
                                @forelse($subject->accommodations as $accommodation)
                                    <span class="badge bg-secondary">{{ $accommodation->name }}</span>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </td>
                            @canany(['edit-subjects', 'delete-subjects'])
                            <td class="icon_wrap">
                                @can('edit-subjects')
                                <button class="icon edit_icon" aria-label="{{ __('Edit') }}" data-bs-toggle="modal" data-bs-target="#edit" data-url="{{ route('subjects.update', $subject->id) }}" data-name="{{ $subject->name }}" data-code="{{ $subject->code }}" data-accommodations='@json($subject->accommodations->pluck("id"))' data-proficiencies='@json($subject->proficiencies->pluck("id"))'><i class="fa fa-edit"></i></button>
                                @endcan
                                @can('delete-subjects')
                                <button class="icon delete_icon" aria-label="{{ __('Delete') }}" data-bs-toggle="modal" data-bs-target="#delete" data-url="{{ route('subjects.destroy', $subject->id) }}" data-name="{{ $subject->name }}"><i class="fa fa-trash-alt"></i></button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-subjects', 'delete-subjects']) ? '6' : '5' }}" class="empty_table_message">{{ __('No subjects found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-subjects')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('subjects.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Create Subject') }}</h1>
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
                            <label>{{ __('Proficiencies') }}</label>
                            <select class="form-control select2_multi_select" name="proficiency_ids[]" id="new_proficiency_ids" multiple data-placeholder="{{ __('Select proficiencies') }}" data-dropdown_parent="new">
                                @foreach($proficiencies as $proficiency)
                                    <option value="{{ $proficiency->id }}">{{ $proficiency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Accommodations') }}</label>
                            <select class="form-control select2_multi_select" name="accommodation_ids[]" id="new_accommodation_ids" multiple data-placeholder="{{ __('Select accommodations') }}" data-dropdown_parent="new">
                                @foreach($accommodations as $accommodation)
                                    <option value="{{ $accommodation->id }}">{{ $accommodation->name }}</option>
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

    @can('edit-subjects')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Edit Subject') }}</h1>
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
                            <label>{{ __('Proficiencies') }}</label>
                            <select class="form-control select2_multi_select" name="proficiency_ids[]" id="edit_proficiency_ids" multiple data-placeholder="{{ __('Select proficiencies') }}" data-dropdown_parent="edit">
                                @foreach($proficiencies as $proficiency)
                                    <option value="{{ $proficiency->id }}">{{ $proficiency->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Accommodations') }}</label>
                            <select class="form-control select2_multi_select" name="accommodation_ids[]" id="edit_accommodation_ids" multiple data-placeholder="{{ __('Select accommodations') }}" data-dropdown_parent="edit">
                                @foreach($accommodations as $accommodation)
                                    <option value="{{ $accommodation->id }}">{{ $accommodation->name }}</option>
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

    @can('delete-subjects')
    @include('components.delete_modal', ['type' => __('Subject')])
    @endcan
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.edit_icon', function () {
            var url = $(this).data('url');
            var name = $(this).data('name');
            var code = $(this).data('code');
            var accommodations = $(this).data('accommodations');
            var proficiencies = $(this).data('proficiencies');

            $('#editForm').attr('action', url);
            $('#edit_name').val(name);
            $('#edit_code').val(code);
            $('#edit_proficiency_ids')
                .val(proficiencies ? proficiencies.map(String) : [])
                .trigger('change');
            $('#edit_accommodation_ids')
                .val(accommodations ? accommodations.map(String) : [])
                .trigger('change');
        });
    </script>
@endpush

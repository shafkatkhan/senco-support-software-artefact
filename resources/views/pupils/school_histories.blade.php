@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
            <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> {{ __('Return back to pupils') }}
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="top_button toggle_button" id="toggleViewBtn">
                    {{ __('Toggle Card View') }}
                </button>
                @can('create-school-histories')
                <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
                    {{ __('Add School History') }}
                </button> 
                @endcan
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->schoolHistories as $history)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $history->school_name }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-school-histories')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('school-histories.update', $history->id) }}" 
                                data-name="{{ $history->school_name }}"
                                data-school_type="{{ $history->school_type }}"
                                data-class_type="{{ $history->class_type }}"
                                data-years_attended="{{ $history->years_attended }}"
                                data-transition_reason="{{ $history->transition_reason }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-school-histories')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('school-histories.destroy', $history->id) }}" 
                                data-name="{{ $history->school_name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-12">
                                <div class="label">{{ __('School Type') }}:</div>
                                <div class="value">
                                    {!! $history->school_type ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Class Type') }}:</div>
                                <div class="value">
                                    {!! $history->class_type ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Years Attended') }}:</div>
                                <div class="value">
                                    {!! number_format((float)$history->years_attended, 1) ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Reason for Transition') }}:</div>
                                <div class="value">
                                    {!! $history->transition_reason ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            @include('components.attachments_list', ['attachments' => $history->attachments, 'card' => true, 'delete_permission' => 'edit-school-histories'])
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">{{ __('No school history found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Name of School') }}</th>
                        <th scope="col">{{ __('School Type') }}</th>
                        <th scope="col">{{ __('Class Type') }}</th>
                        <th scope="col">{{ __('Years Attended') }}</th>
                        <th scope="col">{{ __('Attachments') }}</th>
                        @canany(['edit-school-histories', 'delete-school-histories'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->schoolHistories as $history)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $history->school_name }}</td>
                            <td>{{ Str::limit($history->school_type, 50) }}</td>
                            <td>{{ Str::limit($history->class_type, 50) }}</td>
                            <td>{{ number_format((float)$history->years_attended, 1) }}</td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $history->attachments])
                            </td>
                            @canany(['edit-school-histories', 'delete-school-histories'])
                            <td class="icon_wrap">
                                @can('edit-school-histories')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('school-histories.update', $history->id) }}"
                                    data-name="{{ $history->school_name }}"
                                    data-school_type="{{ $history->school_type }}"
                                    data-class_type="{{ $history->class_type }}"
                                    data-years_attended="{{ $history->years_attended }}"
                                    data-transition_reason="{{ $history->transition_reason }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-school-histories')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('school-histories.destroy', $history->id) }}" 
                                    data-name="{{ $history->school_name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-school-histories', 'delete-school-histories']) ? '7' : '6' }}" class="empty_table_message">{{ __('No school history found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-school-histories')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Add School History') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('school-histories.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        @include('components.file_extraction_box')
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('School Name') }}*</label>
                                <input type="text" class="form-control" name="school_name" required placeholder="{{ __('School Name') }}">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label>{{ __('Years Attended') }}</label>
                                <input type="number" step="0.1" min="0" max="99" class="form-control" name="years_attended" placeholder="{{ __('e.g. 2.5') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('School Type') }}</label>
                                <input type="text" class="form-control" name="school_type" placeholder="{{ __('e.g. State School, Grammar School, Special School...') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Type of Class') }}</label>
                                <input type="text" class="form-control" name="class_type" placeholder="{{ __('e.g. Mainstream class, SEN unit...') }}">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Reason for Transition') }}</label>
                             <textarea class="form-control" name="transition_reason" rows="3" placeholder="{{ __('e.g. Change of location, move to secondary...') }}"></textarea>
                        </div>
                        @include('components.attachments_input', ['for_create' => true])
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-school-histories')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Edit School History') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('School Name') }}*</label>
                                <input type="text" class="form-control" name="school_name" id="edit_name" required placeholder="{{ __('School Name') }}">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label>{{ __('Years Attended') }}</label>
                                <input type="number" step="0.1" min="0" max="99" class="form-control" name="years_attended" id="edit_years_attended" placeholder="{{ __('e.g. 2.5') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('School Type') }}</label>
                                <input type="text" class="form-control" name="school_type" id="edit_school_type" placeholder="{{ __('e.g. State School, Grammar School, Special School...') }}">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Type of Class') }}</label>
                                <input type="text" class="form-control" name="class_type" id="edit_class_type" placeholder="{{ __('e.g. Mainstream class, SEN unit...') }}">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Reason for Transition') }}</label>
                             <textarea class="form-control" name="transition_reason" id="edit_transition_reason" rows="3" placeholder="{{ __('e.g. Change of location, move to secondary...') }}"></textarea>
                        </div>
                        @include('components.attachments_input')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-school-histories')
    @include('components.delete_modal', ['type' => __('Previous School')])
    @endcan

    @can('edit-school-histories')
    @include('components.delete_modal', ['type' => __('Attachment'), 'id' => 'deleteAttachment'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_name').val($(this).data('name'));
        $('#edit_school_type').val($(this).data('school_type'));
        $('#edit_class_type').val($(this).data('class_type'));
        $('#edit_years_attended').val($(this).data('years_attended'));
        $('#edit_transition_reason').val($(this).data('transition_reason'));
    });

    // setup file extraction
    setupFileExtraction('{{ route("school-histories.extract-file") }}', '{{ csrf_token() }}', function(d) {
        if (d.school_name) $('#new input[name="school_name"]').val(d.school_name);
        if (d.school_type) $('#new input[name="school_type"]').val(d.school_type);
        if (d.class_type) $('#new input[name="class_type"]').val(d.class_type);
        if (d.years_attended) $('#new input[name="years_attended"]').val(d.years_attended);
        if (d.transition_reason) $('#new textarea[name="transition_reason"]').val(d.transition_reason);
    });
</script>
@endpush

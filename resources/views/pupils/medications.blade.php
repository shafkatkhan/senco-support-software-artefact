@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
           <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                    Toggle Card View
                </button>
                @can('create-medications')
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Medication
                </button> 
                @endcan
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->medications as $medication)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $medication->name }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-medications')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('medications.update', $medication->id) }}" 
                                data-name="{{ $medication->name }}" 
                                data-dosage="{{ $medication->dosage }}" 
                                data-frequency="{{ $medication->frequency }}"
                                data-time_of_day="{{ $medication->time_of_day }}"
                                data-administration_method="{{ $medication->administration_method }}"
                                data-start_date="{{ $medication->start_date->format('Y-m-d') }}"
                                data-end_date="{{ $medication->end_date ? $medication->end_date->format('Y-m-d') : '' }}"
                                data-expiry_date="{{ $medication->expiry_date ? $medication->expiry_date->format('Y-m-d') : '' }}"
                                data-storage_instructions="{{ $medication->storage_instructions }}"
                                data-self_administer="{{ $medication->self_administer }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-medications')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('medications.destroy', $medication->id) }}" 
                                data-name="{{ $medication->name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Dosage:</div>
                                <div class="value">
                                    {{ $medication->dosage }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Frequency:</div>
                                <div class="value">
                                    {{ $medication->frequency }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Time of Day:</div>
                                <div class="value">
                                    {{ $medication->time_of_day }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Administration Method:</div>
                                <div class="value">
                                    {{ $medication->administration_method }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Start Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->start_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">End Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->end_date ? $medication->end_date->format('d/m/Y') : 'N/A'}}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Expiry Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->expiry_date ? $medication->expiry_date->format('d/m/Y') : 'N/A'}}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Self Adminster?</div>
                                <div class="value">
                                    {{ $medication->self_adminster ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Storage Instructions:</div>
                                <div class="value">
                                    {{ $medication->storage_instructions ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $medication->updated_at->format('H:i') }}
                                </div>
                            </div>
                            @include('components.attachments_list', ['attachments' => $medication->attachments, 'card' => true, 'delete_permission' => 'edit-medications'])
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No medications found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Dosage</th>
                        <th scope="col">Frequency</th>
                        <th scope="col">Time of Day</th>
                        <th scope="col">Administration Method</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Expiry Date</th>
                        <th scope="col">Storage Instructions</th>
                        <th scope="col">Self Administer?</th>
                        <th scope="col">Attachments</th>
                        @canany(['edit-medications', 'delete-medications'])
                        <th scope="col">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->medications as $medication)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $medication->name }}</td>
                            <td>{{ $medication->dosage }}</td>
                            <td>{{ $medication->frequency }}</td>
                            <td>{{ $medication->time_of_day }}</td>
                            <td>{{ $medication->administration_method }}</td>
                            <td data-order="{{ optional($medication->start_date)->format('Y-m-d') ?? '' }}">{{ $medication->start_date->format('d/m/Y') }}</td>
                            <td data-order="{{ optional($medication->end_date)->format('Y-m-d') ?? '' }}">{{ $medication->end_date ? $medication->end_date->format('d/m/Y') : 'N/A'}}</td>
                            <td data-order="{{ optional($medication->expiry_date)->format('Y-m-d') ?? '' }}">{{ $medication->expiry_date ? $medication->expiry_date->format('d/m/Y') : 'N/A'}}</td>
                            <td>{{ $medication->storage_instructions }}</td>
                            <td>{{ $medication->self_administer ? 'Yes' : 'No' }}</td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $medication->attachments])
                            </td>
                            @canany(['edit-medications', 'delete-medications'])
                            <td class="icon_wrap">
                                @can('edit-medications')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('medications.update', $medication->id) }}" 
                                    data-name="{{ $medication->name }}" 
                                    data-dosage="{{ $medication->dosage }}" 
                                    data-frequency="{{ $medication->frequency }}"
                                    data-time_of_day="{{ $medication->time_of_day }}"
                                    data-administration_method="{{ $medication->administration_method }}"
                                    data-start_date="{{ $medication->start_date->format('Y-m-d') }}"
                                    data-end_date="{{ $medication->end_date ? $medication->end_date->format('Y-m-d') : '' }}"
                                    data-expiry_date="{{ $medication->expiry_date ? $medication->expiry_date->format('Y-m-d') : '' }}"
                                    data-storage_instructions="{{ $medication->storage_instructions }}"
                                    data-self_administer="{{ $medication->self_administer }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-medications')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('medications.destroy', $medication->id) }}" 
                                    data-name="{{ $medication->name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-medications', 'delete-medications']) ? '12' : '11' }}" class="empty_table_message">No medications found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-medications')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Medication</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('medications.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        @include('components.file_extraction_box')
                        <div class="form-group mb-3">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" required placeholder="Medication Name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Dosage</label>
                                <input type="text" class="form-control" name="dosage" placeholder="e.g. 50mg">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Frequency*</label>
                                <input type="text" class="form-control" name="frequency" required placeholder="e.g. Twice Daily, As Needed">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Time of Day</label>
                                <input type="text" class="form-control" name="time_of_day" placeholder="e.g. Morning, Night, 1:30pm">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Method</label>
                                <input type="text" class="form-control" name="administration_method" placeholder="e.g. Oral, Injection">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Start Date*</label>
                                <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group mb-3">
                                <label>Storage Instructions</label>
                                <textarea class="form-control" name="storage_instructions" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="form-check mt-2 mb-4">
                            <input type="hidden" name="self_administer" value="0">
                            <input type="checkbox" class="form-check-input" name="self_administer" value="1" id="create_self_administer">
                            <label class="form-check-label" for="create_self_administer">Self Administer?</label>
                        </div>
                        @include('components.attachments_input', ['for_create' => true])
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-medications')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Medication</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                         <div class="form-group mb-3">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Dosage</label>
                                <input type="text" class="form-control" name="dosage" id="edit_dosage">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Frequency*</label>
                                <input type="text" class="form-control" name="frequency" id="edit_frequency" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Time of Day</label>
                                <input type="text" class="form-control" name="time_of_day" id="edit_time_of_day">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Method</label>
                                <input type="text" class="form-control" name="administration_method" id="edit_administration_method">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Start Date*</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" id="edit_end_date">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date" id="edit_expiry_date">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group mb-3">
                                <label>Storage Instructions</label>
                                <textarea class="form-control" name="storage_instructions" id="edit_storage_instructions" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="form-check mt-2 mb-4">
                            <input type="hidden" name="self_administer" value="0">
                            <input type="checkbox" class="form-check-input" name="self_administer" value="1" id="edit_self_administer">
                            <label class="form-check-label" for="edit_self_administer">Self Administer?</label>
                        </div>
                        @include('components.attachments_input')
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-medications')
    @include('components.delete_modal', ['type' => 'Medication'])
    @endcan

    @can('edit-medications')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_name').val($(this).data('name'));
        $('#edit_dosage').val($(this).data('dosage'));
        $('#edit_frequency').val($(this).data('frequency'));
        $('#edit_time_of_day').val($(this).data('time_of_day'));
        $('#edit_administration_method').val($(this).data('administration_method'));
        $('#edit_start_date').val($(this).data('start_date'));
        $('#edit_end_date').val($(this).data('end_date'));
        $('#edit_expiry_date').val($(this).data('expiry_date'));
        $('#edit_storage_instructions').val($(this).data('storage_instructions'));
        
        var selfAdmin = $(this).data('self_administer');
        $('#edit_self_administer').prop('checked', selfAdmin == 1);
    });

    // setup file extraction
    setupFileExtraction('{{ route("medications.extract-file") }}', '{{ csrf_token() }}', function(d) {
        if (d.name) $('#new input[name="name"]').val(d.name);
        if (d.dosage) $('#new input[name="dosage"]').val(d.dosage);
        if (d.frequency) $('#new input[name="frequency"]').val(d.frequency);
        if (d.time_of_day) $('#new input[name="time_of_day"]').val(d.time_of_day);
        if (d.administration_method) $('#new input[name="administration_method"]').val(d.administration_method);
        if (d.start_date) $('#new input[name="start_date"]').val(d.start_date);
        if (d.end_date) $('#new input[name="end_date"]').val(d.end_date);
        if (d.expiry_date) $('#new input[name="expiry_date"]').val(d.expiry_date);
        if (d.storage_instructions) $('#new textarea[name="storage_instructions"]').val(d.storage_instructions);
        if (d.self_administer !== undefined) {
            $('#new #create_self_administer').prop('checked', d.self_administer == true || d.self_administer == 1 || d.self_administer == 'true');
        }
    });
</script>
@endpush
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
                @can('create-records')
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Record
                </button> 
                @endcan
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->records as $record)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $record->title ?? $record->recordType->name . ' Record' }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-records')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('records.update', $record->id) }}" 
                                data-record_type_id="{{ $record->record_type_id }}"
                                data-professional_id="{{ $record->professional_id }}"
                                data-title="{{ $record->title }}"
                                data-date="{{ optional($record->date)->format('Y-m-d') }}" 
                                data-reference_number="{{ $record->reference_number }}"
                                data-description="{{ $record->description }}"
                                data-outcome="{{ $record->outcome }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-records')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('records.destroy', $record->id) }}" 
                                data-name="{{ $record->title ?? $record->recordType->name . ' Record' }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Type:</div>
                                <div class="value">
                                    <span class="badge bg-secondary">{{ $record->recordType->name }}</span>
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Professional:</div>
                                <div class="value">
                                    {{ $record->professional ? $record->professional->title . ' ' . $record->professional->first_name . ' ' . $record->professional->last_name : 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($record->date)->format('d/m/Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Reference No.:</div>
                                <div class="value">
                                    {{ $record->reference_number ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Description:</div>
                                <div class="value">
                                    {{ $record->description ?? 'N/A' }}
                                </div>
                            </div>
                            @if($record->outcome)
                                <hr>
                                <div class="item col-md-12">
                                    <div class="label">Outcome:</div>
                                    <div class="value">
                                        {{ $record->outcome ?? 'N/A' }}
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $record->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $record->updated_at->format('H:i') }}
                                </div>
                            </div>
                            @include('components.attachments_list', ['attachments' => $record->attachments, 'card' => true, 'delete_permission' => 'edit-records'])
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No records found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Title</th>
                        <th scope="col">Type</th>
                        <th scope="col">Date</th>
                        <th scope="col">Professional</th>
                        <th scope="col">Reference No.</th>
                        <th scope="col">Attachments</th>
                        @canany(['edit-records', 'delete-records'])
                        <th scope="col">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->records as $record)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $record->title ?? 'N/A' }}</td>
                            <td><span class="badge bg-secondary">{{ $record->recordType->name }}</span></td>
                            <td data-order="{{ optional($record->date)->format('Y-m-d') ?? '' }}">{{ optional($record->date)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ $record->professional ? $record->professional->title . ' ' . $record->professional->first_name . ' ' . $record->professional->last_name : 'N/A' }}</td>
                            <td>{{ $record->reference_number ?? 'N/A' }}</td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $record->attachments])
                            </td>
                            @canany(['edit-records', 'delete-records'])
                            <td class="icon_wrap">
                                @can('edit-records')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('records.update', $record->id) }}" 
                                    data-record_type_id="{{ $record->record_type_id }}"
                                    data-professional_id="{{ $record->professional_id }}"
                                    data-title="{{ $record->title }}"
                                    data-date="{{ optional($record->date)->format('Y-m-d') }}" 
                                    data-reference_number="{{ $record->reference_number }}"
                                    data-description="{{ $record->description }}"
                                    data-outcome="{{ $record->outcome }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-records')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('records.destroy', $record->id) }}" 
                                    data-name="{{ $record->title ?? $record->recordType->name . ' Record' }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-records', 'delete-records']) ? '8' : '7' }}" class="empty_table_message">No records found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-records')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Record</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('records.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        @include('components.file_extraction_box')
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Record Type*</label>
                                <select class="form-select" name="record_type_id" required>
                                    <option value="" disabled>--- Choose Type ---</option>
                                    @foreach($record_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                @include('components.inline_professional_form')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group mb-3">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" placeholder="Record Title">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label>Reference No.</label>
                                <input type="text" class="form-control" name="reference_number" placeholder="#123ABC">
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>Description*</label>
                             <textarea class="form-control" name="description" rows="3" required placeholder="Description of the record..."></textarea>
                        </div>
                         <div class="form-group mb-3">
                            <label>Outcome / Next Steps</label>
                            <textarea class="form-control" name="outcome" rows="3" placeholder="Outcomes or future actions..."></textarea>
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

    @can('edit-records')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Record</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Record Type*</label>
                                <select class="form-select" name="record_type_id" id="edit_record_type_id" required>
                                    <option value="" disabled>--- Choose Type ---</option>
                                    @foreach($record_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Professional</label>
                                <select class="form-select" name="professional_id" id="edit_professional_id">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($professionals as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->title }} {{ $prof->first_name }} {{ $prof->last_name }}{{ $prof->role ? ' (' . $prof->role . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 form-group mb-3">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" id="edit_title" placeholder="Record Title">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" id="edit_date">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label>Reference No.</label>
                                <input type="text" class="form-control" name="reference_number" id="edit_reference_number" placeholder="#123ABC">
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>Description*</label>
                             <textarea class="form-control" name="description" id="edit_description" rows="3" required placeholder="Description of the record..."></textarea>
                        </div>
                         <div class="form-group mb-3">
                            <label>Outcome / Next Steps</label>
                            <textarea class="form-control" name="outcome" id="edit_outcome" rows="3" placeholder="Outcomes or future actions..."></textarea>
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

    @can('delete-records')
    @include('components.delete_modal', ['type' => 'Record'])
    @endcan

    @can('edit-records')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_record_type_id').val($(this).data('record_type_id'));
        $('#edit_professional_id').val($(this).data('professional_id'));
        $('#edit_title').val($(this).data('title'));
        $('#edit_date').val($(this).data('date'));
        $('#edit_reference_number').val($(this).data('reference_number'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_outcome').val($(this).data('outcome'));
    });

    // setup file extraction
    setupFileExtraction('{{ route("records.extract-file") }}', '{{ csrf_token() }}', function(d) {
        if (d.record_type) {
            var normalised_record_type = d.record_type.toString().trim().toLowerCase();
            $('#new select[name="record_type_id"] option').each(function() {
                if ($(this).text().trim().toLowerCase() === normalised_record_type) {
                    $('#new select[name="record_type_id"]').val($(this).val());
                    return false;
                }
            });
        }
        if (d.title) $('#new input[name="title"]').val(d.title);
        if (d.date) $('#new input[name="date"]').val(d.date);
        if (d.reference_number) $('#new input[name="reference_number"]').val(d.reference_number);
        if (d.description) $('#new textarea[name="description"]').val(d.description);
        if (d.outcome) $('#new textarea[name="outcome"]').val(d.outcome);

        // populate new professional if any professional fields detected
        var hasProf = d.prof_first_name || d.prof_last_name || d.prof_role;
        if (hasProf) {
            if ($('#new #is_new_professional').val() !== '1') {
                $('#new #toggle_professional_btn').click();
            }
            if (d.prof_title) $('#new input[name="prof_title"]').val(d.prof_title);
            if (d.prof_first_name) $('#new input[name="prof_first_name"]').val(d.prof_first_name);
            if (d.prof_last_name) $('#new input[name="prof_last_name"]').val(d.prof_last_name);
            if (d.prof_role) $('#new input[name="prof_role"]').val(d.prof_role);
            if (d.prof_agency) $('#new input[name="prof_agency"]').val(d.prof_agency);
            if (d.prof_phone) $('#new input[name="prof_phone"]').val(d.prof_phone);
            if (d.prof_email) $('#new input[name="prof_email"]').val(d.prof_email);
        }
    });
</script>
@endpush
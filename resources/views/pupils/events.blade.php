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
                @can('create-events')
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Event
                </button> 
                @endcan
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->events as $event)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $event->title }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-events')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('events.update', $event->id) }}" 
                                data-title="{{ $event->title }}"
                                data-date="{{ optional($event->date)->format('Y-m-d') }}" 
                                data-reference_number="{{ $event->reference_number }}"
                                data-description="{{ $event->description }}"
                                data-outcome="{{ $event->outcome }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-events')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('events.destroy', $event->id) }}" 
                                data-name="{{ $event->title }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($event->date)->format('d/m/Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Reference No.:</div>
                                <div class="value">
                                    {{ $event->reference_number ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Description:</div>
                                <div class="value">
                                    {{ $event->description ?? 'N/A' }}
                                </div>
                            </div>
                            @if($event->outcome)
                                <hr>
                                <div class="item col-md-12">
                                    <div class="label">Outcome:</div>
                                    <div class="value">
                                        {{ $event->outcome ?? 'N/A' }}
                                    </div>
                                </div>
                            @endif
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($event->updated_at)->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ optional($event->updated_at)->format('H:i') }}
                                </div>
                            </div>
                            @include('components.attachments_list', ['attachments' => $event->attachments, 'card' => true, 'delete_permission' => 'edit-events',])
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No events found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Title</th>
                        <th scope="col">Date</th>
                        <th scope="col">Reference No.</th>
                        <th scope="col">Attachments</th>
                        @canany(['edit-events', 'delete-events'])
                        <th scope="col">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->events as $event)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $event->title }}</td>
                            <td data-order="{{ optional($event->date)->format('Y-m-d') ?? '' }}">{{ optional($event->date)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ $event->reference_number ?? 'N/A' }}</td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $event->attachments])
                            </td>
                            @canany(['edit-events', 'delete-events'])
                            <td class="icon_wrap">
                                @can('edit-events')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('events.update', $event->id) }}" 
                                    data-title="{{ $event->title }}"
                                    data-date="{{ optional($event->date)->format('Y-m-d') }}" 
                                    data-reference_number="{{ $event->reference_number }}"
                                    data-description="{{ $event->description }}"
                                    data-outcome="{{ $event->outcome }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-events')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('events.destroy', $event->id) }}" 
                                    data-name="{{ $event->title }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-events', 'delete-events']) ? '6' : '5' }}" class="empty_table_message">No events found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-events')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Event</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('events.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        @include('components.file_extraction_box')
                        <div class="row">
                            <div class="col-md-5 form-group mb-3">
                                <label>Title*</label>
                                <input type="text" class="form-control" name="title" required placeholder="Event Title">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-3 form-group mb-3">
                                <label>Reference No.</label>
                                <input type="text" class="form-control" name="reference_number" placeholder="#123ABC">
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>Description</label>
                             <textarea class="form-control" name="description" rows="3" placeholder="Description of the event..."></textarea>
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

    @can('edit-events')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Event</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-5 form-group mb-3">
                                <label>Title*</label>
                                <input type="text" class="form-control" name="title" id="edit_title" required placeholder="Event Title">
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
                            <label>Description</label>
                             <textarea class="form-control" name="description" id="edit_description" rows="3" placeholder="Description of the event..."></textarea>
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

    @can('delete-events')
    @include('components.delete_modal', ['type' => 'Event'])
    @endcan

    @can('edit-events')
    @include('components.delete_modal', ['type' => 'Attachment', 'id' => 'deleteAttachment'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_title').val($(this).data('title'));
        $('#edit_date').val($(this).data('date'));
        $('#edit_reference_number').val($(this).data('reference_number'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_outcome').val($(this).data('outcome'));
    });

    // setup file extraction
    setupFileExtraction('{{ route("events.extract-file") }}', '{{ csrf_token() }}', function(d) {
        if (d.title) $('#new input[name="title"]').val(d.title);
        if (d.date) $('#new input[name="date"]').val(d.date);
        if (d.reference_number) $('#new input[name="reference_number"]').val(d.reference_number);
        if (d.description) $('#new textarea[name="description"]').val(d.description);
        if (d.outcome) $('#new textarea[name="outcome"]').val(d.outcome);
    });
</script>
@endpush

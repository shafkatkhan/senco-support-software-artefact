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
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Meeting
                </button> 
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->meetings as $meeting)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $meeting->title }}
                        </div>
                        <div class="sen_icon_wrap">
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('meetings.update', $meeting->id) }}" 
                                data-date="{{ optional($meeting->date)->format('Y-m-d') }}" 
                                data-meeting_type_id="{{ $meeting->meeting_type_id }}" 
                                data-title="{{ $meeting->title }}" 
                                data-participants="{{ $meeting->participants }}"
                                data-discussion="{{ $meeting->discussion }}"
                                data-recommendations="{{ $meeting->recommendations }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('meetings.destroy', $meeting->id) }}" 
                                data-name="{{ $meeting->title }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Type:</div>
                                <div class="value">
                                    <span class="badge bg-secondary">{{ $meeting->meetingType->name }}</span>
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($meeting->date)->format('d/m/Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Participants:</div>
                                <div class="value">
                                    {{ $meeting->participants ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Discussion:</div>
                                <div class="value">
                                    {{ $meeting->discussion ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Recommendations:</div>
                                <div class="value">
                                    {{ $meeting->recommendations ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $meeting->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $meeting->updated_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No meetings found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
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
                        <th scope="col">Participants</th>
                        <th scope="col">Discussion</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->meetings as $meeting)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $meeting->title }}</td>
                            <td><span class="badge bg-secondary">{{ $meeting->meetingType->name }}</span></td>
                            <td>{{ optional($meeting->date)->format('d/m/Y') ?? 'N/A' }}</td>
                            <td>{{ Str::limit($meeting->participants, 30) }}</td>
                            <td>{{ Str::limit($meeting->discussion, 50) }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('meetings.update', $meeting->id) }}" 
                                    data-date="{{ optional($meeting->date)->format('Y-m-d') }}" 
                                    data-meeting_type_id="{{ $meeting->meeting_type_id }}" 
                                    data-title="{{ $meeting->title }}" 
                                    data-participants="{{ $meeting->participants }}"
                                    data-discussion="{{ $meeting->discussion }}"
                                    data-recommendations="{{ $meeting->recommendations }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('meetings.destroy', $meeting->id) }}" 
                                    data-name="{{ $meeting->title }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty_table_message">No meetings found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Meeting</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('meetings.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Meeting Type</label>
                                <select class="form-select" name="meeting_type_id" required>
                                    <option value="" selected disabled>--- Choose Type ---</option>
                                    @foreach($meeting_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" placeholder="Meeting Title" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Participants</label>
                            <textarea class="form-control" name="participants" rows="2" placeholder="List of attendees..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Discussion Notes</label>
                            <textarea class="form-control" name="discussion" rows="4" placeholder="Minutes or notes from the discussion..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Recommendations</label>
                            <textarea class="form-control" name="recommendations" rows="3" placeholder="Agreed actions or recommendations..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Meeting</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Meeting Type</label>
                                <select class="form-select" name="meeting_type_id" id="edit_meeting_type_id" required>
                                    <option value="" disabled>--- Choose Type ---</option>
                                    @foreach($meeting_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" id="edit_date">
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>Title</label>
                            <input type="text" class="form-control" name="title" id="edit_title" placeholder="Meeting Title" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Participants</label>
                            <textarea class="form-control" name="participants" id="edit_participants" rows="2" placeholder="List of attendees..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Discussion Notes</label>
                             <textarea class="form-control" name="discussion" id="edit_discussion" rows="4" placeholder="Minutes or notes from the discussion..."></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>Recommendations</label>
                            <textarea class="form-control" name="recommendations" id="edit_recommendations" rows="3" placeholder="Agreed actions or recommendations..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'Meeting'])
@endsection

@push('scripts')
<script>
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_date').val($(this).data('date'));
        $('#edit_meeting_type_id').val($(this).data('meeting_type_id'));
        $('#edit_title').val($(this).data('title'));
        $('#edit_participants').val($(this).data('participants'));
        $('#edit_discussion').val($(this).data('discussion'));
        $('#edit_recommendations').val($(this).data('recommendations'));
    });
</script>
@endpush

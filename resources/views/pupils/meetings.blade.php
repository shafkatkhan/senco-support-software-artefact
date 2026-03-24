@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
           <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> {{ __('Return back to pupils') }}
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                    {{ __('Toggle Card View') }}
                </button>
                @can('create-meetings')
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    {{ __('Add New Meeting') }}
                </button> 
                @endcan
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
                            @can('edit-meetings')
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
                            @endcan
                            @can('delete-meetings')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('meetings.destroy', $meeting->id) }}" 
                                data-name="{{ $meeting->title }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">{{ __('Type') }}:</div>
                                <div class="value">
                                    <span class="badge bg-secondary">{{ $meeting->meetingType->name }}</span>
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">{{ __('Date') }}:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {!! optional($meeting->date)->format('d/m/Y') ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Participants') }}:</div>
                                <div class="value">
                                    {!! $meeting->participants ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Discussion Notes') }}:</div>
                                <div class="value">
                                    {!! $meeting->discussion ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Recommendations') }}:</div>
                                <div class="value">
                                    {!! $meeting->recommendations ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Last Edited') }}:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $meeting->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $meeting->updated_at->format('H:i') }}
                                </div>
                            </div>
                            @include('components.attachments_list', ['attachments' => $meeting->attachments, 'card' => true, 'delete_permission' => 'edit-meetings',])
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">{{ __('No meetings found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Title') }}</th>
                        <th scope="col">{{ __('Type') }}</th>
                        <th scope="col">{{ __('Date') }}</th>
                        <th scope="col">{{ __('Participants') }}</th>
                        <th scope="col">{{ __('Discussion Notes') }}</th>
                        <th scope="col">{{ __('Attachments') }}</th>
                        @canany(['edit-meetings', 'delete-meetings'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->meetings as $meeting)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $meeting->title }}</td>
                            <td><span class="badge bg-secondary">{{ $meeting->meetingType->name }}</span></td>
                            <td data-order="{{ optional($meeting->date)->format('Y-m-d') ?? '' }}">{!! optional($meeting->date)->format('d/m/Y') ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</td>
                            <td>{{ Str::limit($meeting->participants, 30) }}</td>
                            <td>{{ Str::limit($meeting->discussion, 50) }}</td>
                            <td>
                                @include('components.attachments_list', ['attachments' => $meeting->attachments])
                            </td>
                            @canany(['edit-meetings', 'delete-meetings'])
                            <td class="icon_wrap">
                                @can('edit-meetings')
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
                                @endcan
                                @can('delete-meetings')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('meetings.destroy', $meeting->id) }}" 
                                    data-name="{{ $meeting->title }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-meetings', 'delete-meetings']) ? '8' : '7' }}" class="empty_table_message">{{ __('No meetings found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-meetings')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Add New Meeting') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('meetings.store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        @include('components.file_extraction_box')
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Meeting Type') }}</label>
                                <select class="form-select" name="meeting_type_id" required>
                                    <option value="" selected disabled>--- {{ __('Choose Type') }} ---</option>
                                    @foreach($meeting_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Date') }}</label>
                                <input type="date" class="form-control" name="date">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="title" placeholder="{{ __('Meeting Title') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Participants') }}</label>
                            <textarea class="form-control" name="participants" rows="2" placeholder="{{ __('List of attendees...') }}"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Discussion Notes') }}</label>
                            <textarea class="form-control" name="discussion" rows="4" placeholder="{{ __('Minutes or notes from the discussion...') }}"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Recommendations') }}</label>
                            <textarea class="form-control" name="recommendations" rows="3" placeholder="{{ __('Agreed actions or recommendations...') }}"></textarea>
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

    @can('edit-meetings')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Edit Meeting') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Meeting Type') }}</label>
                                <select class="form-select" name="meeting_type_id" id="edit_meeting_type_id" required>
                                    <option value="" disabled>--- {{ __('Choose Type') }} ---</option>
                                    @foreach($meeting_types as $type)
                                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Date') }}</label>
                                <input type="date" class="form-control" name="date" id="edit_date">
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>{{ __('Title') }}</label>
                            <input type="text" class="form-control" name="title" id="edit_title" placeholder="{{ __('Meeting Title') }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Participants') }}</label>
                            <textarea class="form-control" name="participants" id="edit_participants" rows="2" placeholder="{{ __('List of attendees...') }}"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Discussion Notes') }}</label>
                             <textarea class="form-control" name="discussion" id="edit_discussion" rows="4" placeholder="{{ __('Minutes or notes from the discussion...') }}"></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label>{{ __('Recommendations') }}</label>
                            <textarea class="form-control" name="recommendations" id="edit_recommendations" rows="3" placeholder="{{ __('Agreed actions or recommendations...') }}"></textarea>
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

    @can('delete-meetings')
    @include('components.delete_modal', ['type' => __('Meeting')] )
    @endcan

    @can('edit-meetings')
    @include('components.delete_modal', ['type' => __('Attachment'), 'id' => 'deleteAttachment'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
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

    // setup file extraction
    setupFileExtraction('{{ route("meetings.extract-file") }}', '{{ csrf_token() }}', function(d) {
        if (d.meeting_type) {
            var normalised_meeting_type = d.meeting_type.toString().trim().toLowerCase();
            $('#new select[name="meeting_type_id"] option').each(function() {
                if ($(this).text().trim().toLowerCase() === normalised_meeting_type) {
                    $('#new select[name="meeting_type_id"]').val($(this).val());
                    return false;
                }
            });
        }
        if (d.date) $('#new input[name="date"]').val(d.date);
        if (d.title) $('#new input[name="title"]').val(d.title);
        if (d.participants) $('#new textarea[name="participants"]').val(d.participants);
        if (d.discussion) $('#new textarea[name="discussion"]').val(d.discussion);
        if (d.recommendations) $('#new textarea[name="recommendations"]').val(d.recommendations);
    });
</script>
@endpush

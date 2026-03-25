@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> {{ __('Return back to pupils') }}
        </div>

        <div class="table_wrap">
            <table class="table sen_table-striped" data-order='[[ 3, "desc" ]]'>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('File Name') }}</th>
                        <th scope="col">{{ __('Source / Name') }}</th>
                        <th scope="col">{{ __('Date Uploaded') }}</th>
                        <th scope="col">{{ __('Transcript Snippet') }}</th>
                        <th scope="col" style="width: 140px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allAttachments as $attachment)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>
                                <a href="{{ route('attachments.show', $attachment->id) }}" target="_blank">
                                    {{ $attachment->filename }}
                                </a>
                            </td>
                            <td>
                                {{ $attachment->source_name }}
                            </td>
                            <td data-order="{{ $attachment->created_at->format('Y-m-d') }}">{{ $attachment->created_at->format('d/m/Y') }}</td>
                            <td>
                                @if($attachment->transcription)
                                    {{ Str::limit($attachment->transcription->transcript, 50) }}
                                @else
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endif
                            </td>
                            <td class="icon_wrap">
                                <a class="icon download_icon button_styled" href="{{ route('attachments.show', ['attachment' => $attachment->id, 'download' => 1]) }}" title="{{ __('Download') }}">
                                    <i class="fa fa-download"></i>
                                </a>
                                
                                @if($attachment->transcription)
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('attachments.update_transcript', $attachment->id) }}"
                                    data-transcript="{{ $attachment->transcription->transcript }}"
                                    title="{{ __('Edit Transcript') }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endif
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteAttachment" 
                                    data-url="{{ route('attachments.destroy', $attachment->id) }}" 
                                    data-name="{{ $attachment->filename }}"
                                    title="{{ __('Delete Attachment') }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty_table_message">{{ __('No attachments found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Edit Transcript') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Transcript') }}</label>
                            <textarea class="form-control" name="transcript" id="edit_transcript_text" rows="10" required placeholder="{{ __('Transcript content...') }}"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Update Transcript') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => __('Attachment'), 'id' => 'deleteAttachment'])
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_transcript_text').val($(this).data('transcript'));
    });
</script>
@endpush
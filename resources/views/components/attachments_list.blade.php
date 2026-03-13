@if($attachments && $attachments->count() > 0)
    @if(isset($card))
        <hr>
        <div class="item col-md-12">
            <div class="label">Attachments:</div>
            <div class="value">
    @endif
                <ul class="list-unstyled mb-0 {{ isset($card) ? 'attachments_list' : '' }}" style="{{ !isset($card) ? 'font-size: 0.9rem;' : '' }}">
                    @foreach($attachments as $attachment)
                        <li>
                            <a href="{{ route('attachments.show', $attachment->id) }}" target="_blank" class="text-decoration-none" title="{{ $attachment->filename }}">
                                <i class="fas fa-paperclip"></i> {{ !isset($card) ? Str::limit($attachment->filename, 15) : $attachment->filename }}
                            </a>
                            @if(isset($card))
                                @can($delete_permission)
                                    <button type="button" class="delete_attachment_icon" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#deleteAttachment" 
                                        data-url="{{ route('attachments.destroy', $attachment->id) }}" 
                                        data-name="{{ $attachment->filename }}"
                                    >
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                @endcan
                            @endif
                        </li>
                    @endforeach
                </ul>
    @if(isset($card))
            </div>
        </div>
    @endif
@else
    @if(!isset($card))
        N/A
    @endif
@endif
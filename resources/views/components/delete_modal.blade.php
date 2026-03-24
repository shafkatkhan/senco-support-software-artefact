<div class="modal fade" id="{{ $id ?? 'delete' }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="post" id="{{ ($id ?? 'delete') . 'Form' }}">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ $action ?? __('Delete') }} {{ ucwords($type) }}: <span id="{{ ($id ?? 'delete') . '_modal_name' }}"></span></h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    {{ __('Are you sure you want to :action this :type?', ['action' => strtolower($action ?? __('delete')), 'type' => strtolower($type)]) }}
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-danger">{{ $action ?? __('Delete') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="content_top_buttons justify-content-between">
    <div class="section_title">
        <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> {{ __('Return back to pupils') }}
    </div>
    <div style="display: flex; gap: 10px; justify-content: flex-end;">
        @can('export-pupil-data')
        <div class="dropdown">
            <button class="top_button export_button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-file-export"></i> {{ __('Export') }}
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="{{ route('pupils.'.$route_name.'.export', ['pupil' => $pupil->id, 'format' => 'excel']) }}">
                        <i class="fas fa-file-excel text-success"></i> {{ __('Export to Excel') }}
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="{{ route('pupils.'.$route_name.'.export', ['pupil' => $pupil->id, 'format' => 'csv']) }}">
                        <i class="fas fa-file-csv text-muted"></i> {{ __('Export to CSV') }}
                    </a>
                </li>
            </ul>
        </div>
        @endcan
        <button type="button" class="top_button toggle_button" id="toggleViewBtn">
            {{ __('Toggle Card View') }}
        </button>
        @can('create-'.$route_name)
        <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
            {{ $new_button_text }}
        </button> 
        @endcan
    </div>
</div>
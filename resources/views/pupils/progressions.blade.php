@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
            <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Progression
                </button> 
            </div>            
        </div>

        <div class="table_wrap">
            <div class="settings_wrap dashboard auto_progression_wrap {{ !$progression_configured ? 'disabled_option' : '' }}">
                <div class="settings_section">
                    <form action="{{ route('pupils.toggle-auto-progression', $pupil->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div>
                            <strong>
                                Automatic Progression
                                @if(!$progression_configured)
                                    <span class="badge bg-danger">Progression Settings Not Configured</span>
                                @endif
                            </strong>
                            <div class="text-muted" style="font-size: 0.85rem; @if(!$progression_configured) margin-top: 10px; @endif">
                                @if($progression_configured)
                                    When enabled, this pupil's year group will automatically increment during the next annual rollover date: {{ $progression_update_date }}.
                                @else
                                    System progression settings must be configured before auto-progression can be enabled.
                                @endif
                            </div>
                        </div>
                        <div class="form-check form-switch" style="font-size: 1.5rem; @if(!$progression_configured) margin-top: 10px @endif">
                            <input class="form-check-input" type="checkbox" role="switch" name="auto_progression" value="1" {{ $pupil->auto_progression ? 'checked' : '' }} onchange="this.form.submit()" {{ !$progression_configured ? 'disabled' : '' }}>
                        </div>
                    </form>
                </div>
            </div>
            <table class="table sen_table-striped" data-order='[[ 0, "desc" ]]'>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Academic Year</th>
                        <th scope="col">Year Group</th>
                        <th scope="col">Tutor Group</th>
                        <th scope="col">Type</th>
                        @can('edit-pupils')
                        <th scope="col">Actions</th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->progressions as $progression)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $progression->academic_year }}</td>
                            <td>Year {{ $progression->year_group }}</td>
                            <td>{{ $progression->tutor_group ?? 'N/A' }}</td>
                            <td>
                                @if($progression->type == 'initial')
                                    <span class="badge bg-secondary">Initial</span>
                                @elseif($progression->type == 'auto')
                                    <span class="badge bg-success">Auto</span>
                                @else
                                    <span class="badge bg-info text-dark">Manual</span>
                                @endif
                            </td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('pupil-progressions.update', $progression->id) }}"
                                    data-academic_year="{{ $progression->academic_year }}"
                                    data-year_group="{{ $progression->year_group }}"
                                    data-tutor_group="{{ $progression->tutor_group }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('pupil-progressions.destroy', $progression->id) }}" 
                                    data-name="{{ $progression->academic_year }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty_table_message">No progression history found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Progression</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pupil-progressions.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Academic Year*</label>
                            <input type="text" class="form-control" name="academic_year" required pattern="\d{4}/\d{4}" title="Please enter in YYYY/YYYY format (e.g. @php echo date('Y') . '/' . (date('Y') + 1); @endphp)" placeholder="e.g. @php echo date('Y') . '/' . (date('Y') + 1); @endphp">
                        </div>
                        <div class="form-group mb-3">
                            <label>Year Group*</label>
                            <input type="number" class="form-control" name="year_group" required min="1" placeholder="e.g. 11">
                        </div>
                        <div class="form-group mb-3">
                            <label>Tutor Group</label>
                            <input type="text" class="form-control" name="tutor_group" placeholder="e.g. 11C">
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Progression</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Academic Year*</label>
                            <input type="text" class="form-control" name="academic_year" id="edit_academic_year" required pattern="\d{4}/\d{4}" title="Please enter in YYYY/YYYY format (e.g. @php echo date('Y') . '/' . (date('Y') + 1); @endphp)" placeholder="e.g. @php echo date('Y') . '/' . (date('Y') + 1); @endphp">
                        </div>
                        <div class="form-group mb-3">
                            <label>Year Group*</label>
                            <input type="number" class="form-control" name="year_group" id="edit_year_group" required min="1" placeholder="e.g. 11">
                        </div>
                        <div class="form-group mb-3">
                            <label>Tutor Group</label>
                            <input type="text" class="form-control" name="tutor_group" id="edit_tutor_group" placeholder="e.g. 11C">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'Progression History'])
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);

        $('#edit_academic_year').val($(this).data('academic_year'));
        $('#edit_year_group').val($(this).data('year_group'));
        $('#edit_tutor_group').val($(this).data('tutor_group'));
    });
</script>
@endpush

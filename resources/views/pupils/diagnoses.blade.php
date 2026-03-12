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
                @can('create-diagnoses')
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Diagnosis
                </button> 
                @endcan
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->diagnoses as $diagnosis)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $diagnosis->name }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-diagnoses')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('diagnoses.update', $diagnosis->id) }}" 
                                data-date="{{ optional($diagnosis->date)->format('Y-m-d') }}" 
                                data-name="{{ $diagnosis->name }}" 
                                data-professional_id="{{ $diagnosis->professional_id }}"
                                data-description="{{ $diagnosis->description }}"
                                data-recommendations="{{ $diagnosis->recommendations }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-diagnoses')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('diagnoses.destroy', $diagnosis->id) }}" 
                                data-name="{{ $diagnosis->name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Date Diagnosed:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ optional($diagnosis->date)->format('d/m/Y') ?? 'N/A' }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Carried Out By:</div>
                                <div class="value">
                                    {{ $diagnosis->professional ? $diagnosis->professional->title . ' ' . $diagnosis->professional->first_name . ' ' . $diagnosis->professional->last_name : 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Description:</div>
                                <div class="value">
                                    {{ $diagnosis->description ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Recommendations:</div>
                                <div class="value">
                                    {{ $diagnosis->recommendations ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $diagnosis->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $diagnosis->updated_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No diagnoses found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Date Diagnosed</th>
                        <th scope="col">Carried Out By</th>
                        <th scope="col">Description</th>
                        <th scope="col">Recommendations</th>
                        @canany(['edit-diagnoses', 'delete-diagnoses'])
                        <th scope="col">Actions</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->diagnoses as $diagnosis)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $diagnosis->name }}</td>
                            <td data-order="{{ optional($diagnosis->date)->format('Y-m-d') ?? '' }}">{{ optional($diagnosis->date)->format('d/m/Y') }}</td>
                            <td>{{ $diagnosis->professional ? $diagnosis->professional->title . ' ' . $diagnosis->professional->first_name . ' ' . $diagnosis->professional->last_name : 'N/A' }}</td>
                            <td>{{ $diagnosis->description }}</td>
                            <td>{{ $diagnosis->recommendations }}</td>
                            @canany(['edit-diagnoses', 'delete-diagnoses'])
                            <td class="icon_wrap">
                                @can('edit-diagnoses')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('diagnoses.update', $diagnosis->id) }}" 
                                    data-date="{{ optional($diagnosis->date)->format('Y-m-d') }}" 
                                    data-name="{{ $diagnosis->name }}" 
                                    data-professional_id="{{ $diagnosis->professional_id }}"
                                    data-description="{{ $diagnosis->description }}"
                                    data-recommendations="{{ $diagnosis->recommendations }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-diagnoses')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('diagnoses.destroy', $diagnosis->id) }}" 
                                    data-name="{{ $diagnosis->name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-diagnoses', 'delete-diagnoses']) ? '7' : '6' }}" class="empty_table_message">No diagnoses found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-diagnoses')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Diagnosis</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('diagnoses.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        <div class="file_extraction_box">
                            <div class="upload_icon"><i class="fas fa-upload"></i></div>
                            <div class="label">
                                Click to upload, or drag & drop a file here for smart data extraction
                            </div>
                            <div class="filename"></div>
                            <button type="button" disabled>
                                Extract Data
                            </button>
                            <div class="status"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" required placeholder="Diagnosis Name">
                        </div>
                         <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Date Diagnosed</label>
                                <input type="date" class="form-control" name="date">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                @include('components.inline_professional_form')
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>Description</label>
                             <textarea class="form-control" name="description" rows="3" placeholder="Description of the diagnosis..."></textarea>
                        </div>
                         <div class="form-group mb-3">
                            <label>Recommendations</label>
                            <textarea class="form-control" name="recommendations" rows="3" placeholder="Recommended actions..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-diagnoses')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Edit Diagnosis</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required placeholder="Diagnosis Name">
                        </div>
                         <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Date</label>
                                <input type="date" class="form-control" name="date" id="edit_date">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Carried Out By (Professional)</label>
                                <select class="form-select" name="professional_id" id="edit_professional_id">
                                    <option value="">None / Not Applicable</option>
                                    @foreach($professionals as $prof)
                                        <option value="{{ $prof->id }}">{{ $prof->title }} {{ $prof->first_name }} {{ $prof->last_name }}{{ $prof->role ? ' (' . $prof->role . ')' : '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>                        
                        <div class="form-group mb-3">
                            <label>Description</label>
                             <textarea class="form-control" name="description" id="edit_description" rows="3" placeholder="Description of the diagnosis..."></textarea>
                        </div>
                         <div class="form-group mb-3">
                            <label>Recommendations</label>
                            <textarea class="form-control" name="recommendations" id="edit_recommendations" rows="3" placeholder="Recommended actions..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-diagnoses')
    @include('components.delete_modal', ['type' => 'Diagnosis'])
    @endcan
@endsection

@push('scripts')
<script>
    // edit modal population
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_date').val($(this).data('date'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_professional_id').val($(this).data('professional_id'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_recommendations').val($(this).data('recommendations'));
    });

    // file extraction
    $('.file_extraction_box button').on('click', function () {
        if (!selectedFile) return;

        var formData = new FormData();
        formData.append('file', selectedFile);
        formData.append('_token', '{{ csrf_token() }}');

        var btn = $(this);
        btn.prop('disabled', true);
        $('.file_extraction_box .status').html('<span class="spinner-border" role="status"></span> Extracting data...').removeClass('text-danger text-success');

        $.ajax({
            url: '{{ route("diagnoses.extract-file") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                var d = response.data;
                if (d.name) $('input[name="name"]').val(d.name);
                if (d.date) $('input[name="date"]').val(d.date);
                if (d.description) $('textarea[name="description"]').val(d.description);
                if (d.recommendations) $('textarea[name="recommendations"]').val(d.recommendations);

                // populate new professional if any professional fields detected
                var hasProf = d.prof_first_name || d.prof_last_name || d.prof_role;
                if (hasProf) {
                    if ($('#is_new_professional').val() !== '1') {
                        $('#toggle_professional_btn').click();
                    }
                    if (d.prof_title) $('input[name="prof_title"]').val(d.prof_title);
                    if (d.prof_first_name) $('input[name="prof_first_name"]').val(d.prof_first_name);
                    if (d.prof_last_name) $('input[name="prof_last_name"]').val(d.prof_last_name);
                    if (d.prof_role) $('input[name="prof_role"]').val(d.prof_role);
                    if (d.prof_agency) $('input[name="prof_agency"]').val(d.prof_agency);
                    if (d.prof_phone) $('input[name="prof_phone"]').val(d.prof_phone);
                    if (d.prof_email) $('input[name="prof_email"]').val(d.prof_email);
                }

                $('.file_extraction_box .status').text('Fields populated successfully.').addClass('text-success');
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.error) ? xhr.responseJSON.error : 'Extraction failed.';
                $('.file_extraction_box .status').text(msg).addClass('text-danger');
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
</script>
@endpush

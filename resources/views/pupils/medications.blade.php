@extends('layouts.app')

@section('content')
    <section id="content">
        <div style="display: flex; justify-content: space-between;">
           <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas fa-arrow-circle-left"></i></a> Return back to pupils
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" id="toggleViewBtn" style="background-color: #5388b6;">
                    Toggle Card View
                </button>
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add New Medication
                </button> 
            </div>            
        </div>

        <div id="medicationsGrid" class="sen_cards" style="display: none;">
            @foreach($pupil->medications as $medication)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $medication->name }}
                        </div>
                        <div class="sen_icon_wrap">
                            <button class="sen_icon sen_edit_icon edit_icon" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('medications.update', $medication->id) }}" 
                                data-name="{{ $medication->name }}" 
                                data-dosage="{{ $medication->dosage }}" 
                                data-frequency="{{ $medication->frequency }}"
                                data-time_of_day="{{ $medication->time_of_day }}"
                                data-administration_method="{{ $medication->administration_method }}"
                                data-start_date="{{ $medication->start_date->format('Y-m-d') }}"
                                data-end_date="{{ $medication->end_date ? $medication->end_date->format('Y-m-d') : '' }}"
                                data-expiry_date="{{ $medication->expiry_date ? $medication->expiry_date->format('Y-m-d') : '' }}"
                                data-storage_instructions="{{ $medication->storage_instructions }}"
                                data-self_administer="{{ $medication->self_administer }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            <button class="sen_icon sen_delete_icon delete_icon" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('medications.destroy', $medication->id) }}" 
                                data-name="{{ $medication->name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Dosage:</div>
                                <div class="value">
                                    {{ $medication->dosage }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Frequency:</div>
                                <div class="value">
                                    {{ $medication->frequency }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Time of Day:</div>
                                <div class="value">
                                    {{ $medication->time_of_day }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Administration Method:</div>
                                <div class="value">
                                    {{ $medication->administration_method }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Start Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->start_date->format('d/m/Y') }}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">End Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->end_date ? $medication->end_date->format('d/m/Y') : 'N/A'}}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-6 border_right-md">
                                <div class="label">Expiry Date:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->expiry_date ? $medication->expiry_date->format('d/m/Y') : 'N/A'}}
                                </div>
                            </div>
                            <div class="item col-md-6">
                                <div class="label">Self Adminster?</div>
                                <div class="value">
                                    {{ $medication->self_adminster ? 'Yes' : 'No' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Storage Instructions:</div>
                                <div class="value">
                                    {{ $medication->storage_instructions ?? 'N/A' }}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">Last Edited:</div>
                                <div class="value">
                                    <i class="far fa-calendar-alt"></i>
                                    {{ $medication->updated_at->format('d/m/Y') }}
                                    <div class="gap"></div>
                                    <i class="far fa-clock"></i>
                                    {{ $medication->updated_at->format('H:i') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="medicationsTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Dosage</th>
                        <th scope="col">Frequency</th>
                        <th scope="col">Time of Day</th>
                        <th scope="col">Administration Method</th>
                        <th scope="col">Start Date</th>
                        <th scope="col">End Date</th>
                        <th scope="col">Expiry Date</th>
                        <th scope="col">Storage Instructions</th>
                        <th scope="col">Self Administer?</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pupil->medications as $medication)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $medication->name }}</td>
                            <td>{{ $medication->dosage }}</td>
                            <td>{{ $medication->frequency }}</td>
                            <td>{{ $medication->time_of_day }}</td>
                            <td>{{ $medication->administration_method }}</td>
                            <td>{{ $medication->start_date->format('d/m/Y') }}</td>
                            <td>{{ $medication->end_date ? $medication->end_date->format('d/m/Y') : 'N/A'}}</td>
                            <td>{{ $medication->expiry_date ? $medication->expiry_date->format('d/m/Y') : 'N/A'}}</td>
                            <td>{{ $medication->storage_instructions }}</td>
                            <td>{{ $medication->self_administer ? 'Yes' : 'No' }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('medications.update', $medication->id) }}" 
                                    data-name="{{ $medication->name }}" 
                                    data-dosage="{{ $medication->dosage }}" 
                                    data-frequency="{{ $medication->frequency }}"
                                    data-time_of_day="{{ $medication->time_of_day }}"
                                    data-administration_method="{{ $medication->administration_method }}"
                                    data-start_date="{{ $medication->start_date->format('Y-m-d') }}"
                                    data-end_date="{{ $medication->end_date ? $medication->end_date->format('Y-m-d') : '' }}"
                                    data-expiry_date="{{ $medication->expiry_date ? $medication->expiry_date->format('Y-m-d') : '' }}"
                                    data-storage_instructions="{{ $medication->storage_instructions }}"
                                    data-self_administer="{{ $medication->self_administer }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('medications.destroy', $medication->id) }}" 
                                    data-name="{{ $medication->name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">Add New Medication</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('medications.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" required placeholder="Medication Name">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Dosage</label>
                                <input type="text" class="form-control" name="dosage" placeholder="e.g. 50mg">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Frequency*</label>
                                <input type="text" class="form-control" name="frequency" required placeholder="e.g. Twice Daily, As Needed">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Time of Day</label>
                                <input type="text" class="form-control" name="time_of_day" placeholder="e.g. Morning, Night, 1:30pm">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Method</label>
                                <input type="text" class="form-control" name="administration_method" placeholder="e.g. Oral, Injection">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Start Date*</label>
                                <input type="date" class="form-control" name="start_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Storage Instructions</label>
                            <textarea class="form-control" name="storage_instructions" rows="2"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input type="hidden" name="self_administer" value="0">
                            <input type="checkbox" class="form-check-input" name="self_administer" value="1" id="create_self_administer">
                            <label class="form-check-label" for="create_self_administer">Self Administer?</label>
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
                    <h1 class="modal-title fs-5">Edit Medication</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                         <div class="form-group mb-3">
                            <label>Name*</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Dosage</label>
                                <input type="text" class="form-control" name="dosage" id="edit_dosage">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Frequency*</label>
                                <input type="text" class="form-control" name="frequency" id="edit_frequency" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>Time of Day</label>
                                <input type="text" class="form-control" name="time_of_day" id="edit_time_of_day">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>Method</label>
                                <input type="text" class="form-control" name="administration_method" id="edit_administration_method">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 form-group mb-3">
                                <label>Start Date*</label>
                                <input type="date" class="form-control" name="start_date" id="edit_start_date" required>
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>End Date</label>
                                <input type="date" class="form-control" name="end_date" id="edit_end_date">
                            </div>
                            <div class="col-md-4 form-group mb-3">
                                <label>Expiry Date</label>
                                <input type="date" class="form-control" name="expiry_date" id="edit_expiry_date">
                            </div>
                        </div>
                        <div class="form-group mb-3">
                            <label>Storage Instructions</label>
                            <textarea class="form-control" name="storage_instructions" id="edit_storage_instructions" rows="2"></textarea>
                        </div>
                        <div class="form-check mb-3">
                            <input type="hidden" name="self_administer" value="0">
                            <input type="checkbox" class="form-check-input" name="self_administer" value="1" id="edit_self_administer">
                            <label class="form-check-label" for="edit_self_administer">Self Administer?</label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'Medication'])
@endsection

@push('scripts')
<script>
    $(document).on('click', '.edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_name').val($(this).data('name'));
        $('#edit_dosage').val($(this).data('dosage'));
        $('#edit_frequency').val($(this).data('frequency'));
        $('#edit_time_of_day').val($(this).data('time_of_day'));
        $('#edit_administration_method').val($(this).data('administration_method'));
        $('#edit_start_date').val($(this).data('start_date'));
        $('#edit_end_date').val($(this).data('end_date'));
        $('#edit_expiry_date').val($(this).data('expiry_date'));
        $('#edit_storage_instructions').val($(this).data('storage_instructions'));
        
        var selfAdmin = $(this).data('self_administer');
        $('#edit_self_administer').prop('checked', selfAdmin == 1);
    });

    $(document).ready(function() {
        $('#toggleViewBtn').click(function() {
            if ($('#medicationsTable').is(':visible')) {
                $('#medicationsTable').hide();
                $('#medicationsGrid').css('display', 'flex');
                $('#toggleViewBtn').text('Toggle Table View');
            } else {
                $('#medicationsTable').show();
                $('#medicationsGrid').hide();
                $('#toggleViewBtn').text('Toggle Card View');
            }
        });
    });
</script>
@endpush
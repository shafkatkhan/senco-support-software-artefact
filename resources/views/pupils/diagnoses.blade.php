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
                    Add New Diagnosis
                </button> 
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @foreach($pupil->diagnoses as $diagnosis)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $diagnosis->name }}
                        </div>
                        <div class="sen_icon_wrap">
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('diagnoses.update', $diagnosis->id) }}" 
                                data-date="{{ optional($diagnosis->date)->format('Y-m-d') }}" 
                                data-name="{{ $diagnosis->name }}" 
                                data-carried_out_by="{{ $diagnosis->carried_out_by }}"
                                data-description="{{ $diagnosis->description }}"
                                data-recommendations="{{ $diagnosis->recommendations }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('diagnoses.destroy', $diagnosis->id) }}" 
                                data-name="{{ $diagnosis->name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
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
                                    {{ $diagnosis->carried_out_by ?? 'N/A' }}
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
            @endforeach
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
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pupil->diagnoses as $diagnosis)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $diagnosis->name }}</td>
                            <td>{{ optional($diagnosis->date)->format('d/m/Y') }}</td>
                            <td>{{ $diagnosis->carried_out_by }}</td>
                            <td>{{ $diagnosis->description }}</td>
                            <td>{{ $diagnosis->recommendations }}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('diagnoses.update', $diagnosis->id) }}" 
                                    data-date="{{ optional($diagnosis->date)->format('Y-m-d') }}" 
                                    data-name="{{ $diagnosis->name }}" 
                                    data-carried_out_by="{{ $diagnosis->carried_out_by }}"
                                    data-description="{{ $diagnosis->description }}"
                                    data-recommendations="{{ $diagnosis->recommendations }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('diagnoses.destroy', $diagnosis->id) }}" 
                                    data-name="{{ $diagnosis->name }}"
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
                    <h1 class="modal-title fs-5">Add New Diagnosis</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('diagnoses.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
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
                                <label>Carried Out By</label>
                                <input type="text" class="form-control" name="carried_out_by" placeholder="e.g. Dr. Smith">
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
                                <label>Carried Out By</label>
                                <input type="text" class="form-control" name="carried_out_by" id="edit_carried_out_by" placeholder="e.g. Dr. Smith">
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

    @include('components.delete_modal', ['type' => 'Diagnosis'])
@endsection

@push('scripts')
<script>
    $(document).on('click', '.edit_icon, .sen_edit_icon', function () {
        var url = $(this).data('url');
        $('#editForm').attr('action', url);
        
        $('#edit_date').val($(this).data('date'));
        $('#edit_name').val($(this).data('name'));
        $('#edit_carried_out_by').val($(this).data('carried_out_by'));
        $('#edit_description').val($(this).data('description'));
        $('#edit_recommendations').val($(this).data('recommendations'));
    });
</script>
@endpush

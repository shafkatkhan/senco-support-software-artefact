@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
            <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> Return back to pupils
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add Diet Entry
                </button>
            </div>
        </div>

        <div class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Proficiency</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->diets as $diet)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $diet->subject->name}}</td>
                            <td>{!! $diet->proficiency?->name ?? '<span class="text-muted">N/A</span>' !!}</td>
                            <td class="icon_wrap">
                                <button class="icon edit_icon"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit"
                                    data-url="{{ route('diets.update', $diet->id) }}"
                                    data-subject_id="{{ $diet->subject_id }}"
                                    data-proficiency_id="{{ $diet->proficiency_id }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="icon delete_icon"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete"
                                    data-url="{{ route('diets.destroy', $diet->id) }}"
                                    data-name="{{ $diet->subject->name . ($diet->proficiency ? ' (' . $diet->proficiency->name . ')' : '') }}">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty_table_message">No diet entries found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
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
                    <h1 class="modal-title fs-5">Add Diet Entry</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('diets.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Subject*</label>
                            <select name="subject_id" id="new_subject_id" class="form-control" required>
                                <option value="" disabled selected>--- Choose Subject ---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3" id="new_proficiency_group" style="display: none;">
                            <label>Proficiency*</label>
                            <select name="proficiency_id" id="new_proficiency_id" class="form-control">
                            </select>
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
                    <h1 class="modal-title fs-5">Edit Diet Entry</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>Subject*</label>
                            <select name="subject_id" id="edit_subject_id" class="form-control" required>
                                <option value="" disabled>--- Choose Subject ---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3" id="edit_proficiency_group" style="display: none;">
                            <label>Proficiency*</label>
                            <select name="proficiency_id" id="edit_proficiency_id" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'Diet Entry'])
@endsection

@push('scripts')
<script>
    const subjectsData = {!! $subjects->toJson() !!};

    function filterProficiencies(formPrefix, currentProficiencyId = null) {
        const subjectId = $(`#${formPrefix}_subject_id`).val();
        const $group = $(`#${formPrefix}_proficiency_group`);
        const $select = $(`#${formPrefix}_proficiency_id`);
        
        const subject = subjectsData.find(s => s.id == subjectId);
        const hasProficiencies = subject && subject.proficiencies.length > 0;

        if (hasProficiencies) {
            $select.empty().append('<option value="" disabled selected>--- Choose Proficiency ---</option>');
            subject.proficiencies.forEach(p => {
                $select.append(`<option value="${p.id}" ${p.id == currentProficiencyId ? 'selected' : ''}>${p.name}</option>`);
            });
            $select.attr('required', true);
            $group.show();
        } else {
            $select.empty().attr('required', false);
            $group.hide();
        }
    }

    $(document).on('change', '#new_subject_id', function () {
        filterProficiencies('new');
    });

    $(document).on('change', '#edit_subject_id', function () {
        filterProficiencies('edit', $('#edit_proficiency_id').val());
    });

    $(document).on('click', '.edit_icon', function () {
        const subjectId = $(this).data('subject_id');
        const proficiencyId = $(this).data('proficiency_id');
        
        $('#editForm').attr('action', $(this).data('url'));
        $('#edit_subject_id').val(subjectId);
        filterProficiencies('edit', proficiencyId);
    });
</script>
@endpush

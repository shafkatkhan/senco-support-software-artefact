@extends('layouts.app')

@section('content')
    <section id="content">
        @include('components.pupil_page_top_header', [
            'route_name' => 'diets',
            'new_button_text' => __('Add Subject to Diet'),
            'create_permission' => 'add-to-diets'
        ])

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->diets as $diet)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $diet->subject->name }}
                        </div>
                        <div class="sen_icon_wrap">
                            @can('edit-diets')
                            <button class="sen_icon sen_edit_icon edit_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#edit" 
                                data-url="{{ route('diets.update', $diet->id) }}"
                                data-subject_id="{{ $diet->subject_id }}"
                                data-proficiency_id="{{ $diet->proficiency_id }}"
                                data-accommodations="{{ $diet->accommodations->toJson() }}"
                                aria-label="{{ __('Edit') }}"
                            >
                                <i class="far fa-edit"></i>
                            </button>
                            @endcan
                            @can('delete-diets')
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('diets.destroy', $diet->id) }}"
                                data-name="{{ $diet->subject->name . ($diet->proficiency ? ' (' . $diet->proficiency->name . ')' : '') }}"
                                aria-label="{{ __('Delete') }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                            @endcan
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-12">
                                <div class="label">{{ __('Proficiency') }}:</div>
                                <div class="value">
                                    {!! $diet->proficiency?->name ?? '<span class="text-muted">'.__('N/A').'</span>' !!}
                                </div>
                            </div>
                            <hr>
                            <div class="item col-md-12">
                                <div class="label">{{ __('Accommodations') }}:</div>
                                <div class="value diet_acc_list">
                                    @forelse($diet->accommodations as $acc)
                                        <div class="diet_acc_row">
                                            <div class="diet_acc_main">
                                                <div class="diet_acc_name">{{ $acc->name }}</div>
                                                <span class="badge {{ $acc->pivot->status === 'Approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                                                    {{ __($acc->pivot->status) }}
                                                </span>
                                            </div>
                                            @if($acc->pivot->details)
                                                <div class="diet_acc_details">{{ $acc->pivot->details }}</div>
                                            @endif
                                        </div>
                                    @empty
                                        <span class="text-muted">{{ __('N/A') }}</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">{{ __('No diet entries found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Subject') }}</th>
                        <th scope="col">{{ __('Proficiency') }}</th>
                        <th scope="col" class="dt-left">{{ __('Accommodations') }}</th>
                        @canany(['edit-diets', 'delete-diets'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->diets as $diet)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $diet->subject->name}}</td>
                            <td>{!! $diet->proficiency?->name ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</td>
                            <td data-order="{{ $diet->accommodations->count() }}" class="dt-left">
                                @forelse($diet->accommodations as $acc)
                                    <span class="badge bg-secondary">{{ $acc->name }} ({{ __($acc->pivot->status) }})</span>
                                @empty
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endforelse
                            </td>
                            @canany(['edit-diets', 'delete-diets'])
                            <td class="icon_wrap">
                                @can('edit-diets')
                                <button class="icon edit_icon"
                                    data-bs-toggle="modal"
                                    data-bs-target="#edit"
                                    data-url="{{ route('diets.update', $diet->id) }}"
                                    data-subject_id="{{ $diet->subject_id }}"
                                    data-proficiency_id="{{ $diet->proficiency_id }}"
                                    data-accommodations="{{ $diet->accommodations->toJson() }}"
                                    aria-label="{{ __('Edit') }}">
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-diets')
                                <button class="icon delete_icon"
                                    data-bs-toggle="modal"
                                    data-bs-target="#delete"
                                    data-url="{{ route('diets.destroy', $diet->id) }}"
                                    data-name="{{ $diet->subject->name . ($diet->proficiency ? ' (' . $diet->proficiency->name . ')' : '') }}"
                                    aria-label="{{ __('Delete') }}">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-diets', 'delete-diets']) ? '5' : '4' }}" class="empty_table_message">{{ __('No diet entries found for :name.', ['name' => $pupil->first_name.' '.$pupil->last_name]) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('add-to-diets')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Add Diet Entry') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('diets.store') }}" method="post">
                    @csrf
                    <input type="hidden" name="pupil_id" value="{{ $pupil->id }}">
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Subject') }}*</label>
                            <select name="subject_id" id="new_subject_id" class="form-control" required>
                                <option value="" disabled selected>--- {{ __('Choose Subject') }} ---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3" id="new_proficiency_group" style="display: none;">
                            <label>{{ __('Proficiency') }}*</label>
                            <select name="proficiency_id" id="new_proficiency_id" class="form-control">
                            </select>
                        </div>
                        <div class="form-group mb-3" id="new_accommodations_wrapper" style="display: none;">
                            <label>{{ __('Accommodations') }}</label>
                            <div id="new_accommodations_container"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addAccommodationRow('new')">
                                <i class="fa fa-plus"></i> {{ __('Add Accommodation') }}
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-diets')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5">{{ __('Edit Diet Entry') }}</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editForm" method="post">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="form-group mb-3">
                            <label>{{ __('Subject') }}*</label>
                            <select name="subject_id" id="edit_subject_id" class="form-control" required>
                                <option value="" disabled>--- {{ __('Choose Subject') }} ---</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-3" id="edit_proficiency_group" style="display: none;">
                            <label>{{ __('Proficiency') }}*</label>
                            <select name="proficiency_id" id="edit_proficiency_id" class="form-control">
                            </select>
                        </div>
                        <div class="form-group mb-3" id="edit_accommodations_wrapper" style="display: none;">
                            <label>{{ __('Accommodations') }}</label>
                            <div id="edit_accommodations_container"></div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addAccommodationRow('edit')">
                                <i class="fa fa-plus"></i> {{ __('Add Accommodation') }}
                            </button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-diets')
    @include('components.delete_modal', ['type' => __('Diet Entry')])
    @endcan
@endsection

@push('scripts')
<script>
    const subjectsData = {!! $subjects->toJson() !!};
    let accommodationRowIndex = 0;

    function filterSubjectOptions(formPrefix, currentProficiencyId = null, currentAccommodations = []) {
        const subjectId = $(`#${formPrefix}_subject_id`).val();
        
        // proficiency logic
        const $profGroup = $(`#${formPrefix}_proficiency_group`);
        const $profSelect = $(`#${formPrefix}_proficiency_id`);
        
        const subject = subjectsData.find(s => s.id == subjectId);
        
        // update proficiencies
        const hasProficiencies = subject && subject.proficiencies.length > 0;
        if (hasProficiencies) {
            $profSelect.empty().append('<option value="" disabled selected>--- ' + __('Choose Proficiency') + ' ---</option>');
            subject.proficiencies.forEach(p => {
                $profSelect.append(`<option value="${p.id}" ${p.id == currentProficiencyId ? 'selected' : ''}>${p.name}</option>`);
            });
            $profSelect.attr('required', true);
            $profGroup.show();
        } else {
            $profSelect.empty().attr('required', false);
            $profGroup.hide();
        }

        // accommodation logic
        const $accWrapper = $(`#${formPrefix}_accommodations_wrapper`);
        const $accContainer = $(`#${formPrefix}_accommodations_container`);
        const hasAccommodations = subject && subject.accommodations.length > 0;

        $accContainer.empty(); // clear existing rows when subject changes or on load

        if (hasAccommodations) {
            $accWrapper.show();
            // fill in existing accommodations (for edit)
            currentAccommodations.forEach(acc => {
                addAccommodationRow(formPrefix, subject.accommodations, acc.id, acc.pivot.status, acc.pivot.details);
            });
        } else {
            $accWrapper.hide();
        }
    }

    function addAccommodationRow(formPrefix, availableAccommodations = null, selectedId = null, selectedStatus = 'Recommended', details = '') {
        if (!availableAccommodations) {
            const subjectId = $(`#${formPrefix}_subject_id`).val();
            const subject = subjectsData.find(s => s.id == subjectId);
            availableAccommodations = subject ? subject.accommodations : [];
        }

        if (availableAccommodations.length === 0) return;

        let options = '<option value="" disabled selected>--- ' + __('Choose Accommodation') + ' ---</option>';
        availableAccommodations.forEach(a => {
            options += `<option value="${a.id}" ${a.id == selectedId ? 'selected' : ''}>${a.name}</option>`;
        });

        const statusRecommended = selectedStatus === 'Recommended' ? 'selected' : '';
        const statusApproved = selectedStatus === 'Approved' ? 'selected' : '';

        const rowHtml = `
            <div class="row align-items-center mb-2 acc-row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <select name="accommodations[${accommodationRowIndex}][id]" class="form-control" required>
                        ${options}
                    </select>
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="accommodations[${accommodationRowIndex}][status]" class="form-control" required>
                        <option value="Recommended" ${statusRecommended}>${__('Recommended')}</option>
                        <option value="Approved" ${statusApproved}>${__('Approved')}</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <textarea name="accommodations[${accommodationRowIndex}][details]" class="form-control" rows="1" placeholder="${__('Details (Optional)')}">${details || ''}</textarea>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="$(this).closest('.acc-row').remove()" aria-label="${__('Remove')}"><i class="fa fa-times"></i></button>
                </div>
            </div>
        `;

        $(`#${formPrefix}_accommodations_container`).append(rowHtml);
        accommodationRowIndex++;
    }

    $(document).on('change', '#new_subject_id', function () {
        filterSubjectOptions('new');
    });

    $(document).on('change', '#edit_subject_id', function () {
        // if subject changes manually in edit, wipe existing accommodations by passing empty array
        filterSubjectOptions('edit', $('#edit_proficiency_id').val(), []);
    });

    $(document).on('click', '.edit_icon', function () {
        const subjectId = $(this).data('subject_id');
        const proficiencyId = $(this).data('proficiency_id');
        const subjectData = subjectsData.find(s => s.id == subjectId);
        const dietAccommodationsStr = $(this).attr('data-accommodations');
        const accommodations = dietAccommodationsStr ? JSON.parse(dietAccommodationsStr) : [];
        
        $('#editForm').attr('action', $(this).data('url'));
        $('#edit_subject_id').val(subjectId);
        filterSubjectOptions('edit', proficiencyId, accommodations);
    });
</script>
@endpush

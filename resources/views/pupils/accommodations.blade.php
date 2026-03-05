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
                <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                    Add Accommodation
                </button> 
            </div>            
        </div>

        <div id="toggleViewGrid" class="sen_cards" style="display: none;">
            @forelse($pupil->accommodations as $accommodation)
                <div class="sen_card">
                    <div class="top">
                        <div class="label">
                            {{ $accommodation->name }}
                        </div>
                        <div class="sen_icon_wrap">
                            <button class="sen_icon sen_delete_icon delete_icon button_styled" 
                                data-bs-toggle="modal" 
                                data-bs-target="#delete" 
                                data-url="{{ route('pupils.accommodations.destroy', ['pupil' => $pupil->id, 'accommodation' => $accommodation->id]) }}" 
                                data-name="{{ $accommodation->name }}"
                            >
                                <i class="far fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="bottom">
                        <div class="row">
                            <div class="item col-md-12">
                                <div class="label">Description:</div>
                                <div class="value">
                                    {{ $accommodation->description ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty_grid_message">No accommodations found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</div>
            @endforelse
        </div>

        <div id="toggleViewTable" class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pupil->accommodations as $accommodation)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $accommodation->name }}</td>
                            <td>{{ $accommodation->description }}</td>
                            <td class="icon_wrap">
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('pupils.accommodations.destroy', ['pupil' => $pupil->id, 'accommodation' => $accommodation->id]) }}" 
                                    data-name="{{ $accommodation->name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="empty_table_message">No accommodations found for {{ $pupil->first_name }} {{ $pupil->last_name }}.</td>
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
                    <h1 class="modal-title fs-5">Add New Accommodation</h1>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('pupils.accommodations.store', $pupil->id) }}" method="post">
                    @csrf
                    <div class="modal-body">
                        @if ($availableAccommodations->isEmpty())
                            <p>All available accommodations have already been added to this pupil, or no accommodations exist in the system.</p>
                        @else
                            <div class="form-group mb-3">
                                <label>Select Accommodation*</label>
                                <select name="accommodation_id" class="form-control" required>
                                    <option value="" disabled>-- Choose Accommodation --</option>
                                    @foreach($availableAccommodations as $available)
                                        <option value="{{ $available->id }}">{{ $available->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        @if (!$availableAccommodations->isEmpty())
                            <button type="submit" class="btn btn-success">Save</button>
                        @else
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    @include('components.delete_modal', ['type' => 'Accommodation', 'action' => 'Remove'])
@endsection
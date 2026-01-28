@extends('layouts.app')

@section('content')
    <section id="content">
        <div style="display: flex; justify-content: space-between;">
           <div class="section_title">
                <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas fa-arrow-circle-left"></i></a> Return back to pupils
            </div>
            <button type="button" class="new_button" data-bs-toggle="modal" data-bs-target="#new">
                Add New Medication
            </button> 
        </div>
        <div class="table_wrap">
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
                                <button class="icon edit_icon"><i class="fa fa-edit"></i></button>
                                <button class="icon delete_icon"><i class="fa fa-trash-alt"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
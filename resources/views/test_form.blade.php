@extends('layouts.app')

@section('content')
    <section id="content">
        <span style="font-size: 15px; font-family: 'Cabin', sans-serif;">
            This page shows a test form. When uploaded with a basic interview asking for participant's name, current date, and current city, it transcribes and populates the table.
        </span>
        <div class="select_audio_wrap">
            <form action="{{ route('test-form.store') }}" method="post" enctype="multipart/form-data">
                @csrf
                <label>Select Audio File:</label>
                <input type="file" name="audioFile" accept="audio/*" required>
                <button type="submit">Upload</button>
            </form>
        </div>
        <div class="table_wrap">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">First Name</th>
                        <th scope="col">Last Name</th>
                        <th scope="col">Date</th>
                        <th scope="col">City</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($test_rows as $test)
                        <tr>
                            <th scope="row">{{ $test->id }}</th>
                            <td>{{ $test->first_name }}</td>
                            <td>{{ $test->last_name }}</td>
                            <td>{{ $test->date }}</td>
                            <td>{{ $test->city }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
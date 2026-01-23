@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="select_audio_wrap">
            <form action="" method="post" enctype="multipart/form-data">
                <label>Select Audio File:</label>
                <input type="file" name="audioFile" accept="audio/*" required>
                <button type="submit">Upload</button>
            </form>
        </div>
        <div class="classes">
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
                    
                </tbody>
            </table>
        </div>
    </section>
@endsection
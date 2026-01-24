@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="table_wrap">
            <table class="table table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user_groups as $user_group)
                        <tr>
                            <th scope="row">{{ $user_group->id }}</th>
                            <td>{{ $user_group->name }}</td>
                            <td>{{ $user_group->description }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endsection
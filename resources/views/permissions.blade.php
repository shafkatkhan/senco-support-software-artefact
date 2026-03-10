@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="table_wrap">
            <form action="{{ route('permissions.update') }}" method="POST">
                @csrf
                <table class="table sen_table-striped" data-page-length="100">
                    <thead class="thead-dark">
                        <tr>
                            <th scope="col" class="dt-left">{{ __('Permission') }}</th>
                            @foreach($userGroups as $userGroup)
                                <th scope="col" class="text-center">{{ $userGroup->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td data-order="{{ $permission->id }}" class="dt-left">
                                    <strong>{{ $permission->name }}</strong><br>
                                    <small class="text-muted">{{ $permission->description }}</small>
                                </td>
                                @foreach($userGroups as $userGroup)
                                    @php
                                        $hasPermission = $userGroup->permissions->contains($permission->id);
                                    @endphp
                                    <td class="text-center align-middle">
                                        <input type="checkbox" name="permissions[{{ $userGroup->id }}][]" value="{{ $permission->id }}" {{ $hasPermission ? 'checked' : '' }}>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-success">{{ __('Save Permissions') }}</button>
                </div>
            </form>
        </div>
    </section>
@endsection

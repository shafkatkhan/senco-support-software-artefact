@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            <form action="{{ route('backups.store') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="new_button" onclick="return confirm('{{ __('Are you sure you want to create a new backup? This may take a moment.') }}')">
                    {{ __('Create Backup') }}
                </button>
            </form>
        </div>

        <div class="table_wrap">
            <table class="table sen_table-striped" data-order='[[ 0, "desc" ]]'>
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('File Name') }}</th>
                        <th scope="col">{{ __('File Size') }}</th>
                        <th scope="col">{{ __('Last Modified') }}</th>
                        <th scope="col">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($backups as $index => $backup)
                        <tr>
                            <th scope="row">{{ $index + 1 }}</th>
                            <td>{{ $backup['file_name'] }}</td>
                            <td>{{ $backup['file_size'] }}</td>
                            <td data-order="{{ $backup['last_modified'] }}">{{ date('d M Y \a\t H:i', $backup['last_modified']) }}</td>
                            <td class="icon_wrap">
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('backups.destroy', ['file_path' => urlencode($backup['relative_path'])]) }}" 
                                    data-name="{{ $backup['file_name'] }}">
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">{{ __('No backups found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @include('components.delete_modal', ['type' => 'Backup'])
@endsection
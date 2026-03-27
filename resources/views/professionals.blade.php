@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons">
            @can('create-professionals')
            <button type="button" class="top_button" data-bs-toggle="modal" data-bs-target="#new">
                {{ __('Add New Professional') }}
            </button>
            @endcan
        </div>
        <div class="table_wrap">
            <table class="table sen_table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{ __('Name') }}</th>
                        <th scope="col">{{ __('Role') }}</th>
                        <th scope="col">{{ __('Agency') }}</th>
                        <th scope="col">{{ __('Contact') }}</th>
                        @canany(['edit-professionals', 'delete-professionals'])
                        <th scope="col">{{ __('Actions') }}</th>
                        @endcanany
                    </tr>
                </thead>
                <tbody>
                    @forelse($professionals as $professional)
                        <tr>
                            <th scope="row">{{ $loop->iteration }}</th>
                            <td>{{ $professional->title }} {{ $professional->first_name }} {{ $professional->last_name }}</td>
                            <td>{!! $professional->role ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</td>
                            <td>{!! $professional->agency ?? '<span class="text-muted">'.__('N/A').'</span>' !!}</td>
                            <td>
                                @if($professional->phone)
                                    <div><i class="fas fa-phone"></i> {{ $professional->phone }}</div>
                                @endif
                                @if($professional->email)
                                    <div><i class="fas fa-envelope"></i> <a href="mailto:{{ $professional->email }}">{{ $professional->email }}</a></div>
                                @endif
                                @if(!$professional->phone && !$professional->email)
                                    <span class="text-muted">{{ __('N/A') }}</span>
                                @endif
                            </td>
                            @canany(['edit-professionals', 'delete-professionals'])
                            <td class="icon_wrap">
                                @can('edit-professionals')
                                <button class="icon edit_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#edit" 
                                    data-url="{{ route('professionals.update', $professional->id) }}" 
                                    data-title="{{ $professional->title }}" 
                                    data-first_name="{{ $professional->first_name }}" 
                                    data-last_name="{{ $professional->last_name }}" 
                                    data-role="{{ $professional->role }}" 
                                    data-agency="{{ $professional->agency }}" 
                                    data-phone="{{ $professional->phone }}" 
                                    data-email="{{ $professional->email }}"
                                >
                                    <i class="fa fa-edit"></i>
                                </button>
                                @endcan
                                @can('delete-professionals')
                                <button class="icon delete_icon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#delete" 
                                    data-url="{{ route('professionals.destroy', $professional->id) }}" 
                                    data-name="{{ $professional->title }} {{ $professional->first_name }} {{ $professional->last_name }}"
                                >
                                    <i class="fa fa-trash-alt"></i>
                                </button>
                                @endcan
                            </td>
                            @endcanany
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->canAny(['edit-professionals', 'delete-professionals']) ? '6' : '5' }}" class="empty_table_message">{{ __('No professionals found.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    @can('create-professionals')
    <div class="modal fade" id="new" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="{{ route('professionals.store') }}" method="post"> 
                    @csrf
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Add New Professional') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-2 form-group mb-3">
                                <label>{{ __('Title') }}</label>
                                <input type="text" class="form-control" name="title" placeholder="{{ __('Dr, Mr, etc.') }}" />
                            </div>
                            <div class="col-md-5 form-group mb-3">
                                <label>{{ __('First Name') }}*</label>
                                <input type="text" class="form-control" name="first_name" placeholder="{{ __('First Name') }}" required />
                            </div>
                            <div class="col-md-5 form-group mb-3">
                                <label>{{ __('Last Name') }}*</label>
                                <input type="text" class="form-control" name="last_name" placeholder="{{ __('Last Name') }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Role') }}</label>
                                <input type="text" class="form-control" name="role" placeholder="{{ __('e.g. Speech Therapist') }}" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Agency') }}</label>
                                <input type="text" class="form-control" name="agency" placeholder="{{ __('e.g. NHS') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Phone') }}</label>
                                <input type="text" class="form-control" name="phone" placeholder="{{ __('Phone') }}" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Email') }}</label>
                                <input type="email" class="form-control" name="email" placeholder="{{ __('Email') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">{{ __('Save') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('edit-professionals')
    <div class="modal fade" id="edit" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form action="" method="post" id="editForm">
                    @csrf
                    @method('PUT')
                    <div class="modal-header">
                        <h1 class="modal-title fs-5">{{ __('Edit Professional') }}</h1>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                         <div class="row">
                            <div class="col-md-2 form-group mb-3">
                                <label>{{ __('Title') }}</label>
                                <input type="text" class="form-control" name="title" id="edit_title" placeholder="{{ __('Dr, Mr, etc.') }}" />
                            </div>
                            <div class="col-md-5 form-group mb-3">
                                <label>{{ __('First Name') }}*</label>
                                <input type="text" class="form-control" name="first_name" id="edit_first_name" placeholder="{{ __('First Name') }}" required />
                            </div>
                            <div class="col-md-5 form-group mb-3">
                                <label>{{ __('Last Name') }}*</label>
                                <input type="text" class="form-control" name="last_name" id="edit_last_name" placeholder="{{ __('Last Name') }}" required />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Role') }}</label>
                                <input type="text" class="form-control" name="role" id="edit_role" placeholder="{{ __('e.g. Speech Therapist') }}" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Agency') }}</label>
                                <input type="text" class="form-control" name="agency" id="edit_agency" placeholder="{{ __('e.g. NHS') }}" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="edit_phone" placeholder="{{ __('Phone') }}" />
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label>{{ __('Email') }}</label>
                                <input type="email" class="form-control" name="email" id="edit_email" placeholder="{{ __('Email') }}" />
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success" name="save">{{ __('Update') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endcan

    @can('delete-professionals')
    @include('components.delete_modal', ['type' => __('Professional')])
    @endcan
@endsection

@push('scripts')
    <script>
        $(document).on('click', '.edit_icon', function () {
            var url = $(this).data('url');

            $('#editForm').attr('action', url);
            $('#edit_title').val($(this).data('title'));
            $('#edit_first_name').val($(this).data('first_name'));
            $('#edit_last_name').val($(this).data('last_name'));
            $('#edit_role').val($(this).data('role'));
            $('#edit_agency').val($(this).data('agency'));
            $('#edit_phone').val($(this).data('phone'));
            $('#edit_email').val($(this).data('email'));
        });
    </script>
@endpush

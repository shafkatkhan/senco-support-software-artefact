@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="section_title">
            <a href="{{ route('pupils.index') }}" class="previous_icon"><i class="fas {{ is_rtl() ? 'fa-arrow-circle-right' : 'fa-arrow-circle-left' }}"></i></a> {{ __('Return back to pupils') }}
        </div>
        <div class="row settings_wrap onboarding_wrap">
            <div class="col-lg-7 d-flex flex-column">                
                <div class="settings_section">
                    <div class="title">
                        <i class="fa-solid fa-file-arrow-up"></i> {{ __('Import Pupils') }}
                    </div>
                    <div class="description">
                        {{ __('Upload an Excel or CSV file to bulk import pupil records.') }}
                    </div>
                    <hr>
                    <form action="{{ route('pupils.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label class="form-label">{{ __('Select File') }} <span class="text-danger">*</span></label>
                                <input type="file" name="file" class="form-control" accept=".csv, .txt, .xls, .xlsx" required>
                                <small class="text-muted">{{ __('Supported formats: .csv, .xls, .xlsx') }}</small>
                            </div>
                        </div>
                        <div class="settings_actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-upload me-2"></i>{{ __('Import Data') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-5 d-flex flex-column">
                <div class="settings_section" style="background-color: #ffffff91;">
                    <div class="title">
                        <i class="fas fa-info-circle me-2"></i>{{ __('Instructions') }}
                    </div>
                    <div class="description">
                        {{ __('To ensure a successful import, please use the provided template or ensure your file matches the exact column headers.') }}
                    </div>
                    <div class="mt-3 mb-3">
                        <a href="{{ route('pupils.import.template') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-download me-2"></i>{{ __('Download Template') }}
                        </a>
                    </div>
                    <hr>
                    <ul class="small text-muted list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>{{ __('Required Fields') }}:</strong> {{ __('Pupil Number, First Name, Last Name, and Year Group must be provided.') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <strong>{{ __('Dates') }}:</strong> {{ __('Please format dates as YYYY-MM-DD or use standard Excel date formats.') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-triangle-exclamation text-warning me-2"></i>
                            <strong>{{ __('Updates') }}:</strong> {{ __('If a pupil with the same Pupil Number already exists, their information will be updated.') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
@endsection
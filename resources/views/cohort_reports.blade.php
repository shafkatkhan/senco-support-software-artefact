@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="content_top_buttons justify-content-between">
            <div class="filter_pupils_wrap">
                <div class="filter_pupils">
                    <button type="button" id="filterReportsBtn">
                        <i class="fas fa-filter"></i>
                        {{ __('Filters') }}
                        <span class="filter_count_badge" style="display:none;">0</span>
                    </button>
                    <div class="popover" id="filterPopover">
                        <div class="header">
                            <div class="title">{{ __('Filters') }}</div>
                            <button type="button" class="clear_filters">{{ __('Clear all') }}</button>
                        </div>
                        <div class="body">
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterGenderBody">
                                    <span><i class="fas fa-venus-mars"></i>{{ __('Gender') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterGenderBody">
                                    @foreach(['Male', 'Female', 'Other'] as $gender)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="gender" value="{{ $gender }}" id="gender_{{ $gender }}">
                                            <label class="form-check-label" for="gender_{{ $gender }}">{{ __($gender) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterYearGroupBody">
                                    <span><i class="fa-solid fa-people-group"></i>{{ __('Year Group') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterYearGroupBody">
                                    @foreach($year_groups as $year_group)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="year_group" value="{{ $year_group }}" id="year_group_{{ $year_group }}">
                                            <label class="form-check-label" for="year_group_{{ $year_group }}">{{ __('Year :year', ['year' => $year_group]) }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterConditionBody">
                                    <span><i class="fas fa-notes-medical"></i>{{ __('Condition') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterConditionBody">
                                    <input type="text" class="filter_search" placeholder="{{ __('Search condition...') }}" data-list="filterConditionList">
                                    <div id="filterConditionList">
                                        @foreach($conditions as $condition)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="diagnosis" value="{{ $condition }}" id="condition_{{ Str::slug($condition) }}">
                                            <label class="form-check-label" for="condition_{{ Str::slug($condition) }}">{{ $condition }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterMedicationBody">
                                    <span><i class="fas fa-pills"></i>{{ __('Medication') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterMedicationBody">
                                    <input type="text" class="filter_search" placeholder="{{ __('Search medications...') }}" data-list="filterMedicationList">
                                    <div id="filterMedicationList">
                                        @foreach($medications as $medication)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="medication" value="{{ $medication }}" id="medication_{{ Str::slug($medication) }}">
                                            <label class="form-check-label" for="medication_{{ Str::slug($medication) }}">{{ $medication }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterAccommodationBody">
                                    <span><i class="fas fa-universal-access"></i>{{ __('Accommodation') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterAccommodationBody">
                                    <input type="text" class="filter_search" placeholder="{{ __('Search accommodations...') }}" data-list="filterAccommodationList">
                                    <div id="filterAccommodationList">
                                        @foreach($accommodations as $accommodation)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="accommodation_ids" value="{{ $accommodation->id }}" id="accommodation_{{ $accommodation->id }}">
                                            <label class="form-check-label" for="accommodation_{{ $accommodation->id }}">{{ $accommodation->name }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterMajorBody">
                                    <span><i class="fas fa-graduation-cap"></i>{{ __('Major') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterMajorBody">
                                    <input type="text" class="filter_search" placeholder="{{ __('Search majors...') }}" data-list="filterMajorList">
                                    <div id="filterMajorList">
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="major_ids" value="none" id="major_none">
                                            <label class="form-check-label" for="major_none">{{ __('No Major') }}</label>
                                        </div>
                                        @foreach($majors as $major)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="major_ids" value="{{ $major->id }}" id="major_{{ $major->id }}">
                                            <label class="form-check-label" for="major_{{ $major->id }}">{{ $major->name }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="section">
                                <div class="section_header collapsed" data-target="filterSubjectsBody">
                                    <span><i class="fas fa-book"></i>{{ __('Subjects') }}</span>
                                    <i class="fas fa-chevron-down chevron"></i>
                                </div>
                                <div class="section_body" id="filterSubjectsBody">
                                    <input type="text" class="filter_search" placeholder="{{ __('Search subjects...') }}" data-list="filterSubjectsList">
                                    <div id="filterSubjectsList">
                                        @foreach($subjects as $subject)
                                        <div class="form-check">
                                            <input class="form-check-input filter_checkbox" type="checkbox" data-group="subjects" value="{{ $subject->id }}" id="subject_{{ $subject->id }}">
                                            <label class="form-check-label" for="subject_{{ $subject->id }}">{{ $subject->name }}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="active_filters"></div>
            </div>
            <div class="dropdown">
                <button class="top_button export_button dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="export_report_btn" disabled>
                    <i class="fas fa-file-export"></i> {{ __('Export') }}
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item export_option" href="#" data-format="excel">
                            <i class="fas fa-file-excel text-success"></i> {{ __('Export to Excel') }}
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item export_option" href="#" data-format="csv">
                            <i class="fas fa-file-csv text-muted"></i> {{ __('Export to CSV') }}
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <div class="table_wrap">
            <div id="reportResults">
                <p class="text-center p-4 text-muted">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('Loading report preview...') }}
                </p>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            // toggle popover
            $('#filterReportsBtn').on('click', function (e) {
                e.stopPropagation();
                $('#filterPopover').toggleClass('show');
                $(this).toggleClass('active', $('#filterPopover').hasClass('show'));
            });

            // close popover when clicking outside
            $(document).on('click', function (e) {
                if (!$(e.target).closest('.filter_pupils').length) {
                    $('#filterPopover').removeClass('show');
                    
                    // only remove active class if no filters are checked
                    if ($('.filter_checkbox:checked').length == 0) {
                        $('#filterReportsBtn').removeClass('active');
                    }
                }
            });

            // accordion toggle
            $('.filter_pupils .section_header').on('click', function () {
                var targetId = $(this).data('target');
                var $body = $('#' + targetId);
                var isOpen = $body.hasClass('show');

                // close all
                $('.filter_pupils .section_body').removeClass('show');
                $('.filter_pupils .section_header').addClass('collapsed');

                // open clicked one (if it was closed)
                if (!isOpen) {
                    $body.addClass('show');
                    $(this).removeClass('collapsed');
                }
            });

            // search within filter sections
            $('.filter_search').on('input', function () {
                var query = $(this).val().toLowerCase();
                var listId = $(this).data('list');
                $('#' + listId + ' .form-check').each(function () {
                    var label = $(this).find('.form-check-label').text().toLowerCase();
                    $(this).toggle(label.indexOf(query) !== -1);
                });
            });

            // build query from all checkboxes
            function buildQuery() {
                var params = {};
                $('.filter_checkbox:checked').each(function () {
                    var group = $(this).data('group');
                    if (!params[group]) params[group] = [];
                    params[group].push($(this).val());
                });
                return params;
            }

            function buildQueryString() {
                var params = buildQuery();
                var qs = new URLSearchParams();
                for (var key in params) {
                    if (Array.isArray(params[key])) {
                        params[key].forEach(function (val) {
                            qs.append(key + '[]', val);
                        });
                    } else {
                        qs.append(key, params[key]);
                    }
                }
                return qs.toString();
            }

            // update badge + pills
            function updateFilterUI() {
                var totalChecked = $('.filter_checkbox:checked').length;
                var $badge = $('.filter_count_badge');
                if (totalChecked > 0) {
                    $badge.text(totalChecked).show();
                    $('#filterReportsBtn').addClass('active');
                } else {
                    $badge.hide();
                    if (!$('#filterPopover').hasClass('show')) {
                        $('#filterReportsBtn').removeClass('active');
                    }
                }

                // build pills
                var $pills = $('.filter_pupils_wrap .active_filters');
                $pills.empty();
                $('.filter_checkbox:checked').each(function () {
                    var label = $(this).next('label').text().trim();
                    var id = $(this).attr('id');
                    $pills.append(
                        `<span class="filter_pill">
                            ${label}
                            <span class="remove_filter" data-checkbox-id="${id}">&times;</span>
                        </span>`
                    );
                });
            }

            // remove pill
            $(document).on('click', '.remove_filter', function () {
                var checkboxId = $(this).data('checkbox-id');
                $('#' + checkboxId).prop('checked', false);
                updateFilterUI();
                fetchPreview();
            });

            // clear all
            $('.filter_pupils_wrap .clear_filters').on('click', function () {
                $('.filter_checkbox').prop('checked', false);
                updateFilterUI();
                fetchPreview();
            });

            // preview and update UI on any checkbox change
            $('.filter_pupils_wrap .filter_checkbox').on('change', function () {
                updateFilterUI();
                fetchPreview();
            });

            var loadingTimeout;
            function fetchPreview() {
                var params = buildQuery();
                var $results = $('#reportResults');
                
                clearTimeout(loadingTimeout);

                // if taking more than half a second to load, show loading (should be done before most of the time)
                loadingTimeout = setTimeout(function() {
                    $results.html('<p class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> ' + __('Loading...') + '</p>');
                }, 500);

                var html = '';

                $.ajax({
                    url: '{{ route("reports.preview") }}',
                    data: params,
                    success: function (data) {
                        clearTimeout(loadingTimeout);
                        if (data.pupils.length == 0) {
                            $results.html('<p class="text-center p-4 text-muted">' + __('No pupils match the selected filters.') + '</p>');
                            $('#export_report_btn').prop('disabled', true);
                            return;
                        }

                        html += '<table class="table sen_table-striped"><thead class="thead-dark"><tr>';
                        html += '<th>#</th>';
                        html += '<th>' + __('Name') + '</th>';
                        html += '<th>' + __('Gender') + '</th>';
                        html += '<th>' + __('Major') + '</th>';
                        html += '<th>' + __('Year Group') + '</th>';
                        html += '<th>' + __('Tutor Group') + '</th>';
                        html += '<th>' + __('Diagnoses') + '</th>';
                        html += '<th>' + __('Medications') + '</th>';
                        html += '<th>' + __('Subjects') + '</th>';
                        html += '<th>' + __('Accommodations') + '</th>';
                        html += '</tr></thead><tbody>';

                        data.pupils.forEach(function (p) {
                            html += '<tr>';
                            html += '<td>' + p.pupil_number + '</td>';
                            html += '<td>' + p.first_name + ' ' + p.last_name + '</td>';
                            html += '<td>' + p.gender + '</td>';
                            html += '<td>' + p.major + '</td>';
                            html += '<td>' + p.year_group + '</td>';
                            html += '<td>' + p.tutor_group + '</td>';
                            html += '<td>' + p.diagnoses + '</td>';
                            html += '<td>' + p.medications + '</td>';
                            html += '<td>' + p.subjects + '</td>';
                            html += '<td>' + p.accommodations + '</td>';
                            html += '</tr>';
                        });

                        html += '</tbody></table>';
                        html += '<p class="text-muted p-2">' + __(':count pupil(s) found').replace(':count', '<strong>' + data.pupils.length + '</strong>') + '</p>';
                        $results.html(html);

                        $('#export_report_btn').prop('disabled', false);
                    },
                    error: function () {
                        clearTimeout(loadingTimeout);
                        $results.html('<p class="text-center p-4 text-danger">' + __('An error occurred while generating the report.') + '</p>');
                    }
                });
            }

            // export
            $('.export_option').on('click', function(e) {
                e.preventDefault();
                if ($('#export_report_btn').is(':disabled')) return;

                var query_string = buildQueryString();
                var format = $(this).data('format');
                var delimiter = query_string ? '&' : '';

                window.location.href = '{{ route("reports.export") }}' + '?' + query_string + delimiter + 'format=' + format;
            });

            // initial load
            fetchPreview();
        });
    </script>
@endpush
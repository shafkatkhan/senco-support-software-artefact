@extends('layouts.app')

@section('content')
    <section id="content">
        <div class="settings_wrap email_settings_wrap">
                <div class="settings_section">
                    <div class="title">
                        <i class="fa-solid fa-chart-line"></i> {{ __('Pupil Progression Settings') }}
                    </div>
                    <div class="description">
                        {{ __('Configure the parameters for automatic pupil progression.') }}
                    </div>
                    <form action="{{ route('progression-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Annual Update Date') }}</label>
                            <div class="d-flex" style="gap: 10px;">
                                <select name="progression_update_month" class="form-select" required style="width: auto; min-width: 150px;">
                                    <option selected disabled value="">--- {{ __('Month') }} ---</option>
                                    @for($i = 1; $i <= 12; $i++)
                                        @php
                                            $num = str_pad($i, 2, '0', STR_PAD_LEFT);
                                            $name = date('F', mktime(0, 0, 0, $i, 10));
                                        @endphp
                                        <option value="{{ $num }}" {{ $currentMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                    @endfor
                                </select>
                                <select name="progression_update_day" class="form-select" required style="width: auto; min-width: 150px;">
                                    <option selected disabled value="">--- {{ __('Day') }} ---</option>
                                    @for($i = 1; $i <= 31; $i++)
                                        @php $dayNum = str_pad($i, 2, '0', STR_PAD_LEFT); @endphp
                                        <option value="{{ $dayNum }}" {{ $currentDay == $dayNum ? 'selected' : '' }}>{{ $dayNum }}</option>
                                    @endfor
                                </select>
                            </div>
                            <small class="text-muted">{{ __('The date when the system will automatically progress all eligible pupils to the next year group.') }}</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Min Year Group') }}</label>
                            <input type="number" name="progression_min_year_group" class="form-control" value="{{ $settings['progression_min_year_group'] }}" required min="1">
                            <small class="text-muted">{{ __('The lowest year group at this school.') }}</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="form-label">{{ __('Max Year Group') }}</label>
                            <input type="number" name="progression_max_year_group" class="form-control" value="{{ $settings['progression_max_year_group'] }}" required min="1">
                            <small class="text-muted">{{ __('The highest year group at this school. Pupils in this year group will not be progressed further automatically.') }}</small>
                        </div>
                        <div class="settings_actions">
                            <button type="submit" class="btn btn-success">
                                {{ __('Save Settings') }}
                            </button>
                        </div>
                    </form>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    const $monthSelect = $('select[name="progression_update_month"]');
    const $daySelect = $('select[name="progression_update_day"]');

    function updateDays() {
        const month = parseInt($monthSelect.val());
        if (!month) return;

        // using 2026 to ensure can't select 29th February
        const maxDays = new Date(2026, month, 0).getDate();
        const currentDay = parseInt($daySelect.val());

        let daySelectedIsValid = false;

        $daySelect.find('option').each(function () {
            if (this.value == '') return;

            const val = parseInt(this.value);

            if (val > maxDays) {
                $(this).prop('disabled', true).prop('hidden', true);
            } else {
                $(this).prop('disabled', false).prop('hidden', false);

                if (val == currentDay) {
                    daySelectedIsValid = true;
                }
            }
        });

        if (currentDay && !daySelectedIsValid) {
            $daySelect.val('');
        }
    }

    $monthSelect.on('change', updateDays);

    // run on initial load if a month is already selected
    if ($monthSelect.val()) {
        updateDays();
    }
</script>
@endpush

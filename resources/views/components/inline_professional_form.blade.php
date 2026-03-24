<div class="justify-between-flexbox">
    <label>{{ __('Professional') }}</label>
    <button type="button" class="btn btn-sm btn-link p-0" id="toggle_professional_btn">+ {{ __('Add New Professional') }}</button>
</div>
<div id="existing_professional_box">
    <select class="form-select" name="professional_id" id="professional_id">
        <option value="">{{ __('None / Not Applicable') }}</option>
        @foreach($professionals as $prof)
            <option value="{{ $prof->id }}">{{ $prof->title }} {{ $prof->first_name }} {{ $prof->last_name }}{{ $prof->role ? ' (' . $prof->role . ')' : '' }}</option>
        @endforeach
    </select>
</div>
<div id="new_professional_box">
    <div class="row">
        <div class="col-md-3 mb-2">
            <input type="text" class="form-control form-control-sm" name="prof_title" placeholder="{{ __('Title') }}">
        </div>
        <div class="col-md-4 mb-2">
            <input type="text" class="form-control form-control-sm" name="prof_first_name" placeholder="{{ __('First Name') }}*">
        </div>
        <div class="col-md-5 mb-2">
            <input type="text" class="form-control form-control-sm" name="prof_last_name" placeholder="{{ __('Last Name') }}*">
        </div>
        <div class="col-md-6 mb-2">
            <input type="text" class="form-control form-control-sm" name="prof_role" placeholder="{{ __('Role') }}">
        </div>
        <div class="col-md-6 mb-2">
            <input type="text" class="form-control form-control-sm" name="prof_agency" placeholder="{{ __('Agency') }}">
        </div>
        <div class="col-md-6">
            <input type="text" class="form-control form-control-sm" name="prof_phone" placeholder="{{ __('Phone') }}">
        </div>
        <div class="col-md-6">
            <input type="email" class="form-control form-control-sm" name="prof_email" placeholder="{{ __('Email') }}">
        </div>
    </div>
    <input type="hidden" name="is_new_professional" id="is_new_professional" value="0">
</div>
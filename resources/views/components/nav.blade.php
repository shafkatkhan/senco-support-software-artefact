<section id="nav">
    <ul>
        <li class="logo"><img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware" /></li>

        <li class="nav_user_name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</li>

        <div class="nav_items @if(auth()->user()->isMfaPending()) nav_disabled @endif">
            <li><a href="{{ route('test-form.index') }}" class="{{ request()->routeIs('test-form.*') ? 'activenav' : '' }}">{{ __('Test Form') }}</a></li>
            <li><a href="{{ route('page1') }}" class="{{ request()->routeIs('page1') ? 'activenav' : '' }}">{{ __('Page 1') }}</a></li>
            <li><a href="{{ route('pupils.index') }}" class="{{ request()->routeIs('pupils.*') ? 'activenav' : '' }}">{{ __('SEND Pupils') }}</a></li>
            <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'activenav' : '' }}">{{ __('Accommodations') }}</a></li>
            <li><a href="{{ route('majors.index') }}" class="{{ request()->routeIs('majors.*') ? 'activenav' : '' }}">{{ __('Majors') }}</a></li>
            <li><a href="{{ route('proficiencies.index') }}" class="{{ request()->routeIs('proficiencies.*') ? 'activenav' : '' }}">{{ __('Proficiencies') }}</a></li>
            <li><a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'activenav' : '' }}">{{ __('Subjects') }}</a></li>
            <li><a href="{{ route('record-types.index') }}" class="{{ request()->routeIs('record-types.*') ? 'activenav' : '' }}">{{ __('Record Types') }}</a></li>
            <li><a href="{{ route('meeting-types.index') }}" class="{{ request()->routeIs('meeting-types.*') ? 'activenav' : '' }}">{{ __('Meeting Types') }}</a></li>
            <li><a href="{{ route('professionals.index') }}" class="{{ request()->routeIs('professionals.*') ? 'activenav' : '' }}">{{ __('Professionals') }}</a></li>
            <li><a href="{{ route('user-groups.index') }}" class="{{ request()->routeIs('user-groups.*') ? 'activenav' : '' }}">{{ __('User Groups') }}</a></li>
            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'activenav' : '' }}">{{ __('Users') }}</a></li>
            <li class="dropdown_nav_btn"><a href="#">{{ __('System Settings') }} <i class="fas fa-chevron-left"></i></a></li>
            <div class="dropdown_items">
                <li class="sub_nav_item"><a href="{{ route('email-settings.index') }}" class="{{ request()->routeIs('email-settings.*') ? 'activenav' : '' }}">{{ __('Email Settings') }}</a></li>
                <li class="sub_nav_item"><a href="{{ route('mfa-settings.index') }}" class="{{ request()->routeIs('mfa-settings.*') ? 'activenav' : '' }}">{{ __('MFA Settings') }}</a></li>
                <li class="sub_nav_item"><a href="{{ route('mfa-setup.index') }}" class="{{ request()->routeIs('mfa-setup.*') ? 'activenav' : '' }}">{{ __('MFA Setup') }}</a></li>
                <li class="sub_nav_item"><a href="{{ route('backups.index') }}" class="{{ request()->routeIs('backups.*') ? 'activenav' : '' }}">{{ __('System Backups') }}</a></li>
            </div>
        </div>
    </ul>
</section>
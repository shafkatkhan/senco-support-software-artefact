<section id="nav">
    <ul>
        <li class="logo"><img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware" /></li>

        <li class="nav_user_name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</li>

        <div class="nav_items @if(auth()->user()->isMfaPending()) nav_disabled @endif">
            @can('view-pupils')
                <li><a href="{{ route('pupils.index') }}" class="{{ request()->routeIs('pupils.*') ? 'activenav' : '' }}">{{ __('SEND Pupils') }}</a></li>
            @endcan
            @can('export-cohort-reports')
                <li><a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'activenav' : '' }}">{{ __('Cohort Reports') }}</a></li>
            @endcan
            @can('view-accommodations')
                <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'activenav' : '' }}">{{ __('Accommodations') }}</a></li>
            @endcan
            @can('view-majors')
                <li><a href="{{ route('majors.index') }}" class="{{ request()->routeIs('majors.*') ? 'activenav' : '' }}">{{ __('Majors') }}</a></li>
            @endcan
            @can('view-proficiencies')
                <li><a href="{{ route('proficiencies.index') }}" class="{{ request()->routeIs('proficiencies.*') ? 'activenav' : '' }}">{{ __('Proficiencies') }}</a></li>
            @endcan
            @can('view-subjects')
                <li><a href="{{ route('subjects.index') }}" class="{{ request()->routeIs('subjects.*') ? 'activenav' : '' }}">{{ __('Subjects') }}</a></li>
            @endcan
            @can('view-record-types')
                <li><a href="{{ route('record-types.index') }}" class="{{ request()->routeIs('record-types.*') ? 'activenav' : '' }}">{{ __('Record Types') }}</a></li>
            @endcan
            @can('view-meeting-types')
                <li><a href="{{ route('meeting-types.index') }}" class="{{ request()->routeIs('meeting-types.*') ? 'activenav' : '' }}">{{ __('Meeting Types') }}</a></li>
            @endcan
            @can('view-professionals')
                <li><a href="{{ route('professionals.index') }}" class="{{ request()->routeIs('professionals.*') ? 'activenav' : '' }}">{{ __('Professionals') }}</a></li>
            @endcan
            @can('view-user-groups')
                <li><a href="{{ route('user-groups.index') }}" class="{{ request()->routeIs('user-groups.*') ? 'activenav' : '' }}">{{ __('User Groups') }}</a></li>
            @endcan
            @can('view-users')
                <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'activenav' : '' }}">{{ __('Users') }}</a></li>
            @endcan
            <li class="dropdown_nav_btn"><a href="#">{{ __('System Settings') }} <i class="fas fa-chevron-left"></i></a></li>
            <div class="dropdown_items">
                @can('manage-llm-settings')
                    <li class="sub_nav_item"><a href="{{ route('llm-settings.index') }}" class="{{ request()->routeIs('llm-settings.*') ? 'activenav' : '' }}">{{ __('LLM Settings') }}</a></li>
                @endcan
                @can('manage-email-settings')
                    <li class="sub_nav_item"><a href="{{ route('email-settings.index') }}" class="{{ request()->routeIs('email-settings.*') ? 'activenav' : '' }}">{{ __('Email Settings') }}</a></li>
                @endcan
                @can('manage-mfa-settings')
                    <li class="sub_nav_item"><a href="{{ route('mfa-settings.index') }}" class="{{ request()->routeIs('mfa-settings.*') ? 'activenav' : '' }}">{{ __('MFA Settings') }}</a></li>
                @endcan
                    <li class="sub_nav_item"><a href="{{ route('mfa-setup.index') }}" class="{{ request()->routeIs('mfa-setup.*') ? 'activenav' : '' }}">{{ __('MFA Setup') }}</a></li>
                @can('manage-permissions')
                    <li class="sub_nav_item"><a href="{{ route('permissions.index') }}" class="{{ request()->routeIs('permissions.*') ? 'activenav' : '' }}">{{ __('Permissions') }}</a></li>
                @endcan
                @can('view-download-backups')
                    <li class="sub_nav_item"><a href="{{ route('backups.index') }}" class="{{ request()->routeIs('backups.*') ? 'activenav' : '' }}">{{ __('System Backups') }}</a></li>
                @endcan
                @can('manage-school-progression-settings')
                    <li class="sub_nav_item"><a href="{{ route('progression-settings.index') }}" class="{{ request()->routeIs('progression-settings.*') ? 'activenav' : '' }}">{{ __('Progression Settings') }}</a></li>
                @endcan
            </div>
        </div>
    </ul>
</section>
<section id="nav">
    <ul>
        <li class="logo"><img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware" /></li>

        <li class="nav_user_name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</li>

        <div class="nav_items">
            <li><a href="{{ route('test-form.index') }}" class="{{ request()->routeIs('test-form.*') ? 'activenav' : '' }}">{{ __('Test Form') }}</a></li>
            <li><a href="{{ route('page1') }}" class="{{ request()->routeIs('page1') ? 'activenav' : '' }}">{{ __('Page 1') }}</a></li>
            <li><a href="{{ route('pupils.index') }}" class="{{ request()->routeIs('pupils.*') ? 'activenav' : '' }}">{{ __('SEND Pupils') }}</a></li>
            <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'activenav' : '' }}">{{ __('Accommodations') }}</a></li>
            <li><a href="{{ route('record-types.index') }}" class="{{ request()->routeIs('record-types.*') ? 'activenav' : '' }}">{{ __('Record Types') }}</a></li>
            <li><a href="{{ route('user-groups.index') }}" class="{{ request()->routeIs('user-groups.*') ? 'activenav' : '' }}">{{ __('User Groups') }}</a></li>
            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'activenav' : '' }}">{{ __('Users') }}</a></li>
        </div>
    </ul>
</section>
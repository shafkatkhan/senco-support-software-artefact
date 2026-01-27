<section id="nav">
    <ul>
        <li class="logo"><img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware" /></li>

        <li class="nav_user_name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</li>

        <div class="nav_items">
            <li><a href="{{ route('test-form.index') }}" class="{{ request()->routeIs('test-form.*') ? 'activenav' : '' }}">Test Form</a></li>
            <li><a href="{{ route('page1') }}" class="{{ request()->routeIs('page1') ? 'activenav' : '' }}">Page 1</a></li>
            <li><a href="{{ route('pupils.index') }}" class="{{ request()->routeIs('pupils.*') ? 'activenav' : '' }}">SEND Pupils</a></li>
            <li><a href="{{ route('accommodations.index') }}" class="{{ request()->routeIs('accommodations.*') ? 'activenav' : '' }}">Accommodations</a></li>
            <li><a href="{{ route('user-groups.index') }}" class="{{ request()->routeIs('user-groups.*') ? 'activenav' : '' }}">User Groups</a></li>
            <li><a href="{{ route('users.index') }}" class="{{ request()->routeIs('users.*') ? 'activenav' : '' }}">Users</a></li>
        </div>
    </ul>
</section>
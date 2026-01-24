<section id="nav">
    <ul>
        <li class="logo"><img src="{{ asset('img/logo.png') }}" alt="MySencoSupportSoftware" /></li>

        <li class="nav_user_name">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</li>

        <div class="nav_items">
            <li><a href="{{ route('test-form.index') }}" class="{{ request()->routeIs('test-form.*') ? 'activenav' : '' }}">Test Form</a></li>
            <li><a href="{{ route('page1') }}" class="{{ request()->routeIs('page1') ? 'activenav' : '' }}">Page 1</a></li>
            <li><a href="{{ route('page2') }}" class="{{ request()->routeIs('page2') ? 'activenav' : '' }}">Page 2</a></li>
            <li><a href="{{ route('page3') }}" class="{{ request()->routeIs('page3') ? 'activenav' : '' }}">Page 3</a></li>
            <li><a href="{{ route('user-groups.index') }}" class="{{ request()->routeIs('user-groups.*') ? 'activenav' : '' }}">User Groups</a></li>
        </div>
    </ul>
</section>
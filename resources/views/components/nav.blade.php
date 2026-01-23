<section id="nav">
    <ul>
        <li class="logo"><img src="{{ asset('img/logo.png') }}" alt="SencoSupport" /></li>

        <li class="nav_user_name">John Smith</li>

        <div class="nav_items">
            <li><a href="{{ url('/test-form') }}" class="{{ request()->is('test-form') ? 'activenav' : '' }}">Test Form</a></li>
            <li><a href="{{ url('/page1') }}" class="{{ request()->is('page1') ? 'activenav' : '' }}">Page 1</a></li>
            <li><a href="{{ url('/page2') }}" class="{{ request()->is('page2') ? 'activenav' : '' }}">Page 2</a></li>
            <li><a href="{{ url('/page3') }}" class="{{ request()->is('page3') ? 'activenav' : '' }}">Page 3</a></li>
        </div>
    </ul>
</section>
<section id="top_nav">
    <div class="top_nav_header">
        <div style="display: flex; align-items: center; padding: 0px 15px;">
            <a href="#" id="toggleNavBtn"><i class="fas fa-bars"></i></a>
            <div class="page_title">{{ __($title) ?? 'MySencoSupportSoftware' }}</div>
        </div>
        <div class="logout">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ __('Logout') }} <i class="fas fa-power-off"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
    @if(request()->is('pupil-*'))
    <div class="top_nav_items_container">
        <ul>
            @can('view-pupils')
                <li>
                    <a href="{{ route('pupils.show', $pupil->id) }}" class="{{ request()->routeIs('pupils.show') ? 'activenav' : '' }}">{{ __('Summary') }}</a>
                </li>
            @endcan
            @can('view-medications')
                <li>
                    <a href="{{ route('pupils.medications', $pupil->id) }}" class="{{ request()->routeIs('pupils.medications') ? 'activenav' : '' }}">{{ __('Medications') }}</a>
                </li>
            @endcan
            @can('view-diagnoses')
                <li>
                    <a href="{{ route('pupils.diagnoses', $pupil->id) }}" class="{{ request()->routeIs('pupils.diagnoses') ? 'activenav' : '' }}">{{ __('Diagnoses') }}</a>
                </li>
            @endcan
            @can('view-records')
                <li>
                    <a href="{{ route('pupils.records', $pupil->id) }}" class="{{ request()->routeIs('pupils.records') ? 'activenav' : '' }}">{{ __('Records') }}</a>
                </li>
            @endcan
            @can('view-events')
                <li>
                    <a href="{{ route('pupils.events', $pupil->id) }}" class="{{ request()->routeIs('pupils.events') ? 'activenav' : '' }}">{{ __('Events') }}</a>
                </li>
            @endcan
            @can('view-meetings')
                <li>
                    <a href="{{ route('pupils.meetings', $pupil->id) }}" class="{{ request()->routeIs('pupils.meetings') ? 'activenav' : '' }}">{{ __('Meetings') }}</a>
                </li>
            @endcan
            @can('view-diets')
                <li>
                    <a href="{{ route('pupils.diets', $pupil->id) }}" class="{{ request()->routeIs('pupils.diets') ? 'activenav' : '' }}">{{ __('Diet') }}</a>
                </li>
            @endcan
            @can('view-family-members')
                <li>
                    <a href="{{ route('pupils.family_members', $pupil->id) }}" class="{{ request()->routeIs('pupils.family_members') ? 'activenav' : '' }}">{{ __('Family Members') }}</a>
                </li>
            @endcan
        </ul>
    </div>
    @endif
</section>
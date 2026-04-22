<section id="top_nav">
    <div class="top_nav_header">
        <div style="display: flex; align-items: center; padding: 0px 15px;">
            <a href="#" id="toggleNavBtn" aria-label="Toggle navigation"><i class="fas fa-bars"></i></a>
            <div class="page_title">{{ isset($title) ? $title : 'EduSen' }}</div>
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
            @can('view-school-histories')
                <li>
                    <a href="{{ route('pupils.school_histories', $pupil->id) }}" class="{{ request()->routeIs('pupils.school_histories') ? 'activenav' : '' }}">{{ __('School History') }}</a>
                </li>
            @endcan
            @can('manage-pupil-progressions')
                <li>
                    <a href="{{ route('pupils.progressions', $pupil->id) }}" class="{{ request()->routeIs('pupils.progressions') ? 'activenav' : '' }}">{{ __('Progressions') }}</a>
                </li>
            @endcan
            @can('manage-attachments')
                <li>
                    <a href="{{ route('pupils.attachments', $pupil->id) }}" class="{{ request()->routeIs('pupils.attachments') ? 'activenav' : '' }}">{{ __('Attachments') }}</a>
                </li>
            @endcan
        </ul>
    </div>
    @endif
</section>
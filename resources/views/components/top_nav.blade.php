<section id="top_nav">
    <div style="display: flex; align-items: center; padding: 0px 15px;">
        <a href="#" id="toggleNavBtn"><i class="fas fa-bars"></i></a>
        <div class="page_title">{{ $title ?? 'MySencoSupportSoftware' }}</div>
    </div>
    <ul>
        @if(request()->is('pupil-*'))
            <li>
                <a href="{{ route('pupils.show', $pupil->id) }}" class="{{ request()->routeIs('pupils.show') ? 'activenav' : '' }}">{{ __('Summary') }}</a>
            </li>
            <li>
                <a href="{{ route('pupils.medications', $pupil->id) }}" class="{{ request()->routeIs('pupils.medications') ? 'activenav' : '' }}">{{ __('Medications') }}</a>
            </li>
            <li>
                <a href="{{ route('pupils.diagnoses', $pupil->id) }}" class="{{ request()->routeIs('pupils.diagnoses') ? 'activenav' : '' }}">{{ __('Diagnoses') }}</a>
            </li>
            <li>
                <a href="{{ route('pupils.records', $pupil->id) }}" class="{{ request()->routeIs('pupils.records') ? 'activenav' : '' }}">{{ __('Records') }}</a>
            </li>
            <li>
                <a href="{{ route('pupils.accommodations', $pupil->id) }}" class="{{ request()->routeIs('pupils.accommodations') ? 'activenav' : '' }}">{{ __('Accommodations') }}</a>
            </li>
            <li>
                <a href="{{ route('pupils.family_members', $pupil->id) }}" class="{{ request()->routeIs('pupils.family_members') ? 'activenav' : '' }}">{{ __('Family Members') }}</a>
            </li>
        @endif
        <li class="logout">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                {{ __('Logout') }} <i class="fas fa-power-off"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</section>
<section id="top_nav">
    <div class="page_title">{{ $title ?? 'MySencoSupportSoftware' }}</div>
    <ul>
        @if(request()->is('pupil-*'))
            <li>
                <a href="{{ route('pupils.show', $pupil->id) }}" class="{{ request()->routeIs('pupils.show') ? 'activenav' : '' }}">Summary</a>
            </li>
            <li>
                <a href="{{ route('pupils.medications', $pupil->id) }}" class="{{ request()->routeIs('pupils.medications') ? 'activenav' : '' }}">Medications</a>
            </li>
            <li>
                <a href="{{ route('pupils.diagnoses', $pupil->id) }}" class="{{ request()->routeIs('pupils.diagnoses') ? 'activenav' : '' }}">Diagnoses</a>
            </li>
            <li>
                <a href="{{ route('pupils.family_members', $pupil->id) }}" class="{{ request()->routeIs('pupils.family_members') ? 'activenav' : '' }}">Family Members</a>
            </li>
        @endif
        <li class="logout">
            <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                Logout <i class="fas fa-power-off"></i>
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </li>
    </ul>
</section>
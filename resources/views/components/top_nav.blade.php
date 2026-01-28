<section id="top_nav">
    <div class="page_title">{{ $title ?? 'MySencoSupportSoftware' }}</div>
    <ul>
        @if(request()->is('pupil-*'))
            <li>
                <a href="{{ route('pupils.show', $pupil->id) }}" class="{{ request()->routeIs('pupils.show') ? 'activenav' : '' }}">Summary</a>
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
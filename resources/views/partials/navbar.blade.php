@php
    $links = [
        ['Home', 'home', 'home'],
        ['About Us', 'about', 'about'],
        ['Executive Committee', 'committee.index', 'committee.*'],
        ['Events', 'events.index', 'events.*'],
        ['News & Announcements', 'announcements.index', 'announcements.*'],
        ['Achievements', 'achievements.index', 'achievements.*'],
        ['Members', 'members.index', 'members.*'],
        ['Resources', 'resources.index', 'resources.*'],
        ['Research Papers', 'research-papers.index', 'research-papers.*'],
        ['Gallery', 'gallery.index', 'gallery.*'],
        ['Membership', 'membership', 'membership'],
        ['Contact', 'contact', 'contact'],
    ];
    $join = safe_url(setting('ieee_join_url'));
@endphp
<nav class="navbar navbar-expand-xl site-nav">
    <div class="container-fluid px-3 px-xl-4">
        <a class="navbar-brand brand" href="{{ route('home') }}">
            @if (setting('logo'))
                <img src="{{ public_file_url(setting('logo')) }}" alt="" width="38" height="38" class="rounded">
            @else
                <span class="mark" aria-hidden="true">B</span>
            @endif
            <span>{{ setting('branch_name') }}<small>Bangladesh University of Professionals</small></span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Open menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-xl-0">
                @foreach ($links as [$label, $route, $match])
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs($match) ? 'active' : '' }}" href="{{ route($route) }}" @if(request()->routeIs($match)) aria-current="page" @endif>{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
            <div class="d-flex flex-wrap gap-2 py-2">
                @if ($join)
                    <a class="btn btn-gold btn-sm" href="{{ $join }}" target="_blank" rel="noopener noreferrer">Join IEEE</a>
                @endif
                @auth
                    <a class="btn btn-navy btn-sm" href="{{ auth()->user()->hasAdminAccess() ? route('admin.dashboard') : route('member.dashboard') }}">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-secondary btn-sm" type="submit">Logout</button>
                    </form>
                @else
                    <a class="btn btn-ieee btn-sm" href="{{ route('login') }}">Login</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

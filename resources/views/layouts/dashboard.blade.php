@php
    $user = auth()->user();
    $adminLinks = [
        ['Dashboard', 'admin.dashboard', 'admin.dashboard'],
        ['Events', 'admin.events.index', 'admin.events.*'],
        ['Registrations', 'admin.registrations.index', 'admin.registrations.*'],
        ['Announcements', 'admin.announcements.index', 'admin.announcements.*'],
        ['Achievements', 'admin.achievements.index', 'admin.achievements.*'],
        ['Gallery', 'admin.gallery.index', 'admin.gallery.*'],
        ['Resources', 'admin.resources.index', 'admin.resources.*'],
        ['Research papers', 'admin.research-papers.index', 'admin.research-papers.*'],
        ['Members', 'admin.members.index', 'admin.members.*'],
        ['Committee', 'admin.committee.index', 'admin.committee.*'],
        ['Faculty leadership', 'admin.leadership.index', 'admin.leadership.*'],
        ['Messages', 'admin.messages.index', 'admin.messages.*'],
        ['Website content', 'admin.content.edit', 'admin.content.*'],
        ['Reports', 'admin.reports.index', 'admin.reports.*'],
        ['Activity log', 'admin.logs.index', 'admin.logs.*'],
        ['Password', 'admin.account.edit', 'admin.account.*'],
    ];
    if ($user?->isSuperAdmin()) {
        $adminLinks[] = ['Admin management', 'super.admins.index', 'super.admins.*'];
        $adminLinks[] = ['System settings', 'super.settings.edit', 'super.settings.*'];
    }
    $memberLinks = [
        ['Dashboard', 'member.dashboard', 'member.dashboard'],
        ['My profile', 'member.profile.edit', 'member.profile.*'],
        ['My registrations', 'member.registrations', 'member.registrations'],
        ['My achievements', 'member.achievements', 'member.achievements'],
    ];
    $links = $user?->hasAdminAccess() ? $adminLinks : $memberLinks;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') — {{ setting('branch_name') }}</title>
    <link rel="icon" href="{{ setting('favicon') ? public_file_url(setting('favicon')) : asset('assets/img/mark.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,560&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('assets/css/app.css') }}" rel="stylesheet">
</head>
<body class="dash-body">
    <div class="dash-shell">
        <aside class="offcanvas-lg offcanvas-start dash-side" tabindex="-1" id="dashNav">
            <div class="offcanvas-header">
                <a class="brand text-white" href="{{ route('home') }}"><span class="mark">B</span><span>BUP IEEE</span></a>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" data-bs-target="#dashNav" aria-label="Close menu"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column p-3">
                <a class="mb-3" href="{{ route('home') }}">View public website</a>
                @foreach ($links as [$label, $route, $match])
                    <a class="{{ request()->routeIs($match) ? 'active' : '' }}" href="{{ route($route) }}">{{ $label }}</a>
                @endforeach
                <a href="{{ route('notifications.index') }}">Notifications</a>
                <form class="mt-3" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-outline-light btn-sm w-100" type="submit">Logout</button>
                </form>
            </div>
        </aside>
        <div class="dash-main">
            <header class="dash-top">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-navy btn-sm d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#dashNav" aria-controls="dashNav">Menu</button>
                    <div>
                        <h1 class="h4 mb-0">@yield('heading', 'Dashboard')</h1>
                        <div class="muted small">{{ $user?->name }} · {{ $user?->isSuperAdmin() ? 'Super Admin' : ($user?->isAdmin() ? 'Admin' : 'Member') }}</div>
                    </div>
                </div>
                <a class="btn btn-outline-secondary btn-sm" href="{{ route('notifications.index') }}">
                    Alerts
                    @if ($user?->unreadNotifications()->count())
                        <span class="badge text-bg-danger">{{ $user->unreadNotifications()->count() }}</span>
                    @endif
                </a>
            </header>
            <div class="dash-content">
                @include('partials.alerts')
                @if (str_ends_with((string) $user?->email, '@bupieee.test'))
                    <div class="alert alert-warning">This account uses a development email. Change the password and replace seeded contact details before a public launch.</div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
</body>
</html>

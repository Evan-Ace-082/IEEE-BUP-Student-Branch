<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h2>{{ setting('branch_name') }}</h2>
                <p>{{ setting('tagline') }}</p>
                @if (setting('address'))<p>{{ setting('address') }}</p>@endif
            </div>
            <div class="col-6 col-md-2">
                <h3>Explore</h3>
                <ul class="list-unstyled">
                    <li><a href="{{ route('about') }}">About Us</a></li>
                    <li><a href="{{ route('committee.index') }}">Committee</a></li>
                    <li><a href="{{ route('events.index') }}">Events</a></li>
                    <li><a href="{{ route('announcements.index') }}">Announcements</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h3>Take part</h3>
                <ul class="list-unstyled">
                    <li><a href="{{ route('membership') }}">Membership</a></li>
                    <li><a href="{{ route('resources.index') }}">Resources</a></li>
                    <li><a href="{{ route('gallery.index') }}">Gallery</a></li>
                    <li><a href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h3>Contact</h3>
                @if (setting('official_email'))<p><a href="mailto:{{ setting('official_email') }}">{{ setting('official_email') }}</a></p>@endif
                @if (setting('phone'))<p>{{ setting('phone') }}</p>@endif
                <div class="d-flex gap-3">
                    @foreach (['facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'instagram' => 'Instagram', 'website' => 'Website'] as $key => $label)
                        @if (safe_url(setting($key)))
                            <a href="{{ safe_url(setting($key)) }}" target="_blank" rel="noopener noreferrer">{{ $label }}</a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
        <div class="bottom d-flex flex-wrap justify-content-between gap-2">
            <span>&copy; {{ date('Y') }} {{ setting('branch_name') }}. Bangladesh University of Professionals.</span>
            <span>IEEE student branch website</span>
        </div>
    </div>
</footer>

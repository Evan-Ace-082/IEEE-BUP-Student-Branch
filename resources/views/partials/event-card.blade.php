@php($status = $event->temporalStatus())
<article class="event-card">
    @if ($event->banner)
        <img src="{{ public_file_url($event->banner) }}" alt="">
    @else
        <div class="avatar-fallback" style="height:180px;font-size:1rem">{{ \App\Models\Event::categories()[$event->category] ?? 'Event' }}</div>
    @endif
    <div class="body">
        <span class="badge status-{{ $status }}">{{ ucfirst($status) }}</span>
        <h3 class="h5 mt-2">{{ $event->title }}</h3>
        <p class="mb-1"><strong>{{ $event->starts_at?->format('d M Y') }}</strong> · {{ $event->starts_at?->format('h:i A') }}</p>
        @if ($event->venue)<p class="mb-1 muted">{{ $event->venue }}</p>@endif
        <p class="muted">{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 120) }}</p>
        <a class="btn btn-outline-primary btn-sm" href="{{ route('events.show', $event) }}">View Details</a>
    </div>
</article>

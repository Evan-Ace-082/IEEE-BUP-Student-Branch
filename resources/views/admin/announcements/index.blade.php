@extends('layouts.dashboard')
@section('title', 'Announcements')
@section('heading', 'Announcements')
@section('content')
<div class="d-flex justify-content-between mb-3"><a class="btn btn-ieee" href="{{ route('admin.announcements.create') }}">New announcement</a></div>
<div class="table-card"><table class="table"><thead><tr><th>Title</th><th>Category</th><th>Status</th><th></th></tr></thead><tbody>
@forelse ($announcements as $item)
<tr>
    <td>{{ $item->title }}</td><td>{{ $item->category }}</td><td><span class="badge status-{{ $item->status }}">{{ $item->status }}</span></td>
    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.announcements.edit', $item) }}">Edit</a>
        <form class="d-inline" method="POST" action="{{ route('admin.announcements.destroy', $item) }}" data-confirm="Delete this announcement?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>
@empty<tr><td colspan="4" class="empty-state">No announcements.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $announcements->links() }}</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Gallery')
@section('heading', 'Gallery albums')
@section('content')
<a class="btn btn-ieee mb-3" href="{{ route('admin.gallery.create') }}">New album</a>
<div class="table-card"><table class="table"><thead><tr><th>Album</th><th>Category</th><th>Photos</th><th></th></tr></thead><tbody>
@forelse ($albums as $album)
<tr>
    <td>{{ $album->title }}</td><td>{{ $album->category?->name }}</td><td>{{ $album->items_count }}</td>
    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.gallery.edit', $album) }}">Edit</a>
        <form class="d-inline" method="POST" action="{{ route('admin.gallery.destroy', $album) }}" data-confirm="Delete this album and its photos?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>
@empty<tr><td colspan="4" class="empty-state">No albums.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $albums->links() }}</div>
@endsection

@extends('layouts.dashboard')
@section('title', 'Resources')
@section('heading', 'Resources')
@section('content')
<a class="btn btn-ieee mb-3" href="{{ route('admin.resources.create') }}">New resource</a>
<div class="table-card"><table class="table"><thead><tr><th>Title</th><th>Type</th><th>Visibility</th><th></th></tr></thead><tbody>
@forelse ($resources as $resource)
<tr>
    <td>{{ $resource->title }}</td><td>{{ $resource->type }}</td><td>{{ $resource->visibility }}</td>
    <td><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.resources.edit', $resource) }}">Edit</a>
        <form class="d-inline" method="POST" action="{{ route('admin.resources.destroy', $resource) }}" data-confirm="Delete this resource?">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form></td>
</tr>
@empty<tr><td colspan="4" class="empty-state">No resources.</td></tr>@endforelse
</tbody></table></div>
<div class="mt-3">{{ $resources->links() }}</div>
@endsection

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ResourceRequest;
use App\Models\Resource;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::query()->latest();

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        return view('admin.resources.index', [
            'resources' => $query->paginate(12)->withQueryString(),
        ]);
    }

    public function create()
    {
        return view('admin.resources.form', [
            'resource' => new Resource(['type' => 'link', 'visibility' => 'public', 'category' => 'learning']),
            'categories' => Resource::categories(),
            'types' => Resource::types(),
        ]);
    }

    public function store(ResourceRequest $request)
    {
        $resource = Resource::query()->create($this->payload($request));
        ActivityLogger::log('create', 'resources', $resource->id, 'Created resource');

        return redirect()->route('admin.resources.edit', $resource)->with('status', 'Resource created.');
    }

    public function edit(Resource $resource)
    {
        return view('admin.resources.form', [
            'resource' => $resource,
            'categories' => Resource::categories(),
            'types' => Resource::types(),
        ]);
    }

    public function update(ResourceRequest $request, Resource $resource)
    {
        $data = $this->payload($request, $resource);
        $resource->update($data);
        ActivityLogger::log('update', 'resources', $resource->id, 'Updated resource');

        return redirect()->route('admin.resources.edit', $resource)->with('status', 'Resource saved.');
    }

    public function destroy(Resource $resource)
    {
        SecureUpload::delete($resource->file_path, $resource->disk ?: 'local');
        SecureUpload::delete($resource->thumbnail);
        $resource->delete();
        ActivityLogger::log('delete', 'resources', $resource->id, 'Deleted resource');

        return redirect()->route('admin.resources.index')->with('status', 'Resource removed.');
    }

    private function payload(ResourceRequest $request, ?Resource $existing = null): array
    {
        $data = $request->safe()->only(['title', 'description', 'category', 'type', 'author', 'published_on', 'visibility']);
        $data['description'] = $data['description'] ?: null;
        $data['author'] = $data['author'] ?: null;
        $data['published_on'] = $data['published_on'] ?: now()->toDateString();
        $data['created_by'] = $existing?->created_by ?: $request->user()->id;
        $data['external_url'] = null;
        $data['disk'] = $existing?->disk ?: 'local';
        $data['file_path'] = $existing?->file_path;

        if (in_array($data['type'], ['link', 'video'], true)) {
            $data['external_url'] = safe_url($request->input('external_url'));
            if ($existing?->file_path) {
                SecureUpload::delete($existing->file_path, $existing->disk ?: 'local');
                $data['file_path'] = null;
            }
        }

        if ($data['type'] === 'file' && $request->file('file')) {
            if ($existing?->file_path) {
                SecureUpload::delete($existing->file_path, $existing->disk ?: 'local');
            }
            $data['file_path'] = SecureUpload::document($request->file('file'), 'resources');
            $data['disk'] = 'local';
            $data['external_url'] = null;
        }

        if ($request->file('thumbnail')) {
            if ($existing?->thumbnail) {
                SecureUpload::delete($existing->thumbnail);
            }
            $data['thumbnail'] = SecureUpload::image($request->file('thumbnail'), 'resources/thumbs');
        }

        return $data;
    }
}

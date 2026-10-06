<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = Resource::query();
        $viewer = $request->user();
        $canSeeMembers = $viewer && ($viewer->isActiveAccount() || $viewer->hasAdminAccess());

        if (! $canSeeMembers) {
            $query->where('visibility', 'public');
        }

        if ($category = $request->string('category')->toString()) {
            if (array_key_exists($category, Resource::categories())) {
                $query->where('category', $category);
            }
        }

        if ($search = trim($request->string('q')->toString())) {
            $query->where('title', 'like', like_term($search));
        }

        $resources = $query->latest('published_on')->latest('id')->paginate(12)->withQueryString();

        return view('public.resources', [
            'resources' => $resources,
            'categories' => Resource::categories(),
            'canSeeMembers' => $canSeeMembers,
        ]);
    }

    public function download(Request $request, Resource $resource)
    {
        if ($resource->type !== 'file' || ! $resource->file_path) {
            abort(404);
        }

        if ($resource->visibility === 'members') {
            $user = $request->user();
            if (! $user) {
                return redirect()->guest(route('login'));
            }
            if (! $user->isActiveAccount() && ! $user->hasAdminAccess()) {
                abort(403);
            }
        }

        $disk = $resource->disk ?: 'local';
        if (! Storage::disk($disk)->exists($resource->file_path)) {
            abort(404);
        }

        $filename = str($resource->title)->slug().'.'.pathinfo($resource->file_path, PATHINFO_EXTENSION);

        return Storage::disk($disk)->download($resource->file_path, $filename);
    }
}

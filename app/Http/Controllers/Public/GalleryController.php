<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryCategory;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryAlbum::query()->with(['category', 'items'])->withCount('items');

        if ($category = $request->integer('category')) {
            $query->where('gallery_category_id', $category);
        }

        $albums = $query->latest()->paginate(9)->withQueryString();
        $categories = GalleryCategory::query()->orderBy('name')->get();

        return view('public.gallery.index', compact('albums', 'categories'));
    }

    public function show(GalleryAlbum $album)
    {
        $album->load(['items', 'category', 'event']);

        return view('public.gallery.show', compact('album'));
    }
}

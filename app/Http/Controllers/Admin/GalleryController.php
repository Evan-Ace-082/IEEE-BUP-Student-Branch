<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GalleryAlbumRequest;
use App\Models\Event;
use App\Models\GalleryAlbum;
use App\Models\GalleryCategory;
use App\Models\GalleryItem;
use App\Services\ActivityLogger;
use App\Services\SecureUpload;
use App\Support\SettingsStore;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::query()->with('category')->withCount('items')->latest()->paginate(12);

        return view('admin.gallery.index', compact('albums'));
    }

    public function create()
    {
        return view('admin.gallery.form', [
            'album' => new GalleryAlbum,
            'categories' => GalleryCategory::query()->orderBy('name')->get(),
            'events' => Event::query()->orderByDesc('starts_at')->limit(100)->get(),
        ]);
    }

    public function store(GalleryAlbumRequest $request)
    {
        $categoryId = $this->categoryId($request);
        $album = GalleryAlbum::query()->create([
            'title' => $request->string('title')->toString(),
            'slug' => unique_slug(GalleryAlbum::class, $request->string('title')->toString()),
            'description' => $request->input('description') ?: null,
            'gallery_category_id' => $categoryId,
            'event_id' => $request->input('event_id') ?: null,
            'created_by' => $request->user()->id,
        ]);

        $this->storeImages($request, $album);
        ActivityLogger::log('create', 'gallery', $album->id, 'Created gallery album');
        SettingsStore::bust();

        return redirect()->route('admin.gallery.edit', $album)->with('status', 'Album created.');
    }

    public function edit(GalleryAlbum $album)
    {
        $album->load('items');

        return view('admin.gallery.form', [
            'album' => $album,
            'categories' => GalleryCategory::query()->orderBy('name')->get(),
            'events' => Event::query()->orderByDesc('starts_at')->limit(100)->get(),
        ]);
    }

    public function update(GalleryAlbumRequest $request, GalleryAlbum $album)
    {
        $album->update([
            'title' => $request->string('title')->toString(),
            'slug' => unique_slug(GalleryAlbum::class, $request->string('title')->toString(), $album->id),
            'description' => $request->input('description') ?: null,
            'gallery_category_id' => $this->categoryId($request),
            'event_id' => $request->input('event_id') ?: null,
        ]);

        $this->storeImages($request, $album);
        ActivityLogger::log('update', 'gallery', $album->id, 'Updated gallery album');
        SettingsStore::bust();

        return redirect()->route('admin.gallery.edit', $album)->with('status', 'Album saved.');
    }

    public function destroy(GalleryAlbum $album)
    {
        $album->load('items');

        foreach ($album->items as $item) {
            SecureUpload::delete($item->image);
        }
        SecureUpload::delete($album->cover);
        $album->delete();
        ActivityLogger::log('delete', 'gallery', $album->id, 'Deleted gallery album');
        SettingsStore::bust();

        return redirect()->route('admin.gallery.index')->with('status', 'Album removed.');
    }

    public function updateCaption(Request $request, GalleryItem $item)
    {
        $data = $request->validate(['caption' => ['nullable', 'string', 'max:180']]);
        $item->caption = $data['caption'] ?: null;
        $item->save();
        ActivityLogger::log('update', 'gallery', $item->id, 'Updated gallery caption');

        return back()->with('status', 'Caption saved.');
    }

    public function destroyItem(GalleryItem $item)
    {
        $album = $item->album;
        SecureUpload::delete($item->image);
        $item->delete();

        if ($album && $album->cover === $item->image) {
            $album->cover = $album->items()->value('image');
            $album->save();
        }

        ActivityLogger::log('delete', 'gallery', $item->id, 'Deleted gallery image');
        SettingsStore::bust();

        return back()->with('status', 'Image removed.');
    }

    private function categoryId(GalleryAlbumRequest $request): ?int
    {
        $name = trim((string) $request->input('new_category'));
        if ($name !== '') {
            $category = GalleryCategory::query()->firstOrCreate(
                ['slug' => Str::slug($name) ?: 'category'],
                ['name' => $name]
            );

            return $category->id;
        }

        return $request->input('gallery_category_id') ?: null;
    }

    private function storeImages(GalleryAlbumRequest $request, GalleryAlbum $album): void
    {
        $order = (int) $album->items()->max('sort_order');

        foreach ($request->file('images', []) as $image) {
            $order++;
            $path = SecureUpload::image($image, 'gallery');
            $album->items()->create([
                'image' => $path,
                'sort_order' => $order,
            ]);

            if (! $album->cover) {
                $album->cover = $path;
                $album->save();
            }
        }
    }
}

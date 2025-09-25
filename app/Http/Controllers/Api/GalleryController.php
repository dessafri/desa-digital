<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index(): JsonResponse
    {
        $albums = GalleryAlbum::with(['items' => function ($query) {
            $query->orderBy('order');
        }])->latest('updated_at')->get();

        return response()->json($albums);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:gallery_albums,slug',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
        ]);

        $data['slug'] = Str::slug($data['slug'] ?? $data['title']);

        $album = GalleryAlbum::create($data);

        return response()->json($album, 201);
    }

    public function show(GalleryAlbum $galleryAlbum): JsonResponse
    {
        return response()->json($galleryAlbum->load(['items' => function ($query) {
            $query->orderBy('order');
        }]));
    }

    public function showBySlug(string $slug): JsonResponse
    {
        $album = GalleryAlbum::with(['items' => function ($query) {
            $query->orderBy('order');
        }])->where('slug', $slug)->firstOrFail();

        return response()->json($album);
    }

    public function update(Request $request, GalleryAlbum $galleryAlbum): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:gallery_albums,slug,' . $galleryAlbum->id,
            'description' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
        ]);

        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        } elseif (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $galleryAlbum->update($data);

        return response()->json($galleryAlbum->fresh());
    }

    public function destroy(GalleryAlbum $galleryAlbum): JsonResponse
    {
        $galleryAlbum->delete();

        return response()->json(['message' => 'Album deleted']);
    }
}

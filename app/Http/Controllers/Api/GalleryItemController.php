<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryItemController extends Controller
{
    public function index(Request $request)
    {
        $query = GalleryItem::with('album');

        if ($albumId = $request->query('album_id')) {
            $query->where('gallery_album_id', $albumId);
        }

        $items = $query->orderBy('order')->get();

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'gallery_album_id' => 'required|exists:gallery_albums,id',
            'title' => 'nullable|string|max:150',
            'caption' => 'nullable|string',
            'image_url' => 'required|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $item = GalleryItem::create($data);

        return response()->json($item->load('album'), 201);
    }

    public function show(GalleryItem $galleryItem)
    {
        return response()->json($galleryItem->load('album'));
    }

    public function update(Request $request, GalleryItem $galleryItem)
    {
        $data = $request->validate([
            'gallery_album_id' => 'nullable|exists:gallery_albums,id',
            'title' => 'nullable|string|max:150',
            'caption' => 'nullable|string',
            'image_url' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $galleryItem->update($data);

        return response()->json($galleryItem->fresh()->load('album'));
    }

    public function destroy(GalleryItem $galleryItem): JsonResponse
    {
        $galleryItem->delete();

        return response()->json(['message' => 'Gallery item deleted']);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'gallery_album_id' => 'required|exists:gallery_albums,id',
            'files' => 'required',
            'files.*' => 'file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $albumId = (int) $request->input('gallery_album_id');
        $files = $request->file('files');
        if (!$files) {
            return response()->json(['message' => 'No files uploaded'], 422);
        }

        $created = [];
        $baseOrder = (int) (GalleryItem::where('gallery_album_id', $albumId)->max('order') ?? 0);
        $index = 0;
        foreach ($files as $file) {
            $path = $file->store('gallery', 'public');
            $url = Storage::url($path); // "/storage/..."

            $title = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $item = GalleryItem::create([
                'gallery_album_id' => $albumId,
                'title' => $title,
                'caption' => null,
                'image_url' => $url,
                'order' => $baseOrder + $index + 1,
            ]);

            $created[] = $item->fresh();
            $index++;
        }

        return response()->json($created, 201);
    }
}

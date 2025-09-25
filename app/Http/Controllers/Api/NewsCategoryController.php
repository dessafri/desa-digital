<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsCategoryController extends Controller
{
    public function index()
    {
        $categories = NewsCategory::orderBy('name')->get();

        return response()->json($categories);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:news_categories,slug',
            'description' => 'nullable|string',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        $category = NewsCategory::create($data);

        return response()->json($category, 201);
    }

    public function show(NewsCategory $newsCategory)
    {
        return response()->json($newsCategory->loadCount('posts'));
    }

    public function update(Request $request, NewsCategory $newsCategory): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:news_categories,slug,' . $newsCategory->id,
            'description' => 'nullable|string',
        ]);

        if (!empty($data['name']) && empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        } elseif (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        $newsCategory->update($data);

        return response()->json($newsCategory->fresh());
    }

    public function destroy(NewsCategory $newsCategory): JsonResponse
    {
        if ($newsCategory->posts()->exists()) {
            return response()->json([
                'message' => 'Tidak dapat menghapus kategori yang masih memiliki berita aktif.',
            ], 422);
        }

        $newsCategory->delete();

        return response()->json(['message' => 'Category deleted']);
    }
}

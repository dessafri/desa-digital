<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsPost::with('category');

        if (!$request->boolean('include_draft', false)) {
            $query->published();
        } elseif ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        if ($category = $request->query('category')) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category)->orWhere('id', $category);
            });
        }

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->integer('per_page', 6), 50);

        $posts = $query->latest('published_at')->paginate($perPage);

        return response()->json($posts);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'news_category_id' => 'nullable|exists:news_categories,id',
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_posts,slug',
            'excerpt' => 'nullable|string',
            'body' => 'required|string',
            'cover_image' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published',
            'published_at' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        if (($data['status'] ?? 'draft') === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }

        if (!empty($data['meta'])) {
            $data['meta'] = json_encode($data['meta']);
        }

        $post = NewsPost::create($data);

        return response()->json($post->load('category'), 201);
    }

    public function show(NewsPost $news)
    {
        return response()->json($news->load('category'));
    }

    public function showBySlug(string $slug)
    {
        $post = NewsPost::with('category')
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($post);
    }

    public function update(Request $request, NewsPost $news)
    {
        $data = $request->validate([
            'news_category_id' => 'nullable|exists:news_categories,id',
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:news_posts,slug,' . $news->id,
            'excerpt' => 'nullable|string',
            'body' => 'nullable|string',
            'cover_image' => 'nullable|string|max:255',
            'status' => 'nullable|in:draft,published',
            'published_at' => 'nullable|date',
            'meta' => 'nullable|array',
        ]);

        if (!empty($data['title']) && empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['title'], $news->id);
        } elseif (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        if (!empty($data['meta'])) {
            $data['meta'] = json_encode($data['meta']);
        }

        if (($data['status'] ?? $news->status) === 'published' && empty($data['published_at'])) {
            $data['published_at'] = $news->published_at ?? now();
        }

        $news->update($data);

        return response()->json($news->fresh()->load('category'));
    }

    public function destroy(NewsPost $news): JsonResponse
    {
        $news->delete();

        return response()->json(['message' => 'News deleted']);
    }

    protected function generateSlug(string $title, int $ignoreId = null): string
    {
        $slug = Str::slug($title);
        $original = $slug;
        $counter = 1;

        while (NewsPost::where('slug', $slug)
            ->when($ignoreId, function ($query) use ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            })
            ->exists()
        ) {
            $slug = $original . '-' . $counter++;
        }

        return $slug;
    }
}

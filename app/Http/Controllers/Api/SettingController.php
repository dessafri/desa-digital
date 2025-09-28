<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $query = Setting::query();

        if ($keys = $request->query('keys')) {
            $keys = is_array($keys) ? $keys : explode(',', $keys);
            $query->whereIn('key', $keys);
        }

        if ($group = $request->query('group')) {
            $query->where('key', 'like', $group . '%');
        }

        $settings = $query->orderBy('key')->get();

        return response()->json($settings);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'key' => 'required|string|max:255',
            'value' => 'nullable',
            'type' => 'nullable|in:text,json',
        ]);

        $setting = Setting::updateOrCreate(
            ['key' => $data['key']],
            [
                'value' => $this->prepareValue($data['value'] ?? null, $data['type'] ?? 'text'),
                'type' => $data['type'] ?? 'text',
            ]
        );

        return response()->json($setting, 201);
    }

    public function show(Setting $setting)
    {
        return response()->json($setting);
    }

    public function update(Request $request, Setting $setting): JsonResponse
    {
        $data = $request->validate([
            'value' => 'nullable',
            'type' => 'nullable|in:text,json',
        ]);

        $type = $data['type'] ?? $setting->type;

        $setting->update([
            'value' => $this->prepareValue($data['value'] ?? $setting->value, $type),
            'type' => $type,
        ]);

        return response()->json($setting->fresh());
    }

    public function destroy(Setting $setting): JsonResponse
    {
        $setting->delete();

        return response()->json(['message' => 'Setting deleted']);
    }

    protected function prepareValue($value, string $type)
    {
        if ($type === 'json' && is_array($value)) {
            return json_encode($value);
        }

        return $value;
    }

    public function uploadHero(Request $request): JsonResponse
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $files = $request->file('files');
        if (!$files) {
            return response()->json(['message' => 'No files uploaded'], 422);
        }

        $urls = [];
        foreach ($files as $file) {
            $path = $file->store('hero', 'public');
            $urls[] = Storage::url($path);
        }

        return response()->json($urls, 201);
    }

    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);
        $file = $request->file('file');
        $path = $file->store('settings', 'public');
        return response()->json(['url' => Storage::url($path)], 201);
    }
}

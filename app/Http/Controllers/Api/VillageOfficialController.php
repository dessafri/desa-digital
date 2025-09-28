<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VillageOfficial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VillageOfficialController extends Controller
{
    public function index(): JsonResponse
    {
        $officials = VillageOfficial::orderBy('order')->get();

        return response()->json($officials);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'position' => 'required|string|max:150',
            'photo' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'order' => 'nullable|integer|min:0',
        ]);

        $official = VillageOfficial::create($data);

        return response()->json($official, 201);
    }

    public function show(VillageOfficial $villageOfficial): JsonResponse
    {
        return response()->json($villageOfficial);
    }

    public function update(Request $request, VillageOfficial $villageOfficial): JsonResponse
    {
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:150',
            'position' => 'sometimes|required|string|max:150',
            'photo' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email',
            'order' => 'nullable|integer|min:0',
        ]);

        $villageOfficial->update($data);

        return response()->json($villageOfficial);
    }

    public function destroy(VillageOfficial $villageOfficial): JsonResponse
    {
        $villageOfficial->delete();

        return response()->json(['message' => 'Village official deleted']);
    }

    public function uploadPhoto(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->store('officials', 'public');
        $url = Storage::url($path);

        return response()->json(['url' => $url], 201);
    }
}

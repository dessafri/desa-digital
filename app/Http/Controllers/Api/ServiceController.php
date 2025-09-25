<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(): JsonResponse
    {
        $services = Service::orderBy('order')->get();

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:150',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $service = Service::create($data);

        return response()->json($service, 201);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json($service);
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:150',
            'subtitle' => 'nullable|string|max:150',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:150',
            'link' => 'nullable|string|max:255',
            'order' => 'nullable|integer|min:0',
        ]);

        $service->update($data);

        return response()->json($service);
    }

    public function destroy(Service $service): JsonResponse
    {
        $service->delete();

        return response()->json(['message' => 'Service deleted']);
    }
}

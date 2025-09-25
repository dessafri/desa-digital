<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Statistic;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatisticController extends Controller
{
    public function index(): JsonResponse
    {
        $items = Statistic::orderBy('order')->get();

        return response()->json($items);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'label' => 'required|string|max:150',
            'value' => 'required|integer|min:0',
            'icon' => 'nullable|string|max:150',
            'order' => 'nullable|integer|min:0',
        ]);

        $statistic = Statistic::create($data);

        return response()->json($statistic, 201);
    }

    public function show(Statistic $statistic): JsonResponse
    {
        return response()->json($statistic);
    }

    public function update(Request $request, Statistic $statistic): JsonResponse
    {
        $data = $request->validate([
            'label' => 'sometimes|required|string|max:150',
            'value' => 'sometimes|required|integer|min:0',
            'icon' => 'nullable|string|max:150',
            'order' => 'nullable|integer|min:0',
        ]);

        $statistic->update($data);

        return response()->json($statistic);
    }

    public function destroy(Statistic $statistic): JsonResponse
    {
        $statistic->delete();

        return response()->json(['message' => 'Statistic deleted']);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Resources\FoodResource;
use App\Services\FoodRecommendationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function __construct(private readonly FoodRecommendationService $recommendationService) {}

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['nullable', 'string', 'max:100'],
            'max_price' => ['nullable', 'integer', 'min:0', 'max:100000000'],
            'preference' => ['nullable', 'in:any,vegetarian,not_spicy'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:6'],
        ]);

        $foods = $this->recommendationService->recommend(
            $validated['category'] ?? null,
            isset($validated['max_price']) ? (int) $validated['max_price'] : null,
            $validated['preference'] ?? 'any',
            (int) ($validated['limit'] ?? 3),
        );

        return response()->json([
            'message' => $foods->isEmpty()
                ? 'Chưa tìm thấy món phù hợp. Bạn thử nới rộng tiêu chí nhé.'
                : 'Đây là những món phù hợp với lựa chọn của bạn.',
            'data' => FoodResource::collection($foods),
        ]);
    }
}

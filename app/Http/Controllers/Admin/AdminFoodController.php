<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFoodRequest;
use App\Http\Requests\Admin\ToggleFoodAvailabilityRequest;
use App\Http\Requests\Admin\UpdateFoodRequest;
use App\Http\Resources\FoodResource;
use App\Models\Food;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AdminFoodController extends Controller
{
    /**
     * Display a listing of foods for admin (includes unavailable foods).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $search = trim((string) $request->query('search', ''));
        $categoryId = $request->query('category_id');
        $isAvailable = $request->query('is_available');
        $perPage = min((int) $request->query('per_page', 15), 50);

        $query = Food::with('category');

        if (! empty($categoryId)) {
            $query->where('category_id', (int) $categoryId);
        }

        if (! is_null($isAvailable) && $isAvailable !== '') {
            $query->where('is_available', filter_var($isAvailable, FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $foods = $query->latest()->paginate($perPage);

        return FoodResource::collection($foods);
    }

    /**
     * Store a newly created food.
     */
    public function store(StoreFoodRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['is_available'] = (bool) ($data['is_available'] ?? true);

        $food = Food::create($data);

        return (new FoodResource($food->load('category')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified food.
     */
    public function show(Food $food): FoodResource
    {
        return new FoodResource($food->load('category'));
    }

    /**
     * Update the specified food.
     */
    public function update(UpdateFoodRequest $request, Food $food): FoodResource
    {
        $food->update($request->validated());

        return new FoodResource($food->load('category'));
    }

    /**
     * Toggle availability status of the food.
     */
    public function toggleAvailability(ToggleFoodAvailabilityRequest $request, Food $food): FoodResource
    {
        $food->update([
            'is_available' => (bool) $request->validated('is_available'),
        ]);

        return new FoodResource($food->load('category'));
    }
}

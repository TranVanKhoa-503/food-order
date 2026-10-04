<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreFoodRequest;
use App\Http\Requests\Admin\ToggleFoodAvailabilityRequest;
use App\Http\Requests\Admin\UpdateFoodRequest;
use App\Http\Resources\FoodResource;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminFoodController extends Controller
{
    /**
     * Display a listing of foods for admin (includes unavailable foods).
     */
    public function index(Request $request): View|AnonymousResourceCollection
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

        if ($request->expectsJson() || $request->is('api/*')) {
            return FoodResource::collection($foods);
        }

        $categories = Category::all();

        return view('admin.foods.index', compact('foods', 'categories', 'categoryId', 'isAvailable', 'search'));
    }

    /**
     * Store a newly created food.
     */
    public function store(StoreFoodRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image_upload')) {
            $data['image'] = Storage::disk('public')->url($request->file('image_upload')->store('foods', 'public'));
        }
        unset($data['image_upload']);
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
        $data = $request->validated();
        $oldImage = $food->image;
        $newUploadedPath = null;

        if ($request->hasFile('image_upload')) {
            $newUploadedPath = $request->file('image_upload')->store('foods', 'public');
            $data['image'] = Storage::disk('public')->url($newUploadedPath);
        }
        unset($data['image_upload']);

        $food->update($data);

        // Delete old image only after successful DB update
        if ($newUploadedPath !== null && ! empty($oldImage)) {
            $this->deleteOldStoredImage($oldImage);
        }

        return new FoodResource($food->load('category'));
    }

    /**
     * Delete previous stored food image file from public disk, avoiding defaults or external URLs.
     */
    protected function deleteOldStoredImage(?string $imagePath): void
    {
        if (empty($imagePath)) {
            return;
        }

        // Avoid deleting default or placeholder images
        if (str_contains($imagePath, 'default') || str_contains($imagePath, 'placeholder')) {
            return;
        }

        $parsedPath = parse_url($imagePath, PHP_URL_PATH) ?? $imagePath;
        $disk = Storage::disk('public');

        if (str_contains($parsedPath, '/storage/')) {
            $relative = Str::after($parsedPath, '/storage/');
            if ($relative !== '' && $disk->exists($relative)) {
                $disk->delete($relative);
            }
        } elseif (str_starts_with($imagePath, 'foods/') && $disk->exists($imagePath)) {
            $disk->delete($imagePath);
        }
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

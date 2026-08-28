<?php

namespace App\Http\Controllers;

use App\Http\Resources\FoodResource;
use App\Models\Category;
use App\Models\Food;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class FoodController extends Controller
{
    /**
     * Display a listing of foods on the home menu (Web) or as JSON API.
     */
    public function index(Request $request): View|AnonymousResourceCollection
    {
        $request->validate([
            'category' => ['nullable', 'string', 'max:255'],
            'search' => ['nullable', 'string', 'max:100'],
            'min_price' => ['nullable', 'numeric', 'min:0'],
            'max_price' => ['nullable', 'numeric', 'min:0'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $categoryIdOrSlug = $request->query('category');
        $search = trim((string) $request->query('search', ''));
        $minPrice = $request->query('min_price');
        $maxPrice = $request->query('max_price');
        $perPage = min((int) $request->query('per_page', 15), 50);

        $foodsQuery = Food::with('category')->where('is_available', true);

        if (! empty($categoryIdOrSlug)) {
            if (is_numeric($categoryIdOrSlug)) {
                $foodsQuery->where('category_id', (int) $categoryIdOrSlug);
            } else {
                $foodsQuery->whereHas('category', function ($q) use ($categoryIdOrSlug) {
                    $q->where('slug', $categoryIdOrSlug);
                });
            }
        }

        if (! empty($search)) {
            $foodsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! is_null($minPrice) && $minPrice !== '') {
            $foodsQuery->where('price', '>=', (float) $minPrice);
        }

        if (! is_null($maxPrice) && $maxPrice !== '') {
            $foodsQuery->where('price', '<=', (float) $maxPrice);
        }

        if ($request->expectsJson() || $request->is('api/*')) {
            $foods = $foodsQuery->latest()->paginate($perPage);

            return FoodResource::collection($foods);
        }

        $categories = Category::withCount(['foods' => function ($q) {
            $q->where('is_available', true);
        }])->get();

        $foods = $foodsQuery->latest()->get();
        $categoryId = is_numeric($categoryIdOrSlug) ? (int) $categoryIdOrSlug : null;

        return view('home', compact('foods', 'categories', 'categoryId', 'search'));
    }

    /**
     * Display the specified food item.
     */
    public function show(Request $request, Food $food): FoodResource|View
    {
        abort_unless($food->is_available, 404, 'Món ăn không tồn tại hoặc đã tạm dừng phục vụ.');

        $food->load('category');

        if ($request->expectsJson() || $request->is('api/*')) {
            return new FoodResource($food);
        }

        return view('home', [
            'selectedFood' => $food,
            'foods' => Food::with('category')->where('is_available', true)->latest()->get(),
            'categories' => Category::withCount(['foods' => function ($q) {
                $q->where('is_available', true);
            }])->get(),
            'categoryId' => null,
            'search' => '',
        ]);
    }
}

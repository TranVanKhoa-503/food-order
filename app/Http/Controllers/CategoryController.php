<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories (public).
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Category::query();

        if ($request->boolean('with_foods_count')) {
            $query->withCount(['foods' => function ($q) {
                $q->where('is_available', true);
            }]);
        }

        $categories = $query->orderBy('id')->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category by slug or id (public).
     */
    public function show(Category $category): CategoryResource
    {
        $category->loadCount(['foods' => function ($q) {
            $q->where('is_available', true);
        }]);

        return new CategoryResource($category);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    /**
     * Display a listing of foods on the home menu.
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category');
        $search = trim($request->query('search', ''));

        $categories = Category::withCount('foods')->get();

        $foodsQuery = Food::with('category')->where('is_available', true);

        if ($categoryId) {
            $foodsQuery->where('category_id', $categoryId);
        }

        if (! empty($search)) {
            $foodsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $foods = $foodsQuery->latest()->get();

        return view('home', compact('foods', 'categories', 'categoryId', 'search'));
    }
}

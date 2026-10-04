<?php

namespace App\Services;

use App\Models\Food;
use Illuminate\Support\Collection;

class FoodRecommendationService
{
    /**
     * Find available foods matching the visitor's simple preferences.
     *
     * The project does not need an external AI service for this flow. The
     * existing category, price, name and description data are enough for a
     * predictable recommendation suitable for a course project.
     *
     * @return Collection<int, Food>
     */
    public function recommend(?string $category, ?int $maxPrice, string $preference, int $limit = 3): Collection
    {
        $query = Food::query()
            ->with('category')
            ->where('is_available', true);

        $category = trim((string) $category);
        if ($category !== '') {
            if (is_numeric($category)) {
                $query->where('category_id', (int) $category);
            } else {
                $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category));
            }
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        $preference = $this->normalizePreference($preference);
        $foods = $query->get()->filter(function (Food $food) use ($preference): bool {
            return match ($preference) {
                'vegetarian' => $this->isVegetarian($food),
                'not_spicy' => ! $this->isSpicy($food),
                default => true,
            };
        });

        return $foods
            ->sortByDesc(function (Food $food) use ($maxPrice): array {
                $categoryScore = $food->category?->id ?? 0;
                $budgetScore = $maxPrice !== null ? max(0, $maxPrice - (int) $food->price) : 0;

                return [
                    $maxPrice !== null ? 1 : 0,
                    $categoryScore,
                    $budgetScore,
                    $food->created_at?->timestamp ?? 0,
                ];
            })
            ->take($limit)
            ->values();
    }

    private function normalizePreference(string $preference): string
    {
        return in_array($preference, ['vegetarian', 'not_spicy'], true) ? $preference : 'any';
    }

    private function isVegetarian(Food $food): bool
    {
        $text = $this->foodText($food);

        return $food->category?->slug === 'mon-chay'
            || preg_match('/\b(chay|rau|nấm|nam|salad|đậu|dau|hạt sen|hat sen|trái cây|trai cay|bơ|bo)\b/iu', $text) === 1;
    }

    private function isSpicy(Food $food): bool
    {
        return preg_match('/\b(cay|ớt|ot|tiêu|tieu)\b/iu', $this->foodText($food)) === 1;
    }

    private function foodText(Food $food): string
    {
        return implode(' ', [
            $food->name,
            $food->description,
            $food->category?->name,
        ]);
    }
}

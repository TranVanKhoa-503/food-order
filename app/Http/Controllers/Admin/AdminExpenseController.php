<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OperatingExpense;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminExpenseController extends Controller
{
    /**
     * Display a listing of operating expenses.
     */
    public function index(Request $request): View|JsonResponse
    {
        $category = $request->query('category');
        $from = $request->query('from');
        $to = $request->query('to');
        $perPage = min((int) $request->query('per_page', 20), 100);

        $query = OperatingExpense::with('creator')->latest('expense_date');

        if (! empty($category)) {
            $query->where('category', $category);
        }

        if (! empty($from)) {
            $query->whereDate('expense_date', '>=', $from);
        }

        if (! empty($to)) {
            $query->whereDate('expense_date', '<=', $to);
        }

        $expenses = $query->paginate($perPage)->withQueryString();

        // Calculate summary
        $totalExpense = (int) OperatingExpense::query()
            ->when($category, fn ($q) => $q->where('category', $category))
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->sum('amount');

        $byCategory = OperatingExpense::query()
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->selectRaw('category, SUM(amount) as total_amount, COUNT(*) as count')
            ->groupBy('category')
            ->get();

        $categories = OperatingExpense::CATEGORIES;

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => [
                    'total_expense' => $totalExpense,
                    'by_category' => $byCategory,
                    'expenses' => $expenses,
                ],
            ]);
        }

        return view('admin.expenses.index', compact(
            'expenses',
            'categories',
            'category',
            'from',
            'to',
            'totalExpense',
            'byCategory',
        ));
    }

    /**
     * Store a newly created operating expense.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in(array_keys(OperatingExpense::CATEGORIES))],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense = OperatingExpense::create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Tạo khoản chi phí thành công.',
                'data' => $expense->load('creator'),
            ], 201);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Đã thêm chi phí vận hành thành công.');
    }

    /**
     * Update the specified operating expense.
     */
    public function update(Request $request, OperatingExpense $expense): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', Rule::in(array_keys(OperatingExpense::CATEGORIES))],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $expense->update($validated);

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'message' => 'Cập nhật chi phí thành công.',
                'data' => $expense->fresh('creator'),
            ]);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Đã cập nhật chi phí thành công.');
    }

    /**
     * Remove the specified operating expense.
     */
    public function destroy(Request $request, OperatingExpense $expense): JsonResponse|RedirectResponse
    {
        $expense->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Đã xóa chi phí thành công.']);
        }

        return redirect()->route('admin.expenses.index')->with('success', 'Đã xóa chi phí thành công.');
    }
}

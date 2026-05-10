<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseCategoryRequest;
use App\Http\Requests\Expense\UpdateExpenseCategoryRequest;
use App\Http\Resources\ExpenseCategoryResource;
use App\Models\ExpenseCategory;
use Illuminate\Http\JsonResponse;

class ExpenseCategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = ExpenseCategory::withCount('expenses')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Expense categories retrieved successfully',
            'data'    => ExpenseCategoryResource::collection($categories),
        ]);
    }

    public function store(StoreExpenseCategoryRequest $request): JsonResponse
    {
        $category = ExpenseCategory::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Expense category created successfully',
            'data'    => new ExpenseCategoryResource($category),
        ], 201);
    }

    public function update(UpdateExpenseCategoryRequest $request, ExpenseCategory $expenseCategory): JsonResponse
    {
        $expenseCategory->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Expense category updated successfully',
            'data'    => new ExpenseCategoryResource($expenseCategory->fresh()),
        ]);
    }

    public function destroy(ExpenseCategory $expenseCategory): JsonResponse
    {
        // Restrict delete if category has expenses (mirrors DB ON DELETE RESTRICT)
        if ($expenseCategory->expenses()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete category that has expenses.',
            ], 409);
        }

        $expenseCategory->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense category deleted successfully',
        ]);
    }
}

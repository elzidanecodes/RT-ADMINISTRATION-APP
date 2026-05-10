<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Requests\Expense\UpdateExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use App\Services\FileUploadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(private FileUploadService $fileUpload) {}

    public function index(Request $request): JsonResponse
    {
        $expenses = Expense::with(['category', 'creator'])
            ->when($request->date_from,   fn ($q, $d) => $q->where('expense_date', '>=', $d))
            ->when($request->date_to,     fn ($q, $d) => $q->where('expense_date', '<=', $d))
            ->when($request->category_id, fn ($q, $c) => $q->where('expense_category_id', $c))
            ->orderByDesc('expense_date')
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'message' => 'Expenses retrieved successfully',
            'data'    => ExpenseResource::collection($expenses),
            'meta'    => [
                'current_page' => $expenses->currentPage(),
                'per_page'     => $expenses->perPage(),
                'total'        => $expenses->total(),
                'last_page'    => $expenses->lastPage(),
            ],
        ]);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt_photo')) {
            $data['receipt_photo_path'] = $this->fileUpload->storeReceipt($request->file('receipt_photo'));
        }

        unset($data['receipt_photo']);
        $data['created_by'] = $request->user()->id;

        $expense = Expense::create($data);
        $expense->load(['category', 'creator']);

        return response()->json([
            'success' => true,
            'message' => 'Expense created successfully',
            'data'    => new ExpenseResource($expense),
        ], 201);
    }

    public function show(Expense $expense): JsonResponse
    {
        $expense->load(['category', 'creator']);

        return response()->json([
            'success' => true,
            'data'    => new ExpenseResource($expense),
        ]);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('receipt_photo')) {
            $this->fileUpload->deleteFile($expense->receipt_photo_path);
            $data['receipt_photo_path'] = $this->fileUpload->storeReceipt($request->file('receipt_photo'));
            unset($data['receipt_photo']);
        }

        $expense->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Expense updated successfully',
            'data'    => new ExpenseResource($expense->fresh()->load(['category', 'creator'])),
        ]);
    }

    public function destroy(Expense $expense): JsonResponse
    {
        $this->fileUpload->deleteFile($expense->receipt_photo_path);
        $expense->delete();

        return response()->json([
            'success' => true,
            'message' => 'Expense deleted successfully',
        ]);
    }
}

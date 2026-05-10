<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Bill\GenerateBillRequest;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Services\BillingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function __construct(private BillingService $billingService) {}

    public function index(Request $request): JsonResponse
    {
        $bills = Bill::with(['house', 'resident'])
            ->withSum('payments', 'amount_paid')
            ->when($request->period_year,  fn ($q, $y) => $q->where('period_year', $y))
            ->when($request->period_month, fn ($q, $m) => $q->where('period_month', $m))
            ->when($request->status,       fn ($q, $s) => $q->where('status', $s))
            ->when($request->house_id,     fn ($q, $h) => $q->where('house_id', $h))
            ->when($request->resident_id,  fn ($q, $r) => $q->where('resident_id', $r))
            ->when($request->bill_type,    fn ($q, $t) => $q->where('bill_type', $t))
            ->orderByDesc('period_year')
            ->orderByDesc('period_month')
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'message' => 'Bills retrieved successfully',
            'data'    => BillResource::collection($bills),
            'meta'    => [
                'current_page' => $bills->currentPage(),
                'per_page'     => $bills->perPage(),
                'total'        => $bills->total(),
                'last_page'    => $bills->lastPage(),
            ],
        ]);
    }

    public function show(Bill $bill): JsonResponse
    {
        $bill->load(['house', 'resident', 'payments.recorder']);

        return response()->json([
            'success' => true,
            'data'    => new BillResource($bill),
        ]);
    }

    public function generate(GenerateBillRequest $request): JsonResponse
    {
        $result = $this->billingService->generateMonthlyBills(
            $request->integer('period_year'),
            $request->integer('period_month')
        );

        $monthName = \Carbon\Carbon::create($result['period'])->translatedFormat('F Y');

        return response()->json([
            'success' => true,
            'message' => "Generated {$result['created']} bills for {$monthName}",
            'data'    => [
                'created_count' => $result['created'],
                'skipped_count' => $result['skipped'],
                'period'        => $result['period'],
            ],
        ], 201);
    }
}

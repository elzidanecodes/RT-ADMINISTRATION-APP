<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\PayAnnualRequest;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Bill;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(private PaymentService $paymentService) {}

    public function index(Request $request): JsonResponse
    {
        $payments = Payment::with(['bill', 'recorder'])
            ->when($request->date_from, fn ($q, $d) => $q->where('payment_date', '>=', $d))
            ->when($request->date_to,   fn ($q, $d) => $q->where('payment_date', '<=', $d))
            ->when($request->bill_id,   fn ($q, $b) => $q->where('bill_id', $b))
            ->orderByDesc('payment_date')
            ->paginate($request->integer('per_page', 25));

        return response()->json([
            'success' => true,
            'message' => 'Payments retrieved successfully',
            'data'    => PaymentResource::collection($payments),
            'meta'    => [
                'current_page' => $payments->currentPage(),
                'per_page'     => $payments->perPage(),
                'total'        => $payments->total(),
                'last_page'    => $payments->lastPage(),
            ],
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $bill = Bill::findOrFail($request->integer('bill_id'));

        try {
            $payment = $this->paymentService->recordPayment(
                $bill,
                $request->validated(),
                $request->user()->id
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
        }

        $payment->load(['bill', 'recorder']);

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded successfully',
            'data'    => new PaymentResource($payment),
        ], 201);
    }

    public function payAnnual(PayAnnualRequest $request): JsonResponse
    {
        $billType = $request->bill_type;
        $year     = $request->integer('year');

        try {
            $result = $this->paymentService->payAnnual(
                $request->validated(),
                $request->user()->id
            );
        } catch (\RuntimeException $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 409);
        }

        return response()->json([
            'success' => true,
            'message' => "Annual payment recorded for {$billType} {$year}",
            'data'    => $result,
        ], 201);
    }
}

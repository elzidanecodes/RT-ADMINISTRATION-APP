<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function __construct(private BillingService $billingService) {}

    public function recordPayment(Bill $bill, array $data, int $recordedBy): Payment
    {
        if ($bill->status === 'paid') {
            throw new \RuntimeException('Bill is already fully paid.');
        }

        return DB::transaction(function () use ($bill, $data, $recordedBy) {
            $payment = Payment::create([
                'bill_id'          => $bill->id,
                'amount_paid'      => $data['amount_paid'],
                'payment_date'     => $data['payment_date'],
                'payment_method'   => $data['payment_method'],
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
                'recorded_by'      => $recordedBy,
            ]);

            $this->billingService->updateBillStatus($bill);

            return $payment;
        });
    }

    public function payAnnual(array $data, int $recordedBy): array
    {
        return DB::transaction(function () use ($data, $recordedBy) {
            $houseId    = $data['house_id'];
            $residentId = $data['resident_id'];
            $billType   = $data['bill_type'];
            $year       = $data['year'];

            $amount = $billType === 'security'
                ? Bill::SECURITY_AMOUNT
                : Bill::CLEANING_AMOUNT;

            $paymentsCreated = 0;
            $totalPaid       = 0;

            foreach (range(1, 12) as $month) {
                // Ensure bill exists for each month
                $bill = Bill::firstOrCreate(
                    [
                        'house_id'     => $houseId,
                        'resident_id'  => $residentId,
                        'bill_type'    => $billType,
                        'period_year'  => $year,
                        'period_month' => $month,
                    ],
                    [
                        'amount'   => $amount,
                        'due_date' => Carbon::create($year, $month, 10)->toDateString(),
                        'status'   => 'unpaid',
                    ]
                );

                // Skip months that are already fully paid
                if ($bill->status === 'paid') continue;

                Payment::create([
                    'bill_id'          => $bill->id,
                    'amount_paid'      => $amount,
                    'payment_date'     => $data['payment_date'],
                    'payment_method'   => $data['payment_method'],
                    'reference_number' => $data['reference_number'] ?? null,
                    'recorded_by'      => $recordedBy,
                ]);

                $bill->update(['status' => 'paid']);
                $paymentsCreated++;
                $totalPaid += $amount;
            }

            return [
                'total_paid'       => $totalPaid,
                'bills_count'      => 12,
                'payments_created' => $paymentsCreated,
            ];
        });
    }
}

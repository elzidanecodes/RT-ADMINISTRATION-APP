<?php

namespace App\Services;

use App\Models\Bill;
use App\Models\House;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function generateMonthlyBills(int $year, int $month): array
    {
        return DB::transaction(function () use ($year, $month) {
            $created = 0;
            $skipped = 0;

            // Only occupied houses can have bills
            $occupiedHouses = House::where('status', 'occupied')
                ->with('activeResidents')
                ->get();

            foreach ($occupiedHouses as $house) {
                $resident = $house->activeResidents->first();
                if (! $resident) continue;

                $amounts = [
                    'security' => Bill::SECURITY_AMOUNT,
                    'cleaning' => Bill::CLEANING_AMOUNT,
                ];

                foreach ($amounts as $type => $amount) {
                    $bill = Bill::firstOrCreate(
                        [
                            'house_id'     => $house->id,
                            'resident_id'  => $resident->id,
                            'bill_type'    => $type,
                            'period_year'  => $year,
                            'period_month' => $month,
                        ],
                        [
                            'amount'   => $amount,
                            'due_date' => Carbon::create($year, $month, 10)->toDateString(),
                            'status'   => 'unpaid',
                        ]
                    );

                    $bill->wasRecentlyCreated ? $created++ : $skipped++;
                }
            }

            return [
                'created' => $created,
                'skipped' => $skipped,
                'period'  => sprintf('%04d-%02d', $year, $month),
            ];
        });
    }

    // Recalculate bill status based on total payments received
    public function updateBillStatus(Bill $bill): void
    {
        $totalPaid = (float) $bill->payments()->sum('amount_paid');
        $amount    = (float) $bill->amount;

        $bill->status = match (true) {
            $totalPaid >= $amount => 'paid',
            $totalPaid > 0        => 'partial',
            default               => 'unpaid',
        };

        $bill->save();
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // withSum() sets payments_sum_amount_paid (index); load('payments') uses the accessor (show)
        $totalPaid = isset($this->payments_sum_amount_paid)
            ? (float) $this->payments_sum_amount_paid
            : $this->total_paid;
        $remaining = max(0, (float) $this->amount - $totalPaid);

        return [
            'id'           => $this->id,
            'house'        => $this->whenLoaded('house', fn () => [
                'id'           => $this->house->id,
                'house_number' => $this->house->house_number,
                'block'        => $this->house->block,
            ]),
            'resident'     => $this->whenLoaded('resident', fn () => [
                'id'        => $this->resident->id,
                'full_name' => $this->resident->full_name,
            ]),
            'bill_type'    => $this->bill_type,
            'amount'       => number_format((float) $this->amount, 2, '.', ''),
            'period_year'  => $this->period_year,
            'period_month' => $this->period_month,
            'due_date'     => $this->due_date?->toDateString(),
            'status'       => $this->status,
            'total_paid'   => number_format($totalPaid, 2, '.', ''),
            'remaining'    => number_format($remaining, 2, '.', ''),
            'payments'     => $this->whenLoaded('payments', PaymentResource::collection($this->payments)),
        ];
    }
}

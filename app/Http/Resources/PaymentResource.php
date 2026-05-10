<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'bill_id'          => $this->bill_id,
            'bill'             => $this->whenLoaded('bill', fn () => [
                'id'           => $this->bill->id,
                'bill_type'    => $this->bill->bill_type,
                'period_year'  => $this->bill->period_year,
                'period_month' => $this->bill->period_month,
                'status'       => $this->bill->status,
            ]),
            'amount_paid'      => number_format((float) $this->amount_paid, 2, '.', ''),
            'payment_date'     => $this->payment_date?->toDateString(),
            'payment_method'   => $this->payment_method,
            'reference_number' => $this->reference_number,
            'notes'            => $this->notes,
            'recorded_by'      => $this->whenLoaded('recorder', fn () => [
                'id'   => $this->recorder->id,
                'name' => $this->recorder->name,
            ]),
            'created_at'       => $this->created_at?->toDateTimeString(),
        ];
    }
}

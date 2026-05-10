<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $activeResident = $this->activeResidents->first();

        return [
            'id'             => $this->id,
            'house_number'   => $this->house_number,
            'block'          => $this->block,
            'address'        => $this->address,
            'ownership_type' => $this->ownership_type,
            'status'         => $this->status,
            'notes'          => $this->notes,
            'active_resident' => $activeResident ? [
                'id'            => $activeResident->id,
                'full_name'     => $activeResident->full_name,
                'phone_number'  => $activeResident->phone_number,
                'resident_type' => $activeResident->resident_type,
                'start_date'    => $activeResident->pivot->start_date,
            ] : null,
            'resident_history' => $this->residents->map(fn ($r) => [
                'id'         => $r->id,
                'full_name'  => $r->full_name,
                'start_date' => $r->pivot->start_date,
                'end_date'   => $r->pivot->end_date,
                'is_active'  => (bool) $r->pivot->is_active,
                'notes'      => $r->pivot->notes,
            ]),
            'bills_summary' => [
                'total_bills' => $this->bills->count(),
                'paid'        => $this->bills->where('status', 'paid')->count(),
                'unpaid'      => $this->bills->where('status', 'unpaid')->count(),
                'partial'     => $this->bills->where('status', 'partial')->count(),
            ],
        ];
    }
}

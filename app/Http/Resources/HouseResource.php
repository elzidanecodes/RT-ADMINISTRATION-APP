<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HouseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'house_number'   => $this->house_number,
            'block'          => $this->block,
            'address'        => $this->address,
            'ownership_type' => $this->ownership_type,
            'status'         => $this->status,
            'notes'          => $this->notes,
            'active_resident' => $this->whenLoaded('activeResidents', function () {
                $resident = $this->activeResidents->first();
                if (! $resident) return null;

                return [
                    'id'            => $resident->id,
                    'full_name'     => $resident->full_name,
                    'phone_number'  => $resident->phone_number,
                    'resident_type' => $resident->resident_type,
                    'start_date'    => $resident->pivot->start_date,
                ];
            }),
        ];
    }
}

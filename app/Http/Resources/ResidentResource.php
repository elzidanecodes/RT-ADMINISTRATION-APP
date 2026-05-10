<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ResidentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'full_name'     => $this->full_name,
            'phone_number'  => $this->phone_number,
            'resident_type' => $this->resident_type,
            'is_married'    => $this->is_married,
            'ktp_photo_url' => $this->ktp_photo_path
                ? Storage::disk('public')->url($this->ktp_photo_path)
                : null,
            'current_house' => $this->whenLoaded('currentHouse', function () {
                return $this->currentHouse->map(fn ($house) => [
                    'id'           => $house->id,
                    'house_number' => $house->house_number,
                    'block'        => $house->block,
                    'start_date'   => $house->pivot->start_date,
                ]);
            }),
            'created_at' => $this->created_at?->toDateString(),
        ];
    }
}

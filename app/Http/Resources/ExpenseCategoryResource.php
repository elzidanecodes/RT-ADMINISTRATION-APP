<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseCategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'frequency'   => $this->frequency,
            'description' => $this->description,
            'expenses_count' => $this->when(
                isset($this->expenses_count),
                $this->expenses_count
            ),
        ];
    }
}

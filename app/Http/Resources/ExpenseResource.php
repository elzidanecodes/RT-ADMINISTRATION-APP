<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ExpenseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'category'     => $this->whenLoaded('category', fn () => [
                'id'        => $this->category->id,
                'name'      => $this->category->name,
                'frequency' => $this->category->frequency,
            ]),
            'title'        => $this->title,
            'amount'       => number_format((float) $this->amount, 2, '.', ''),
            'expense_date' => $this->expense_date?->toDateString(),
            'description'  => $this->description,
            'receipt_url'  => $this->receipt_photo_path
                ? Storage::disk('public')->url($this->receipt_photo_path)
                : null,
            'created_by'   => $this->whenLoaded('creator', fn () => [
                'id'   => $this->creator->id,
                'name' => $this->creator->name,
            ]),
            'created_at'   => $this->created_at?->toDateString(),
        ];
    }
}

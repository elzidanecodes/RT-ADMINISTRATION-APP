<?php

namespace App\Http\Requests\House;

use Illuminate\Foundation\Http\FormRequest;

class StoreHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_number'   => ['required', 'string', 'max:20', 'unique:houses,house_number'],
            'block'          => ['nullable', 'string', 'max:10'],
            'address'        => ['nullable', 'string'],
            'ownership_type' => ['required', 'in:permanent,rental'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}

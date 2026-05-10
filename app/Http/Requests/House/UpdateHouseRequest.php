<?php

namespace App\Http\Requests\House;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateHouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_number'   => ['sometimes', 'string', 'max:20', Rule::unique('houses', 'house_number')->ignore($this->house)],
            'block'          => ['nullable', 'string', 'max:10'],
            'address'        => ['nullable', 'string'],
            'ownership_type' => ['sometimes', 'in:permanent,rental'],
            'notes'          => ['nullable', 'string'],
        ];
    }
}

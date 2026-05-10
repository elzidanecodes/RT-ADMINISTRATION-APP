<?php

namespace App\Http\Requests\House;

use Illuminate\Foundation\Http\FormRequest;

class AssignResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'resident_id' => ['required', 'integer', 'exists:residents,id'],
            'start_date'  => ['required', 'date', 'date_format:Y-m-d'],
            'notes'       => ['nullable', 'string'],
        ];
    }
}

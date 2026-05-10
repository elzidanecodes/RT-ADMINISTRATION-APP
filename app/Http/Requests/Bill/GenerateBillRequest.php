<?php

namespace App\Http\Requests\Bill;

use Illuminate\Foundation\Http\FormRequest;

class GenerateBillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'period_year'  => ['required', 'integer', 'min:2020', 'max:2099'],
            'period_month' => ['required', 'integer', 'min:1', 'max:12'],
        ];
    }
}

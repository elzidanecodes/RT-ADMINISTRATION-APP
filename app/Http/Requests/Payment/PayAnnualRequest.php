<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class PayAnnualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'house_id'         => ['required', 'integer', 'exists:houses,id'],
            'resident_id'      => ['required', 'integer', 'exists:residents,id'],
            'bill_type'        => ['required', 'in:security,cleaning'],
            'year'             => ['required', 'integer', 'min:2020', 'max:2099'],
            'payment_date'     => ['required', 'date', 'date_format:Y-m-d'],
            'payment_method'   => ['required', 'in:cash,transfer'],
            'reference_number' => ['nullable', 'string', 'max:255'],
        ];
    }
}

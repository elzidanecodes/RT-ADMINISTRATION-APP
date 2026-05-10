<?php

namespace App\Http\Requests\Payment;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bill_id'          => ['required', 'integer', 'exists:bills,id'],
            'amount_paid'      => ['required', 'numeric', 'min:1'],
            'payment_date'     => ['required', 'date', 'date_format:Y-m-d'],
            'payment_method'   => ['required', 'in:cash,transfer'],
            'reference_number' => ['nullable', 'string', 'max:255'],
            'notes'            => ['nullable', 'string'],
        ];
    }
}

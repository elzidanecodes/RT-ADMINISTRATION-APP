<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'title'               => ['required', 'string', 'max:255'],
            'amount'              => ['required', 'numeric', 'min:1'],
            'expense_date'        => ['required', 'date', 'date_format:Y-m-d'],
            'description'         => ['nullable', 'string'],
            'receipt_photo'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }
}

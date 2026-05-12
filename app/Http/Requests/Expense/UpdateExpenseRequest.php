<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_category_id' => ['sometimes', 'integer', 'exists:expense_categories,id'],
            'title'               => ['sometimes', 'string', 'max:255'],
            'amount'              => ['sometimes', 'numeric', 'min:1'],
            'expense_date'        => ['sometimes', 'date', 'date_format:Y-m-d'],
            'description'         => ['nullable', 'string'],
            'receipt_photo'       => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:2048'],
        ];
    }
}

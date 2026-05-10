<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateExpenseCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes', 'string', 'max:255', Rule::unique('expense_categories', 'name')->ignore($this->expense_category)],
            'frequency'   => ['sometimes', 'in:monthly,occasional'],
            'description' => ['nullable', 'string'],
        ];
    }
}

<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;

class UpdateResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'     => ['sometimes', 'string', 'max:255'],
            'phone_number'  => ['sometimes', 'string', 'max:20'],
            'resident_type' => ['sometimes', 'in:permanent,contract'],
            'is_married'    => ['sometimes', 'boolean'],
            'ktp_photo'     => ['sometimes', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}

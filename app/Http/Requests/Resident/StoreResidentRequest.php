<?php

namespace App\Http\Requests\Resident;

use Illuminate\Foundation\Http\FormRequest;

class StoreResidentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name'     => ['required', 'string', 'max:255'],
            'phone_number'  => ['required', 'string', 'max:20'],
            'resident_type' => ['required', 'in:permanent,contract'],
            'is_married'    => ['required', 'boolean'],
            'ktp_photo'     => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}

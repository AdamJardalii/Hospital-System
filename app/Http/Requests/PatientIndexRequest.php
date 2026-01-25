<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PatientIndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search'   => ['nullable', 'string', 'max:100'],
            'gender'   => ['nullable', 'in:male,female,other'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function validatedFilters(): array
    {
        return $this->only(['search', 'gender']);
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

use App\Enums\{BloodType,Gender};


class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'date_of_birth' => 'required|date|before_or_equal:today',
            'gender' => ['required',new Enum(Gender::class)],
            'phone' => 'required|string|max:20',
            'medical_history' => 'nullable|array',
            'allergies' => 'nullable|array',
            'current_medications' => 'nullable|array',
            'blood_type' => ['nullable',new Enum(BloodType::class)],
            'insurance'               => 'sometimes|nullable|array',
            'insurance.provider_name' => 'nullable|string|max:255',
            'insurance.policy_number' => 'nullable|string|max:255',
            'insurance.expiry_date'   => 'nullable|date',
        ];
    }
}

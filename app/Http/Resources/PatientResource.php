<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\InsuranceResource;
class PatientResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'full_name' => "{$this->first_name} {$this->last_name}",
            'contact' => [
                'email' => $this->email,
                'phone' => $this->phone,
                'date_of_birth' => $this->date_of_birth,
                'gender' => $this->gender,
                'address' => $this->address,
                'medical_history' => $this->medical_history,
                'allergies' => $this->allergies,
                'current_medications' => $this->current_medications,
                'blood_type' => $this->blood_type,
                'emergency_contact' => $this->emergency_contact,
                'emergency_phone' => $this->emergency_phone,
            ],
            'insurance' => new InsuranceResource($this->whenLoaded('insuranceCard')),
            'created_at' => $this->created_at?->format('Y-m-d') ?? null,
        ];
    }
}
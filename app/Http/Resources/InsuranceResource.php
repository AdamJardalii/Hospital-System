<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InsuranceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'policy_number' => $this->policy_number,
            'provider_name' => $this->provider_name,
            'group_number' => $this->group_number,
            'expiry_date' => $this->expiry_date,
            'created_at' => $this->created_at?->format('Y-m-d') ?? null,
        ];
    }
}
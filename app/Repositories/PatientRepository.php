<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PatientRepository
{
    /**
     * Get filtered and paginated patients.
     */
    public function getPaginated(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        return Patient::query()
            ->with('insuranceCard')
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($filters['gender'] ?? null, fn($q, $gender) => $q->where('gender', $gender))
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Create patient and related insurance in one transaction.
     */
    public function createWithInsurance(array $data): Patient
    {
        return DB::transaction(function () use ($data) {
            $patient = Patient::create($data);

            if (!empty($data['insurance_policy_number'])) {
                $patient->insuranceCard()->create([
                    'policy_number' => $data['insurance_policy_number'],
                    'provider_name' => $data['insurance_provider'] ?? 'Unknown',
                    'expiry_date'   => $data['insurance_expiry_date'] ?? null,
                ]);
            }

            return $patient->load('insuranceCard');
        });
    }

    /**
     * Update existing patient record.
     */
    public function update(Patient $patient, array $data): Patient
    {
        return DB::transaction(function () use ($patient, $data) {
            $patient->update($data);

            if (isset($data['insurance_policy_number'])) {
                $patient->insuranceCard()->updateOrCreate(
                    ['patient_id' => $patient->id],
                    [
                        'policy_number' => $data['insurance_policy_number'],
                        'provider_name' => $data['insurance_provider'] ?? 'Unknown',
                    ]
                );
            }

            return $patient->fresh('insuranceCard');
        });
    }

    /**
     * Delete a patient record.
     */
    public function delete(Patient $patient): bool
    {
        return $patient->delete();
    }
}
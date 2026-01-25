<?php

namespace App\Repositories;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

use App\Models\{Patient,User};

class PatientRepository
{
    /**
     * Get filtered and paginated patients.
     */
    public function getPaginated(array $filters,User $user, int $perPage = 15): LengthAwarePaginator
    {
        return Patient::query()
            ->with('insuranceCard')
            ->where('user_id',$user->id)
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
    public function createWithInsurance(array $data,User $user): Patient
    {
        return DB::transaction(function () use ($data,$user) {
            $patient = Patient::create([
                ...$data,
                'user_id' => $user->id,
            ]);

            if (!empty($data['insurance'])) {
                $patient->insuranceCard()->create([
                    'policy_number' => $data['insurance']['policy_number'],
                    'provider_name' => $data['insurance']['provider_name'] ?? 'Unknown',
                    'expiry_date'   => $data['insurance']['expiry_date'] ?? null,
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

            if (isset($data['insurance'])) {
                $patient->insuranceCard()->updateOrCreate(
                    ['patient_id' => $patient->id],
                    [
                        'policy_number' => $data['insurance']['policy_number'],
                        'provider_name' => $data['insurance']['provider_name'] ?? 'Unknown',
                        'expiry_date'   => $data['insurance']['expiry_date'] ?? null,
                    ]
                );
            }
            else{
                $patient->insuranceCard()->delete();
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
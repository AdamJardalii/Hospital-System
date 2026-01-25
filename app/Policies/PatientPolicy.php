<?php

namespace App\Policies;

use App\Models\{Patient,User};

class PatientPolicy
{
    /**
     * View a single patient
     */
    public function view(User $user, Patient $patient): bool
    {
        return $patient->user_id === $user->id;
    }

    /**
     * Create patient
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Update patient
     */
    public function update(User $user, Patient $patient): bool
    {
        return $patient->user_id === $user->id;
    }

    /**
     * Delete patient
     */
    public function delete(User $user, Patient $patient): bool
    {
        return $patient->user_id === $user->id;
    }

    /**
     * Restore patient
     */
    public function restore(User $user, Patient $patient): bool
    {
        return $patient->user_id === $user->id;
    }

    /**
     * Force delete patient
     */
    public function forceDelete(User $user, Patient $patient): bool
    {
        return $patient->user_id === $user->id;
    }
}
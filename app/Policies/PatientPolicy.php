<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

class PatientPolicy
{
    /**
     * Determine if user can view any patients
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine if user can view the patient
     */
    public function view(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine if user can create patients
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine if user can update the patient
     */
    public function update(User $user, Patient $patient): bool
    {
        return $user->isAdmin() || $user->isStaff();
    }

    /**
     * Determine if user can delete the patient
     */
    public function delete(User $user, Patient $patient): bool
    {
        // Only admins can delete
        return $user->isAdmin();
    }

    /**
     * Determine if user can restore the patient
     */
    public function restore(User $user, Patient $patient): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if user can permanently delete the patient
     */
    public function forceDelete(User $user, Patient $patient): bool
    {
        return $user->isAdmin();
    }
}
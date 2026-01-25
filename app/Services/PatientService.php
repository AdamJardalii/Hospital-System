<?php
namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Repositories\PatientRepository;
use App\Models\{Patient,User};

class PatientService
{
    public function __construct(protected PatientRepository $repository) {}
    public function getAllPatients(array $filters,User $user): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters,$user);
    }
    public function createPatient(array $data,User $user): Patient
    {
        return $this->repository->createWithInsurance($data,$user);
    }

    public function updatePatient(Patient $patient, array $data): Patient
    {
        return $this->repository->update($patient, $data);
    }


    public function removePatient(Patient $patient): bool
    {
        return $this->repository->delete($patient);
    }
}
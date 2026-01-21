<?php
namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Repositories\PatientRepository;
use App\Models\Patient;

class PatientService
{
    public function __construct(protected PatientRepository $repository) {}
    public function getAllPatients(array $filters): LengthAwarePaginator
    {
        return $this->repository->getPaginated($filters);
    }
    public function createPatient(array $data): Patient
    {
        return $this->repository->createWithInsurance($data);
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
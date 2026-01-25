<?php
namespace App\Http\Controllers;

use Exception;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Http\Requests\{StorePatientRequest,UpdatePatientRequest,PatientIndexRequest};
use App\Http\Resources\PatientResource;

use App\Services\PatientService;
use App\Models\Patient;

class PatientController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected PatientService $service) {}

    public function index(PatientIndexRequest $request): AnonymousResourceCollection
    {
        try {
            $patients = $this->service->getAllPatients($request->validatedFilters(),$request->user());
            return PatientResource::collection($patients);
        } 
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized.'
            ], 403);
        }
        catch (Exception $e) {
            Log::error('Error fetching patients: ' . $e->getMessage()); 
            return PatientResource::collection(collect());
        }
    }

    public function store(StorePatientRequest $request): PatientResource|JsonResponse
    {
        try{
            $this->authorize('create', Patient::class);
            $patient = $this->service->createPatient($request->validated(),$request->user());
            return new PatientResource($patient);
        } 
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized.'
            ], 403);
        }
        catch (Exception $e) {
            Log::error('Error creating patient: ' . $e->getMessage());
            return response()->json(['message' => 'Error creating patient'], 500);
        }
    }

    public function show(Patient $patient): PatientResource|JsonResponse
    {
        try{
            $this->authorize('view', $patient);
            return new PatientResource($patient->load('insuranceCard'));
        } 
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized.'
            ], 403);
        }
        catch (Exception $e) {
            Log::error('Error fetching patient: ' . $e->getMessage());
            return response()->json(['message' => 'Patient not found or inaccessible.'], 404);
        }
    }

    public function update(UpdatePatientRequest $request, Patient $patient): PatientResource|JsonResponse
    {
        try{
            $this->authorize('update', $patient);

            $updatedPatient = $this->service->updatePatient($patient, $request->validated());

            return new PatientResource($updatedPatient);
        } 
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized.'
            ], 403);
        }
        catch (Exception $e) {
            Log::error("Patient update failed for ID {$patient->id}: " . $e->getMessage());
            return response()->json(['error' => 'Update failed. Please check your data.'], 422);
        }
    }

    public function destroy(Patient $patient): JsonResponse
    {
        try{
            $this->authorize('delete', $patient);

            $this->service->removePatient($patient);
            return response()->json(['message' => 'Patient deleted successfully'], 200);
        }
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized.'
            ], 403);
        }
        catch (Exception $e) {
            Log::error("Deletion failed for Patient ID {$patient->id}: " . $e->getMessage());
            return response()->json(['error' => 'Record could not be deleted.'], 500);
        }
    }

    public function destroyInsuranceCard(Patient $patient): JsonResponse
    {
        try {
            $insuranceCard = $patient->insuranceCard;
            if (!$insuranceCard) {
                return response()->json(['message' => 'No insurance card found for this patient.'], 404);
            }
            $insuranceCard->delete();
            return response()->json(['message' => 'Insurance card deleted successfully'], 200);
        }
        catch (AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized.'
            ], 403);
        }
        catch (Exception $e) {
            Log::error("Failed to delete insurance card for Patient ID {$patient->id}: " . $e->getMessage());
            return response()->json(['error' => 'Insurance card could not be deleted.'], 500);
        }
    }
}
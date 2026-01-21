<?php
namespace App\Http\Controllers;

use Exception;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

use App\Models\Patient;

class InsuranceController extends Controller
{
    public function __construct() {}

    public function destroy(Patient $patient): JsonResponse
    {
        try {
            $insuranceCard = $patient->insuranceCard;
            $insuranceCardId = $insuranceCard?->id;
            if (!$insuranceCard) {
                return response()->json(['message' => 'No insurance card found for this patient.'], 404);
            }
            $insuranceCard->delete();
            return response()->json(['message' => "Insurance card deleted successfully with id {$insuranceCardId}"], 200);
        } catch (Exception $e) {
            Log::error("Failed to delete insurance card for Patient ID {$patient->id}: " . $e->getMessage());
            return response()->json(['error' => 'Insurance card could not be deleted.'], 500);
        }
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\InsuranceOcrService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function __construct(
        private InsuranceOcrService $ocrService
    ) {
        // $this->middleware('auth');
        // $this->middleware('can:create,App\Models\Patient')->only(['create', 'store']);
        // $this->middleware('can:update,patient')->only(['edit', 'update']);
        // $this->middleware('can:delete,patient')->only('destroy');
    }

    /**
     * Display patient dashboard
     */
    public function index(Request $request)
    {
        $query = Patient::with('insuranceCard');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by blood type
        if ($request->filled('blood_type')) {
            $query->where('blood_type', $request->blood_type);
        }

        $patients = $query->latest()->paginate(15);

        return view('patients.index', compact('patients'));
    }

    /**
     * Show patient creation form
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store new patient
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|array',
            'current_medications' => 'nullable|array',
            'blood_type' => 'nullable|string|max:5',
            
            // Insurance data
            'insurance_policy_number' => 'nullable|string|max:255',
            'insurance_provider' => 'nullable|string|max:255',
            'insurance_group_number' => 'nullable|string|max:255',
            'insurance_expiry_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $patient = Patient::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'date_of_birth' => $validated['date_of_birth'],
                'gender' => $validated['gender'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'],
                'emergency_contact' => $validated['emergency_contact'] ?? null,
                'emergency_phone' => $validated['emergency_phone'] ?? null,
                'address' => $validated['address'] ?? null,
                'medical_history' => $validated['medical_history'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'current_medications' => $validated['current_medications'] ?? null,
                'blood_type' => $validated['blood_type'] ?? null,
            ]);

            // Create insurance card if data provided
            if (!empty($validated['insurance_policy_number'])) {
                $patient->insuranceCard()->create([
                    'policy_number' => $validated['insurance_policy_number'],
                    'provider_name' => $validated['insurance_provider'] ?? 'Unknown',
                    'group_number' => $validated['insurance_group_number'] ?? null,
                    'expiry_date' => $validated['insurance_expiry_date'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('patients.show', $patient)
                ->with('success', 'Patient created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return back()
                ->withInput()
                ->with('error', 'Failed to create patient: ' . $e->getMessage());
        }
    }

    /**
     * Display patient details
     */
    public function show(Patient $patient)
    {
        $patient->load('insuranceCard');
        
        return view('patients.show', compact('patient'));
    }

    /**
     * Show edit form
     */
    public function edit(Patient $patient)
    {
        $patient->load('insuranceCard');
        
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update patient
     */
    public function update(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:male,female,other',
            'email' => 'nullable|email|max:255',
            'phone' => 'required|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'emergency_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'allergies' => 'nullable|array',
            'current_medications' => 'nullable|array',
            'blood_type' => 'nullable|string|max:5',
        ]);

        $patient->update($validated);

        return redirect()
            ->route('patients.show', $patient)
            ->with('success', 'Patient updated successfully!');
    }

    /**
     * Delete patient
     */
    public function destroy(Patient $patient)
    {
        $this->authorize('delete', $patient);
        
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted successfully!');
    }

    /**
     * Process insurance card OCR
     */
    public function processInsuranceCard(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
        ]);

        try {
            // Store uploaded image
            $path = $request->file('image')->store('insurance-cards', 'public');
            $fullPath = Storage::disk('public')->path($path);

            // Process with OCR
            $result = $this->ocrService->processInsuranceCard($fullPath);

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'data' => $result['data'],
                    'image_path' => $path,
                    'confidence_score' => $result['confidence_score'],
                ]);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'OCR processing failed',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
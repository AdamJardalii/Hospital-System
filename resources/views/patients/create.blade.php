<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('New Patient Registration') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form method="POST" action="{{ route('patients.store') }}" 
                          x-data="patientForm()" 
                          @submit="handleSubmit">
                        @csrf

                        {{-- Insurance Card Scanner Section --}}
                        <div class="mb-8 p-6 bg-blue-50 rounded-lg border-2 border-blue-200">
                            <h3 class="text-lg font-semibold mb-4 text-blue-900">
                                Insurance Card Scanner
                            </h3>
                            
                            <div class="flex flex-col md:flex-row gap-4">
                                {{-- Upload Method --}}
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Upload Insurance Card
                                    </label>
                                    <input type="file" 
                                           @change="handleFileUpload($event)"
                                           accept="image/*"
                                           class="block w-full text-sm text-gray-500
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-blue-50 file:text-blue-700
                                                  hover:file:bg-blue-100">
                                </div>

                                {{-- Camera Capture (Optional) --}}
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Or Capture with Camera
                                    </label>
                                    <button type="button"
                                            @click="openCamera"
                                            class="w-full px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700">
                                        📷 Open Camera
                                    </button>
                                </div>
                            </div>

                            {{-- Processing Status --}}
                            <div x-show="processing" class="mt-4">
                                <div class="flex items-center text-blue-600">
                                    <svg class="animate-spin h-5 w-5 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Processing insurance card...
                                </div>
                            </div>

                            {{-- OCR Result --}}
                            <div x-show="ocrResult" class="mt-4 p-4 bg-green-50 border border-green-200 rounded-md">
                                <p class="text-sm text-green-800">
                                    ✓ Insurance card processed successfully! 
                                    <span x-text="`Confidence: ${confidenceScore}%`" class="font-semibold"></span>
                                </p>
                            </div>
                        </div>

                        {{-- Personal Information --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Personal Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_name" class="block text-sm font-medium text-gray-700">First Name *</label>
                                    <input type="text" name="first_name" id="first_name" x-model="formData.first_name" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('first_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="last_name" class="block text-sm font-medium text-gray-700">Last Name *</label>
                                    <input type="text" name="last_name" id="last_name" x-model="formData.last_name" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('last_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="date_of_birth" class="block text-sm font-medium text-gray-700">Date of Birth *</label>
                                    <input type="date" name="date_of_birth" id="date_of_birth" x-model="formData.date_of_birth" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('date_of_birth')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="gender" class="block text-sm font-medium text-gray-700">Gender *</label>
                                    <select name="gender" id="gender" x-model="formData.gender" required
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                    @error('gender')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                    <input type="tel" name="phone" id="phone" x-model="formData.phone" required
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('phone')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" name="email" id="email" x-model="formData.email"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error('email')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Insurance Information (Auto-filled) --}}
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-4">Insurance Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="insurance_policy_number" class="block text-sm font-medium text-gray-700">Policy Number</label>
                                    <input type="text" name="insurance_policy_number" id="insurance_policy_number" 
                                           x-model="formData.insurance_policy_number"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="insurance_provider" class="block text-sm font-medium text-gray-700">Provider Name</label>
                                    <input type="text" name="insurance_provider" id="insurance_provider" 
                                           x-model="formData.insurance_provider"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="insurance_group_number" class="block text-sm font-medium text-gray-700">Group Number</label>
                                    <input type="text" name="insurance_group_number" id="insurance_group_number" 
                                           x-model="formData.insurance_group_number"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="insurance_expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date</label>
                                    <input type="date" name="insurance_expiry_date" id="insurance_expiry_date" 
                                           x-model="formData.insurance_expiry_date"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end mt-6">
                            <a href="{{ route('patients.index') }}" 
                               class="mr-4 text-sm text-gray-600 hover:text-gray-900">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                Create Patient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function patientForm() {
            return {
                processing: false,
                ocrResult: false,
                confidenceScore: 0,
                formData: {
                    first_name: '',
                    last_name: '',
                    date_of_birth: '',
                    gender: '',
                    phone: '',
                    email: '',
                    insurance_policy_number: '',
                    insurance_provider: '',
                    insurance_group_number: '',
                    insurance_expiry_date: '',
                },

                async handleFileUpload(event) {
                    const file = event.target.files[0];
                    if (!file) return;

                    this.processing = true;
                    this.ocrResult = false;

                    const formData = new FormData();
                    formData.append('image', file);

                    try {
                        const response = await fetch('{{ route("patients.ocr") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: formData
                        });

                        const result = await response.json();

                        if (result.success) {
                            this.populateInsuranceData(result.data);
                            this.confidenceScore = Math.round(result.confidence_score);
                            this.ocrResult = true;
                        } else {
                            alert('OCR processing failed: ' + result.error);
                        }
                    } catch (error) {
                        alert('Error processing image: ' + error.message);
                    } finally {
                        this.processing = false;
                    }
                },

                populateInsuranceData(data) {
                    if (data.policy_number) {
                        this.formData.insurance_policy_number = data.policy_number;
                    }
                    if (data.provider_name) {
                        this.formData.insurance_provider = data.provider_name;
                    }
                    if (data.group_number) {
                        this.formData.insurance_group_number = data.group_number;
                    }
                    if (data.expiry_date) {
                        this.formData.insurance_expiry_date = data.expiry_date;
                    }
                },

                openCamera() {
                    alert('Camera integration would require MediaDevices API implementation');
                },

                handleSubmit(event) {
                    // Additional validation can be added here
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
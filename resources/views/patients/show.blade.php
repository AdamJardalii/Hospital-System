<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Patient Details') }}
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-3xl leading-6 font-semibold text-gray-900">
                    {{ $patient->full_name }}
                </h3>

                <div class="mt-2 max-w-md mx-auto">
                    <dl class="grid grid-cols-1 gap-5 sm:gap-6">
                        <dt class="text-sm font-medium text-gray-500">
                            Date of Birth
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->date_of_birth->format('M d, Y') }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Gender
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ ucfirst($patient->gender) }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Email
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->email ?? 'No email provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Phone
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->phone }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Emergency Contact
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->emergency_contact ?? 'No emergency contact provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Emergency Phone
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->emergency_phone ?? 'No emergency phone provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Address
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->address ?? 'No address provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Medical History
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->medical_history ?? 'No medical history provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Allergies
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->allergies ? implode(', ', $patient->allergies) : 'No allergies provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Current Medications
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->current_medications ? implode(', ', $patient->current_medications) : 'No current medications provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Blood Type
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->blood_type ?? 'No blood type provided' }}
                        </dd>

                        <dt class="text-sm font-medium text-gray-500">
                            Insurance
                        </dt>
                        <dd class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">
                            {{ $patient->insuranceCard ? $patient->insuranceCard->provider_name . ' - ' . $patient->insuranceCard->policy_number : 'No insurance provided' }}
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </x-app-layout>
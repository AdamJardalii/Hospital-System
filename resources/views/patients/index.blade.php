<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Patient Management Dashboard') }}
            </h2>
            @can('create', App\Models\Patient::class)
                <a href="{{ route('patients.create') }}" 
                   class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                    + New Patient
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md">
                    {{ session('success') }}
                </div>
            @endif

            {{-- Search and Filter Section --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="GET" action="{{ route('patients.index') }}" 
                          x-data="{ showFilters: false }">
                        
                        <div class="flex flex-col md:flex-row gap-4 mb-4">
                            {{-- Search Input --}}
                            <div class="flex-1">
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Search by Name, ID, Email, or Phone..."
                                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            {{-- Search Button --}}
                            <div class="flex gap-2">
                                <button type="submit" 
                                        class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                    Search
                                </button>
                                <button type="button"
                                        @click="showFilters = !showFilters"
                                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                    Filters
                                </button>
                                @if(request()->hasAny(['search', 'gender', 'blood_type']))
                                    <a href="{{ route('patients.index') }}" 
                                       class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                        Clear
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Advanced Filters --}}
                        <div x-show="showFilters" 
                             x-transition
                             class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender</label>
                                <select name="gender" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                                <select name="blood_type" 
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">All</option>
                                    <option value="A+" {{ request('blood_type') == 'A+' ? 'selected' : '' }}>A+</option>
                                    <option value="A-" {{ request('blood_type') == 'A-' ? 'selected' : '' }}>A-</option>
                                    <option value="B+" {{ request('blood_type') == 'B+' ? 'selected' : '' }}>B+</option>
                                    <option value="B-" {{ request('blood_type') == 'B-' ? 'selected' : '' }}>B-</option>
                                    <option value="O+" {{ request('blood_type') == 'O+' ? 'selected' : '' }}>O+</option>
                                    <option value="O-" {{ request('blood_type') == 'O-' ? 'selected' : '' }}>O-</option>
                                    <option value="AB+" {{ request('blood_type') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                    <option value="AB-" {{ request('blood_type') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Patients Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Patient ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Age/Gender
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Insurance
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($patients as $patient)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $patient->patient_id }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $patient->full_name }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $patient->email }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $patient->age }} years / {{ ucfirst($patient->gender) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $patient->phone }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($patient->insuranceCard)
                                            <div class="text-sm text-gray-900">
                                                {{ $patient->insuranceCard->provider_name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{ $patient->insuranceCard->policy_number }}
                                            </div>
                                            @if($patient->insuranceCard->isExpired())
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    Expired
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-400">No insurance</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex gap-2">
                                            <a href="{{ route('patients.show', $patient) }}" 
                                               class="text-blue-600 hover:text-blue-900">View</a>
                                            
                                            @can('update', $patient)
                                                <a href="{{ route('patients.edit', $patient) }}" 
                                                   class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                            @endcan
                                            
                                            @can('delete', $patient)
                                                <form method="POST" 
                                                      action="{{ route('patients.destroy', $patient) }}"
                                                      onsubmit="return confirm('Are you sure you want to delete this patient?');"
                                                      class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="text-red-600 hover:text-red-900">
                                                        Delete
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        No patients found. 
                                        @can('create', App\Models\Patient::class)
                                            <a href="{{ route('patients.create') }}" 
                                               class="text-blue-600 hover:text-blue-900">
                                                Add your first patient
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($patients->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $patients->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
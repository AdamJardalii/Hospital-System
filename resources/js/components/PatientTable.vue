// ========================================== // FILE:
resources/js/components/PatientTable.vue // ADDED: Search, filters, sorting, and
auto-pagination // ==========================================
<template>
    <div class="space-y-4">
        <!-- ADDED: Search and Filters Section -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                <!-- Search Bar -->
                <div class="md:col-span-5">
                    <div class="relative">
                        <div
                            class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                        >
                            <svg
                                class="h-5 w-5 text-gray-400"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </div>
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Search patients by name, phone..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            @input="debouncedSearch"
                        />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600"
                        >
                            <svg
                                class="h-5 w-5"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"
                                />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Blood Type Filter -->
                <div class="md:col-span-2">
                    <select
                        v-model="filters.bloodType"
                        @change="applyFilters"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                    >
                        <option value="">All Blood Types</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                    </select>
                </div>

                <!-- Gender Filter -->
                <div class="md:col-span-2">
                    <select
                        v-model="filters.gender"
                        @change="applyFilters"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                    >
                        <option value="">All Genders</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- Insurance Filter -->
                <div class="md:col-span-2">
                    <select
                        v-model="filters.insurance"
                        @change="applyFilters"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                    >
                        <option value="">All Patients</option>
                        <option value="insured">With Insurance</option>
                        <option value="uninsured">No Insurance</option>
                    </select>
                </div>

                <!-- Clear Filters Button -->
                <div class="md:col-span-1 flex items-center">
                    <button
                        v-if="hasActiveFilters"
                        @click="clearFilters"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition flex items-center justify-center"
                        title="Clear all filters"
                    >
                        <svg
                            class="h-5 w-5"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"
                            />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Active Filters Display -->
            <div
                v-if="hasActiveFilters || searchQuery"
                class="mt-4 flex flex-wrap gap-2"
            >
                <span class="text-sm text-gray-600 font-medium"
                    >Active filters:</span
                >
                <span
                    v-if="searchQuery"
                    class="inline-flex items-center space-x-1 bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm"
                >
                    <span>Search: "{{ searchQuery }}"</span>
                    <button @click="clearSearch" class="hover:text-blue-900">
                        <svg
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </span>
                <span
                    v-if="filters.bloodType"
                    class="inline-flex items-center space-x-1 bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm"
                >
                    <span>Blood: {{ filters.bloodType }}</span>
                    <button
                        @click="
                            filters.bloodType = '';
                            applyFilters();
                        "
                        class="hover:text-red-900"
                    >
                        <svg
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </span>
                <span
                    v-if="filters.gender"
                    class="inline-flex items-center space-x-1 bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm"
                >
                    <span>Gender: {{ filters.gender }}</span>
                    <button
                        @click="
                            filters.gender = '';
                            applyFilters();
                        "
                        class="hover:text-purple-900"
                    >
                        <svg
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </span>
                <span
                    v-if="filters.insurance"
                    class="inline-flex items-center space-x-1 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm"
                >
                    <span>{{
                        filters.insurance === "insured"
                            ? "With Insurance"
                            : "No Insurance"
                    }}</span>
                    <button
                        @click="
                            filters.insurance = '';
                            applyFilters();
                        "
                        class="hover:text-green-900"
                    >
                        <svg
                            class="h-3 w-3"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <!-- Loading State -->
            <div v-if="patientStore.loading" class="p-12 text-center">
                <div
                    class="inline-block animate-spin rounded-full h-12 w-12 border-4 border-blue-600 border-t-transparent"
                ></div>
                <p class="mt-4 text-gray-600">Loading patients...</p>
            </div>

            <!-- Error State -->
            <div v-else-if="patientStore.error" class="p-8">
                <div
                    class="bg-red-50 border border-red-200 rounded-lg p-4 text-red-700"
                >
                    {{ patientStore.error }}
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else-if="filteredPatients.length === 0"
                class="p-12 text-center"
            >
                <svg
                    class="mx-auto h-16 w-16 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                    />
                </svg>
                <h3 class="mt-4 text-lg font-semibold text-gray-900">
                    {{
                        hasActiveFilters || searchQuery
                            ? "No patients found"
                            : "No patients yet"
                    }}
                </h3>
                <p class="mt-2 text-gray-600">
                    {{
                        hasActiveFilters || searchQuery
                            ? "Try adjusting your filters or search query"
                            : "Get started by adding your first patient"
                    }}
                </p>
                <button
                    v-if="hasActiveFilters || searchQuery"
                    @click="clearAll"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                >
                    Clear All Filters
                </button>
            </div>

            <!-- Table -->
            <div v-else class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <!-- ADDED: Sortable column headers -->
                            <th class="px-6 py-4 text-left">
                                <button
                                    @click="toggleSort('name')"
                                    class="flex items-center space-x-1 text-xs font-semibold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition"
                                >
                                    <span>Patient</span>
                                    <svg
                                        v-if="sortBy === 'name'"
                                        class="h-4 w-4"
                                        :class="
                                            sortOrder === 'asc'
                                                ? 'rotate-180'
                                                : ''
                                        "
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                        />
                                    </svg>
                                </button>
                            </th>
                            <th class="px-6 py-4 text-left">
                                <button
                                    @click="toggleSort('phone')"
                                    class="flex items-center space-x-1 text-xs font-semibold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition"
                                >
                                    <span>Contact</span>
                                    <svg
                                        v-if="sortBy === 'phone'"
                                        class="h-4 w-4"
                                        :class="
                                            sortOrder === 'asc'
                                                ? 'rotate-180'
                                                : ''
                                        "
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                        />
                                    </svg>
                                </button>
                            </th>
                            <th class="px-6 py-4 text-left">
                                <button
                                    @click="toggleSort('blood_type')"
                                    class="flex items-center space-x-1 text-xs font-semibold text-gray-600 uppercase tracking-wider hover:text-blue-600 transition"
                                >
                                    <span>Blood Type</span>
                                    <svg
                                        v-if="sortBy === 'blood_type'"
                                        class="h-4 w-4"
                                        :class="
                                            sortOrder === 'asc'
                                                ? 'rotate-180'
                                                : ''
                                        "
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M19 9l-7 7-7-7"
                                        />
                                    </svg>
                                    <svg
                                        v-else
                                        class="h-4 w-4 text-gray-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                                        />
                                    </svg>
                                </button>
                            </th>
                            <th
                                class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"
                            >
                                Insurance
                            </th>
                            <th
                                class="px-6 py-4 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider"
                            >
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr
                            v-for="patient in paginatedPatients"
                            :key="patient.id"
                            class="hover:bg-gray-50 transition"
                        >
                            <td class="px-6 py-4">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center"
                                    >
                                        <span
                                            class="text-blue-600 font-semibold text-sm"
                                        >
                                            {{
                                                getInitials(
                                                    patient.full_name ||
                                                        `${patient.first_name} ${patient.last_name}`,
                                                )
                                            }}
                                        </span>
                                    </div>
                                    <div>
                                        <div
                                            class="font-semibold text-slate-900"
                                        >
                                            {{
                                                patient.full_name ||
                                                `${patient.first_name} ${patient.last_name}`
                                            }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ patient.gender }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-slate-900">
                                    {{ patient.phone }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ formatDate(patient.date_of_birth) }}
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <BadgeComponent
                                    :text="patient.blood_type || 'N/A'"
                                    variant="secondary"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <BadgeComponent
                                    v-if="patientStore.hasInsurance(patient)"
                                    text="Covered"
                                    variant="success"
                                />
                                <BadgeComponent
                                    v-else
                                    text="No Insurance"
                                    variant="warning"
                                />
                            </td>
                            <td class="px-6 py-4">
                                <div
                                    class="flex items-center justify-end space-x-2"
                                >
                                    <button
                                        @click="$emit('view', patient)"
                                        class="p-2 text-slate-600 hover:bg-slate-50 rounded-lg transition"
                                        title="View"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="$emit('edit', patient)"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                        title="Edit"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                            />
                                        </svg>
                                    </button>
                                    <button
                                        @click="$emit('delete', patient)"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition"
                                        title="Delete"
                                    >
                                        <svg
                                            class="h-5 w-5"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                            />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Pagination -->
                <div
                    class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50"
                >
                    <div class="flex items-center space-x-4">
                        <div class="text-sm text-gray-600">
                            Showing {{ startIndex + 1 }} to {{ endIndex }} of
                            {{ filteredPatients.length }} patients
                            <span
                                v-if="hasActiveFilters || searchQuery"
                                class="text-blue-600 font-medium"
                            >
                                (filtered from
                                {{ patientStore.patients.length }} total)
                            </span>
                        </div>
                        <!-- ADDED: Per page selector -->
                        <select
                            v-model.number="perPage"
                            @change="changePerPage"
                            class="px-3 py-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600 focus:border-transparent"
                        >
                            <option :value="5">5 per page</option>
                            <option :value="10">10 per page</option>
                            <option :value="20">20 per page</option>
                            <option :value="50">50 per page</option>
                        </select>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button
                            @click="changePage(currentPage - 1)"
                            :disabled="currentPage === 1"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            Previous
                        </button>

                        <div class="flex items-center space-x-1">
                            <button
                                v-for="page in displayedPages"
                                :key="page"
                                @click="changePage(page)"
                                :class="[
                                    'px-3 py-2 rounded-lg text-sm font-medium transition',
                                    page === currentPage
                                        ? 'bg-blue-600 text-white'
                                        : 'text-gray-700 hover:bg-gray-100',
                                ]"
                            >
                                {{ page }}
                            </button>
                        </div>

                        <button
                            @click="changePage(currentPage + 1)"
                            :disabled="currentPage === totalPages"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed transition"
                        >
                            Next
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from "vue";
import { usePatientStore } from "../stores/patientStore";
import { formatDateForDisplay } from "../utils/dateFormatter";
import BadgeComponent from "./BadgeComponent.vue";

const patientStore = usePatientStore();

defineEmits(["edit", "delete", "view"]);

// ADDED: Search and filter state
const searchQuery = ref("");
const filters = ref({
    bloodType: "",
    gender: "",
    insurance: "",
});

// ADDED: Sorting state
const sortBy = ref("name");
const sortOrder = ref("asc"); // 'asc' or 'desc'

// ADDED: Pagination state (client-side)
const currentPage = ref(1);
const perPage = ref(10);

// ADDED: Debounce timer for search
let searchTimeout = null;

onMounted(() => {
    patientStore.fetchPatients(1);
});

// ADDED: Watch for patient store updates to maintain pagination
watch(
    () => patientStore.patients,
    () => {
        // Auto-adjust page if current page is out of bounds
        if (currentPage.value > totalPages.value && totalPages.value > 0) {
            currentPage.value = totalPages.value;
        }
    },
    { deep: true },
);

const getInitials = (name) => {
    if (!name) return "??";
    return name
        .split(" ")
        .map((n) => n[0])
        .join("")
        .toUpperCase()
        .slice(0, 2);
};

const formatDate = (date) => {
    return formatDateForDisplay(date);
};

// ADDED: Computed property for filtered patients
const filteredPatients = computed(() => {
    let patients = [...patientStore.patients];

    // Apply search filter
    if (searchQuery.value) {
        const query = searchQuery.value.toLowerCase();
        patients = patients.filter((patient) => {
            const fullName = (
                patient.full_name ||
                `${patient.first_name} ${patient.last_name}`
            ).toLowerCase();
            const phone = (patient.phone || "").toLowerCase();
            return fullName.includes(query) || phone.includes(query);
        });
    }

    // Apply blood type filter
    if (filters.value.bloodType) {
        patients = patients.filter(
            (patient) => patient.blood_type === filters.value.bloodType,
        );
    }

    // Apply gender filter
    if (filters.value.gender) {
        patients = patients.filter(
            (patient) => patient.gender === filters.value.gender,
        );
    }

    // Apply insurance filter
    if (filters.value.insurance) {
        if (filters.value.insurance === "insured") {
            patients = patients.filter((patient) =>
                patientStore.hasInsurance(patient),
            );
        } else if (filters.value.insurance === "uninsured") {
            patients = patients.filter(
                (patient) => !patientStore.hasInsurance(patient),
            );
        }
    }

    // Apply sorting
    patients.sort((a, b) => {
        let aValue, bValue;

        switch (sortBy.value) {
            case "name":
                aValue = (
                    a.full_name || `${a.first_name} ${a.last_name}`
                ).toLowerCase();
                bValue = (
                    b.full_name || `${b.first_name} ${b.last_name}`
                ).toLowerCase();
                break;
            case "phone":
                aValue = a.phone || "";
                bValue = b.phone || "";
                break;
            case "blood_type":
                aValue = a.blood_type || "";
                bValue = b.blood_type || "";
                break;
            default:
                return 0;
        }

        if (aValue < bValue) return sortOrder.value === "asc" ? -1 : 1;
        if (aValue > bValue) return sortOrder.value === "asc" ? 1 : -1;
        return 0;
    });

    return patients;
});

// ADDED: Computed property for paginated patients
const paginatedPatients = computed(() => {
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return filteredPatients.value.slice(start, end);
});

// ADDED: Computed property for total pages
const totalPages = computed(() => {
    return Math.ceil(filteredPatients.value.length / perPage.value) || 1;
});

// ADDED: Computed property for pagination info
const startIndex = computed(() => {
    return (currentPage.value - 1) * perPage.value;
});

const endIndex = computed(() => {
    return Math.min(
        startIndex.value + perPage.value,
        filteredPatients.value.length,
    );
});

// ADDED: Computed property for displayed page numbers
const displayedPages = computed(() => {
    const current = currentPage.value;
    const last = totalPages.value;
    const pages = [];

    if (last <= 7) {
        for (let i = 1; i <= last; i++) {
            pages.push(i);
        }
    } else {
        if (current <= 3) {
            for (let i = 1; i <= 5; i++) pages.push(i);
        } else if (current >= last - 2) {
            for (let i = last - 4; i <= last; i++) pages.push(i);
        } else {
            for (let i = current - 2; i <= current + 2; i++) pages.push(i);
        }
    }

    return pages;
});

// ADDED: Check if any filters are active
const hasActiveFilters = computed(() => {
    return (
        filters.value.bloodType ||
        filters.value.gender ||
        filters.value.insurance
    );
});

// ADDED: Debounced search function
const debouncedSearch = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        currentPage.value = 1; // Reset to first page on search
    }, 300);
};

// ADDED: Toggle sort function
const toggleSort = (column) => {
    if (sortBy.value === column) {
        sortOrder.value = sortOrder.value === "asc" ? "desc" : "asc";
    } else {
        sortBy.value = column;
        sortOrder.value = "asc";
    }
};

// ADDED: Apply filters function
const applyFilters = () => {
    currentPage.value = 1; // Reset to first page when filters change
};

// ADDED: Clear search function
const clearSearch = () => {
    searchQuery.value = "";
    currentPage.value = 1;
};

// ADDED: Clear filters function
const clearFilters = () => {
    filters.value = {
        bloodType: "",
        gender: "",
        insurance: "",
    };
    currentPage.value = 1;
};

// ADDED: Clear all function
const clearAll = () => {
    searchQuery.value = "";
    clearFilters();
};

// ADDED: Change page function
const changePage = (page) => {
    if (page >= 1 && page <= totalPages.value) {
        currentPage.value = page;
    }
};

// ADDED: Change per page function
const changePerPage = () => {
    currentPage.value = 1; // Reset to first page when changing items per page
};

// ADDED: Expose refresh function for parent component
defineExpose({
    refresh: () => {
        patientStore.fetchPatients(1);
    },
});
</script>

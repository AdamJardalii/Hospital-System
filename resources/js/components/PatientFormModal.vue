<template>
    <div class="fixed inset-0 z-50 overflow-y-auto">
        <div
            class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0"
        >
            <!-- Backdrop -->
            <div
                class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"
                @click="$emit('close')"
            ></div>

            <!-- Modal -->
            <div
                class="relative bg-white rounded-2xl shadow-2xl transform transition-all sm:max-w-4xl sm:w-full"
            >
                <!-- Header -->
                <div
                    class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4 rounded-t-2xl"
                >
                    <div class="flex items-center justify-between">
                        <h3 class="text-xl font-bold text-white">
                            {{
                                mode === "create"
                                    ? "Add New Patient"
                                    : mode === "edit"
                                      ? "Edit Patient"
                                      : "View Patient Details"
                            }}
                        </h3>
                        <button
                            @click="$emit('close')"
                            class="text-white hover:bg-white hover:bg-opacity-20 rounded-lg p-2 transition"
                        >
                            <svg
                                class="h-6 w-6"
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

                <!-- AI Scanner -->
                <div
                    v-if="mode === 'create' || mode === 'edit'"
                    class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-100"
                >
                    <!-- Header Row -->
                    <div
                        class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4"
                    >
                        <!-- Left: Icon & Title -->
                        <div class="flex items-center space-x-3">
                            <div
                                class="h-12 w-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg"
                            >
                                <svg
                                    class="h-7 w-7 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"
                                    />
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"
                                    />
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-slate-900 text-lg">
                                    AI Magic Scan
                                </h4>
                                <p class="text-sm text-gray-600">
                                    Auto-fill patient data from insurance card
                                </p>
                            </div>
                        </div>

                        <!-- Right: AI Agent Selector & Scan Button -->
                        <div
                            class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3"
                        >
                            <!-- AI Agent Selector -->
                            <div class="relative">
                                <label
                                    class="block text-xs font-medium text-gray-700 mb-1"
                                >
                                    AI Engine
                                </label>
                                <select
                                    v-model="selectedAgent"
                                    class="w-full sm:w-48 pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition bg-white text-sm font-medium"
                                >
                                    <option
                                        v-for="agent in aiAgents"
                                        :key="agent.value"
                                        :value="agent.value"
                                        :disabled="
                                            agent.status === 'development'
                                        "
                                    >
                                        {{ agent.label }}
                                        {{
                                            agent.status === "development"
                                                ? "(Coming Soon)"
                                                : ""
                                        }}
                                    </option>
                                </select>
                                <!-- Icon in dropdown -->
                                <div
                                    class="absolute left-3 top-[26px] pointer-events-none"
                                >
                                    <svg
                                        class="h-4 w-4 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"
                                        />
                                    </svg>
                                </div>
                            </div>

                            <!-- Scan Button -->
                            <div class="flex flex-col">
                                <label
                                    class="block text-xs font-medium text-transparent mb-1"
                                >
                                    Action
                                </label>
                                <input
                                    ref="fileInput"
                                    type="file"
                                    accept="image/*"
                                    class="hidden"
                                    @change="handleScan"
                                />
                                <button
                                    type="button"
                                    @click="$refs.fileInput.click()"
                                    :disabled="
                                        scanning ||
                                        aiAgents.find(
                                            (a) => a.value === selectedAgent,
                                        )?.status === 'development'
                                    "
                                    class="flex items-center justify-center space-x-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold hover:from-blue-700 hover:to-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg hover:shadow-xl"
                                >
                                    <svg
                                        v-if="!scanning"
                                        class="h-5 w-5"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                        />
                                    </svg>
                                    <div
                                        v-else
                                        class="h-5 w-5 border-2 border-white border-t-transparent rounded-full animate-spin"
                                    ></div>
                                    <span>{{
                                        scanning
                                            ? "Scanning..."
                                            : "Scan & Auto-Fill"
                                    }}</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Selected Agent Info -->
                    <div
                        class="mt-4 flex items-start space-x-2 bg-white bg-opacity-60 rounded-lg p-3 border border-blue-200"
                    >
                        <svg
                            class="h-5 w-5 text-blue-600 mt-0.5 flex-shrink-0"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-slate-900">
                                {{
                                    aiAgents.find(
                                        (a) => a.value === selectedAgent,
                                    )?.label
                                }}
                            </p>
                            <p class="text-xs text-gray-600 mt-0.5">
                                {{
                                    aiAgents.find(
                                        (a) => a.value === selectedAgent,
                                    )?.description
                                }}
                            </p>
                            <div
                                v-if="
                                    aiAgents.find(
                                        (a) => a.value === selectedAgent,
                                    )?.status === 'development'
                                "
                                class="mt-2"
                            >
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                >
                                    <svg
                                        class="h-3 w-3 mr-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    Under Development
                                </span>
                            </div>
                            <div v-else class="mt-2">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                >
                                    <svg
                                        class="h-3 w-3 mr-1"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                    Ready to Use
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Scanning Progress -->
                    <div
                        v-if="scanning"
                        class="mt-4 bg-white rounded-lg p-4 border border-blue-200 shadow-sm"
                    >
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div
                                    class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center scanning-animation"
                                >
                                    <svg
                                        class="h-6 w-6 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                        />
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-900">
                                    Reading Insurance Card...
                                </p>
                                <p class="text-sm text-gray-600">
                                    Using
                                    {{
                                        aiAgents.find(
                                            (a) => a.value === selectedAgent,
                                        )?.label
                                    }}
                                    to extract patient information
                                </p>
                            </div>
                        </div>
                        <div
                            class="mt-3 bg-gray-200 rounded-full h-2 overflow-hidden"
                        >
                            <div
                                class="bg-gradient-to-r from-blue-600 to-indigo-600 h-full rounded-full skeleton"
                            ></div>
                        </div>
                    </div>
                </div>

                <!-- Form -->
                <component
                    @submit.prevent="handleSubmit"
                    :is="mode === 'view' ? 'div' : 'form'"
                    class="px-6 py-6 max-h-[70vh] overflow-y-auto"
                >
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="col-span-2">
                            <h4
                                class="font-semibold text-slate-900 mb-4 flex items-center space-x-2"
                            >
                                <svg
                                    class="h-5 w-5 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                                    />
                                </svg>
                                <span>Personal Information</span>
                            </h4>
                        </div>

                        <InputField
                            v-model="form.first_name"
                            label="First Name"
                            :required="mode !== 'view'"
                            :disabled="mode === 'view'"
                            placeholder="John"
                        />

                        <InputField
                            v-model="form.last_name"
                            label="Last Name"
                            :required="mode !== 'view'"
                            :disabled="mode === 'view'"
                            placeholder="Doe"
                        />

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 mb-2"
                            >
                                Gender
                                <span
                                    v-if="mode !== 'view'"
                                    class="text-red-500"
                                    >*</span
                                >
                            </label>
                            <select
                                v-model="form.gender"
                                :required="mode !== 'view'"
                                :disabled="mode === 'view'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            >
                                <option value="">Select gender</option>
                                <option value="male">male</option>
                                <option value="female">female</option>
                                <option value="other">other</option>
                            </select>
                        </div>

                        <InputField
                            v-model="form.phone"
                            label="Phone"
                            type="tel"
                            placeholder="+1 (555) 123-4567"
                            :disabled="mode === 'view'"
                            :required="mode !== 'view'"
                        />

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 mb-2"
                                >Blood Type</label
                            >
                            <select
                                v-model="form.blood_type"
                                :disabled="mode == 'view'"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            >
                                <option value="">Select blood type</option>
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

                        <div>
                            <label
                                class="block text-sm font-medium text-slate-700 mb-2"
                                >Date of Birth
                                <span
                                    v-if="mode !== 'view'"
                                    class="text-red-500"
                                    >*</span
                                ></label
                            >
                            <input
                                v-model="form.date_of_birth"
                                :disabled="mode === 'view'"
                                :required="mode !== 'view'"
                                :max="maxDate"
                                type="date"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            />
                        </div>

                        <!-- Medical Information -->
                        <div class="col-span-2 mt-4">
                            <h4
                                class="font-semibold text-slate-900 mb-4 flex items-center space-x-2"
                            >
                                <svg
                                    class="h-5 w-5 text-blue-600"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                    />
                                </svg>
                                <span>Medical Information</span>
                            </h4>
                        </div>

                        <div class="col-span-2">
                            <TagInput
                                v-model="form.allergies"
                                label="Allergies"
                                placeholder="Add allergy (press Enter)"
                                :disabled="mode === 'view'"
                            />
                        </div>

                        <div class="col-span-2">
                            <TagInput
                                v-model="form.current_medications"
                                label="Current Medications"
                                placeholder="Add medication (press Enter)"
                                :disabled="mode === 'view'"
                            />
                        </div>

                        <div class="col-span-2">
                            <TagInput
                                v-model="form.medical_history"
                                label="Medical History"
                                placeholder="Add medical history (press Enter)"
                                :disabled="mode === 'view'"
                            />
                        </div>

                        <!-- Insurance Information -->
                        <div class="col-span-2 mt-4">
                            <div class="flex items-center justify-between mb-4">
                                <h4
                                    class="font-semibold text-slate-900 flex items-center space-x-2"
                                >
                                    <svg
                                        class="h-5 w-5 text-blue-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"
                                        />
                                    </svg>
                                    <span>Insurance Information</span>
                                </h4>
                                <div v-if="mode === 'view'">
                                    <span
                                        v-if="hasInsurance"
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700"
                                    >
                                        Has Insurance
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"
                                    >
                                        No Insurance
                                    </span>
                                </div>
                                <label
                                    v-else
                                    class="flex items-center space-x-2 cursor-pointer"
                                >
                                    <input
                                        v-model="hasInsurance"
                                        type="checkbox"
                                        class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    />
                                    <span class="text-sm text-gray-700"
                                        >Has Insurance</span
                                    >
                                </label>
                            </div>
                        </div>

                        <template v-if="hasInsurance">
                            <InputField
                                v-model="form.insurance.provider_name"
                                label="Provider Name"
                                placeholder="Blue Cross"
                                :disabled="mode === 'view'"
                            />

                            <InputField
                                v-model="form.insurance.policy_number"
                                label="Policy Number"
                                placeholder="ABC123456789"
                                :disabled="mode === 'view'"
                            />

                            <div class="col-span-2">
                                <label
                                    class="block text-sm font-medium text-slate-700 mb-2"
                                    >Expiry Date</label
                                >
                                <input
                                    v-model="form.insurance.expiry_date"
                                    type="date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                                    :disabled="mode === 'view'"
                                />
                            </div>
                        </template>
                    </div>

                    <!-- Error Message -->
                    <div
                        v-if="error && mode !== 'view'"
                        class="mt-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm"
                    >
                        {{ error }}
                    </div>

                    <!-- Actions -->
                    <div class="mt-8 flex items-center justify-end space-x-4">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-6 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition"
                        >
                            {{ mode === "view" ? "Close" : "Cancel" }}
                        </button>
                        <button
                            v-if="mode !== 'view'"
                            type="submit"
                            :disabled="loading"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50 shadow-lg hover:shadow-xl"
                        >
                            {{
                                loading
                                    ? "Saving..."
                                    : mode === "create"
                                      ? "Create Patient"
                                      : "Update Patient"
                            }}
                        </button>
                        <button
                            v-else
                            type="button"
                            @click="$emit('switch-to-edit')"
                            class="px-6 py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg hover:shadow-xl flex items-center space-x-2"
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
                            <span>Edit Patient</span>
                        </button>
                    </div>
                </component>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, watch, onMounted, computed } from "vue";
import { usePatientStore } from "../stores/patientStore";
import { ScannerService } from "../services/ScannerService";
import { parseISOToInput, formatDateForAPI } from "../utils/dateFormatter";
import InputField from "./InputField.vue";
import TagInput from "./TagInput.vue";

const props = defineProps({
    patient: Object,
    mode: {
        type: String,
        default: "create",
        validator: (value) => ["create", "edit", "view"].includes(value),
    },
});

const emit = defineEmits(["close", "saved", "switch-to-edit"]);

const patientStore = usePatientStore();
const fileInput = ref(null);
const scanning = ref(false);
const loading = ref(false);
const error = ref("");
const hasInsurance = ref(false);
const selectedAgent = ref("ocr_space");

const aiAgents = [
    {
        value: "ocr_space",
        label: "OCR Space",
        status: "active",
        description: "Fast and reliable OCR",
    },
    {
        value: "tesseract",
        label: "Tesseract",
        status: "development",
        description: "Under development",
    },
    {
        value: "google",
        label: "Google Vision",
        status: "development",
        description: "Under development",
    },
    {
        value: "paddle",
        label: "Paddle OCR",
        status: "development",
        description: "Under development",
    },
];

const form = reactive({
    first_name: "",
    last_name: "",
    gender: "",
    phone: "",
    blood_type: "",
    date_of_birth: "",
    allergies: [],
    current_medications: [],
    medical_history: [],
    insurance: {
        provider_name: "",
        policy_number: "",
        expiry_date: "",
    },
});

const maxDate = computed(() => {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, "0");
    const day = String(today.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
});
onMounted(() => {
    if (props.patient) {
        Object.assign(form, {
            first_name: props.patient.first_name || "",
            last_name: props.patient.last_name || "",
            gender: props.patient.gender || "",
            phone: props.patient.phone || "",
            blood_type: props.patient.blood_type || "",
            date_of_birth: parseISOToInput(props.patient.date_of_birth) || "",
            allergies: props.patient.allergies || [],
            current_medications: props.patient.current_medications || [],
            medical_history: props.patient.medical_history || [],
            insurance: props.patient.insurance
                ? {
                      provider_name:
                          props.patient.insurance.provider_name || "",
                      policy_number:
                          props.patient.insurance.policy_number || "",
                      expiry_date:
                          parseISOToInput(
                              props.patient.insurance.expiry_date,
                          ) || "",
                  }
                : {
                      provider_name: "",
                      policy_number: "",
                      expiry_date: "",
                  },
        });

        hasInsurance.value = !!(
            props.patient.insurance &&
            Object.keys(props.patient.insurance).length > 0
        );
    }
});

const handleScan = async (event) => {
    const file = event.target.files[0];
    if (!file) return;

    scanning.value = true;
    error.value = "";

    try {
        const scannedData = await ScannerService.scanCard(
            file,
            selectedAgent.value,
        );

        if (scannedData.first_name) form.first_name = scannedData.first_name;
        if (scannedData.last_name) form.last_name = scannedData.last_name;
        if (scannedData.gender) form.gender = scannedData.gender.toLowerCase();
        if (scannedData.phone_number) form.phone = scannedData.phone_number;
        if (scannedData.date_of_birth)
            form.date_of_birth = scannedData.date_of_birth;

        if (
            scannedData.Company ||
            scannedData.policy_number ||
            scannedData.expiry_date
        ) {
            hasInsurance.value = true;
            form.insurance.provider_name = scannedData.Company || "";
            form.insurance.policy_number = scannedData.policy_number || "";

            if (scannedData.expiry_date) {
                form.insurance.expiry_date = parseISOToInput(
                    scannedData.expiry_date,
                );
            }
        }
        if (scannedData.other && Array.isArray(scannedData.other)) {
            const bloodType = ScannerService.extractBloodType(
                scannedData.other,
            );
            if (bloodType) {
                form.blood_type = bloodType;
            }
        }
    } catch (err) {
        error.value = "Failed to scan card. Please enter details manually.";
    } finally {
        scanning.value = false;
        fileInput.value.value = "";
    }
};

const handleSubmit = async () => {
    if (props.mode === "view") return;
    loading.value = true;
    error.value = "";

    try {
        const patientData = {
            ...form,
            insurance: hasInsurance.value ? form.insurance : null,
        };

        if (props.mode === "create") {
            await patientStore.createPatient(patientData);
        } else {
            await patientStore.updatePatient(props.patient.id, patientData);
        }

        emit("saved");
    } catch (err) {
        console.log("value of err", err.response);
        error.value =
            err?.data?.message ||
            err?.response?.data?.message ||
            "Failed to save patient";
    } finally {
        loading.value = false;
    }
};
</script>

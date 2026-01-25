<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Patients</h1>
                <p class="text-gray-600 mt-1">Manage your patient records</p>
            </div>
            <button
                @click="openCreateModal"
                class="flex items-center space-x-2 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition shadow-lg hover:shadow-xl"
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
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"
                    />
                </svg>
                <span>Add Patient</span>
            </button>
        </div>

        <!-- Patient Table -->
        <PatientTable
            ref="patientTableRef"
            @edit="openEditModal"
            @delete="handleDelete"
            @view="openViewModal"
        />

        <!-- Patient Form Modal -->
        <PatientFormModal
            v-if="showModal"
            :patient="selectedPatient"
            :mode="modalMode"
            @close="closeModal"
            @saved="handleSaved"
            @switch-to-edit="switchToEditMode"
        />

        <Transition name="fade">
            <div
                v-if="showNotification"
                class="fixed bottom-6 right-6 bg-green-600 text-white px-6 py-4 rounded-lg shadow-2xl flex items-center space-x-3 z-50"
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
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                    />
                </svg>
                <span class="font-medium">{{ notificationMessage }}</span>
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref, nextTick } from "vue";
import PatientTable from "../components/PatientTable.vue";
import PatientFormModal from "../components/PatientFormModal.vue";
import { usePatientStore } from "../stores/patientStore";

const patientStore = usePatientStore();
const showModal = ref(false);
const selectedPatient = ref(null);
const modalMode = ref("create");

const patientTableRef = ref(null);

const showNotification = ref(false);
const notificationMessage = ref("");

const displayNotification = (message) => {
    notificationMessage.value = message;
    showNotification.value = true;
    setTimeout(() => {
        showNotification.value = false;
    }, 3000);
};

const refreshTable = async () => {
    await nextTick();
    if (patientTableRef.value) {
        patientTableRef.value.refresh();
    }
};

const openCreateModal = () => {
    selectedPatient.value = null;
    modalMode.value = "create";
    showModal.value = true;
};

const openEditModal = (patient) => {
    selectedPatient.value = patient;
    modalMode.value = "edit";
    showModal.value = true;
};

const openViewModal = (patient) => {
    selectedPatient.value = patient;
    modalMode.value = "view";
    showModal.value = true;
};

const switchToEditMode = () => {
    modalMode.value = "edit";
};

const closeModal = () => {
    showModal.value = false;
    selectedPatient.value = null;
};

const handleSaved = async () => {
    closeModal();
    await refreshTable();
    if (modalMode.value === "create") {
        displayNotification("Patient created successfully!");
    } else {
        displayNotification("Patient updated successfully!");
    }
};

const handleDelete = async (patient) => {
    if (confirm(`Are you sure you want to delete ${patient.full_name}?`)) {
        try {
            await patientStore.deletePatient(patient.id);
            await refreshTable();
            displayNotification("Patient deleted successfully!");
        } catch (error) {
            alert("Failed to delete patient");
            displayNotification("Failed to delete patient");
        }
    }
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: all 0.3s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(20px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(-20px);
}
</style>

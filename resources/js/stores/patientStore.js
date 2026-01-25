import { defineStore } from "pinia";
import apiClient from "../services/api";
import { formatDateForAPI } from "../utils/dateFormatter";

export const usePatientStore = defineStore("patient", {
    state: () => ({
        patients: [],
        currentPatient: null,
        pagination: {
            currentPage: 1,
            lastPage: 1,
            perPage: 10,
            total: 0,
        },
        loading: false,
        error: null,
        patientCache: {},
    }),

    getters: {
        getPatientById: (state) => (id) => {
            return (
                state.patientCache[id] ||
                state.patients.find((p) => p.id === id)
            );
        },

        hasInsurance: (state) => (patient) => {
            return (
                patient &&
                patient.insurance &&
                Object.keys(patient.insurance).length > 0
            );
        },
    },

    actions: {
        async fetchPatients(page = 1) {
            this.loading = true;
            this.error = null;

            try {
                const response = await apiClient.get("/patients", {
                    params: { page },
                });

                this.patients = response.data.data;

                if (response.data.meta) {
                    this.pagination = {
                        currentPage: response.data.meta.current_page,
                        lastPage: response.data.meta.last_page,
                        perPage: response.data.meta.per_page,
                        total: response.data.meta.total,
                    };
                }

                return response.data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to fetch patients";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async fetchPatientById(id) {
            if (this.patientCache[id]) {
                this.currentPatient = this.patientCache[id];
                return this.currentPatient;
            }

            this.loading = true;
            this.error = null;

            try {
                const response = await apiClient.get(`/patients/${id}`);
                this.currentPatient = response.data.data;
                this.patientCache[id] = this.currentPatient;
                return this.currentPatient;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to fetch patient";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async createPatient(patientData) {
            this.loading = true;
            this.error = null;

            try {
                const payload = this.formatPatientForAPI(patientData);
                const response = await apiClient.post("/patients", payload);

                if (response.data.success) {
                    await this.fetchPatients(this.pagination.currentPage);
                    return response.data.data;
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to create patient";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async updatePatient(id, patientData) {
            this.loading = true;
            this.error = null;

            try {
                const payload = this.formatPatientForAPI(patientData);
                const response = await apiClient.put(
                    `/patients/${id}`,
                    payload,
                );

                if (response.data.success) {
                    delete this.patientCache[id];
                    await this.fetchPatients(this.pagination.currentPage);
                    return response.data.data;
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to update patient";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async deletePatient(id) {
            this.loading = true;
            this.error = null;

            try {
                const response = await apiClient.delete(`/patients/${id}`);

                if (response.data.success) {
                    delete this.patientCache[id];
                    if (
                        this.patients.length === 0 &&
                        this.pagination.currentPage > 1
                    ) {
                        await this.fetchPatients(
                            this.pagination.currentPage - 1,
                        );
                    } else {
                        await this.fetchPatients(this.pagination.currentPage);
                    }
                    this.patients = this.patients.filter((p) => p.id !== id);
                }
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Failed to delete patient";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        formatPatientForAPI(patient) {
            return {
                first_name: patient.first_name,
                last_name: patient.last_name,
                gender: patient.gender,
                phone: patient.phone,
                blood_type: patient.blood_type,
                date_of_birth: patient.date_of_birth
                    ? formatDateForAPI(patient.date_of_birth)
                    : null,
                allergies: patient.allergies || [],
                current_medications: patient.current_medications || [],
                medical_history: patient.medical_history || [],
                insurance:
                    patient.insurance &&
                    Object.keys(patient.insurance).length > 0
                        ? {
                              provider_name: patient.insurance.provider_name,
                              policy_number: patient.insurance.policy_number,
                              expiry_date: patient.insurance.expiry_date
                                  ? formatDateForAPI(
                                        patient.insurance.expiry_date,
                                    )
                                  : null,
                          }
                        : null,
            };
        },

        clearError() {
            this.error = null;
        },
    },
});

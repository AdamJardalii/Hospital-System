<template>
    <div
        class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-slate-100 px-4"
    >
        <div
            class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl"
        >
            <div class="text-center">
                <div
                    class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center"
                >
                    <svg
                        class="h-10 w-10 text-white"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
                        />
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-slate-900">
                    Create Account
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Join the patient management system
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleSignup">
                <div
                    v-if="error"
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm whitespace-pre-line"
                >
                    {{ error }}
                </div>

                <div class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 mb-2"
                            >Full Name</label
                        >
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            placeholder="Dr. John Smith"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 mb-2"
                            >Email Address</label
                        >
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            placeholder="doctor@hospital.com"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 mb-2"
                            >Password</label
                        >
                        <input
                            v-model="form.password"
                            type="password"
                            required
                            minlength="8"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                        <div
                            class="mt-2 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden"
                        >
                            <div
                                :class="strengthClass"
                                :style="{ width: strengthWidth }"
                                class="h-full transition-all duration-500"
                            ></div>
                        </div>
                        <p
                            class="text-[10px] mt-1 text-gray-500 uppercase tracking-wider font-semibold"
                        >
                            Strength: {{ strengthText }}
                        </p>
                    </div>
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 mb-2"
                            >Confirm Password</label
                        >
                        <input
                            v-model="form.password_confirmation"
                            type="password"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                            placeholder="••••••••"
                        />
                    </div>
                </div>

                <button
                    type="submit"
                    :disabled="loading"
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                >
                    {{ loading ? "Creating account..." : "Create Account" }}
                </button>

                <p class="text-center text-sm text-gray-600">
                    Already have an account?
                    <button
                        type="button"
                        @click="switchToLogin"
                        class="text-blue-600 font-semibold hover:underline"
                    >
                        Sign in
                    </button>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, reactive, computed } from "vue";
import { useAuthStore } from "../stores/authStore";

const authStore = useAuthStore();

const loading = ref(false);
const error = ref("");

const form = reactive({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const passwordStrength = computed(() => {
    let score = 0;
    if (!form.password) return 0;
    if (form.password.length > 7) score++;
    if (/[A-Z]/.test(form.password)) score++;
    if (/[0-9]/.test(form.password)) score++;
    if (/[^A-Za-z0-9]/.test(form.password)) score++;
    return score;
});

const strengthWidth = computed(() => (passwordStrength.value / 4) * 100 + "%");

const strengthText = computed(() => {
    const labels = ["Very Weak", "Weak", "Medium", "Strong", "Very Strong"];
    return labels[passwordStrength.value];
});

const strengthClass = computed(() => {
    if (passwordStrength.value <= 1) return "bg-red-500";
    if (passwordStrength.value <= 2) return "bg-orange-500";
    if (passwordStrength.value <= 3) return "bg-yellow-500";
    return "bg-green-500";
});

const handleSignup = async () => {
    if (form.password !== form.password_confirmation) {
        error.value = "• Passwords do not match";
        return;
    }

    loading.value = true;
    error.value = "";

    try {
        await authStore.signup(form);
        switchToLogin();
    } catch (err) {
        error.value = err.response?.data?.message || "Failed to create account";
    } finally {
        loading.value = false;
    }
};

const switchToLogin = () => {
    window.dispatchEvent(new Event("switch-to-login"));
};
</script>

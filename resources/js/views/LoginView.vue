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
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                </div>
                <h2 class="mt-6 text-3xl font-bold text-slate-900">
                    Patient Management
                </h2>
                <p class="mt-2 text-sm text-gray-600">
                    Sign in to your account
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <div
                    v-if="error"
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg text-sm error-message"
                >
                    {{ error }}
                </div>

                <div class="space-y-4">
                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 mb-2"
                            >Email Address</label
                        >
                        <input
                            v-model="email"
                            type="email"
                            required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-600 focus:border-transparent transition"
                        />
                    </div>

                    <div>
                        <label
                            class="block text-sm font-medium text-slate-700 mb-2"
                            >Password</label
                        >
                        <input
                            v-model="password"
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
                    {{ loading ? "Signing in..." : "Sign In" }}
                </button>

                <p class="text-center text-sm text-gray-600">
                    Don't have an account?
                    <button
                        type="button"
                        @click="switchToSignup"
                        class="text-blue-600 font-semibold hover:underline"
                    >
                        Sign up
                    </button>
                </p>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { useAuthStore } from "../stores/authStore";

const authStore = useAuthStore();
const email = ref("");
const password = ref("");
const loading = ref(false);
const error = ref("");

const handleLogin = async () => {
    loading.value = true;
    error.value = "";
    try {
        await authStore.login(email.value, password.value);
    } catch (err) {
        error.value = err?.response?.data?.message ?? "Invalid credentials";
    } finally {
        loading.value = false;
    }
};

const switchToSignup = () => {
    window.dispatchEvent(new Event("switch-to-signup"));
};
</script>

<style scoped>
.error-message {
    white-space: pre-line;
}
</style>

<template>
    <div class="min-h-screen bg-gray-50">
        <component :is="currentView" />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { useAuthStore } from "./stores/authStore";
import LoginView from "./views/LoginView.vue";
import SignupView from "./views/SignupView.vue";
import DashboardLayout from "./layouts/DashboardLayout.vue";

const authStore = useAuthStore();
const showSignup = ref(false);

onMounted(() => {
    authStore.checkAuth();
});

const currentView = computed(() => {
    if (!authStore.isAuthenticated) {
        return showSignup.value ? SignupView : LoginView;
    }
    return DashboardLayout;
});

// Global event bus for view switching
window.addEventListener("switch-to-signup", () => {
    showSignup.value = true;
});

window.addEventListener("switch-to-login", () => {
    showSignup.value = false;
});
</script>

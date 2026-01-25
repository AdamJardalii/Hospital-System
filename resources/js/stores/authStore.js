import { defineStore } from "pinia";
import apiClient from "../services/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("userData")) || null,
        token: localStorage.getItem("authToken") || null,
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
        currentUser: (state) => state.user,
    },

    actions: {
        async login(email, password) {
            try {
                const response = await apiClient.post("/auth/login", {
                    email,
                    password,
                });
                const { token, user } = response.data;
                this.token = token;
                this.user = user;
                localStorage.setItem("authToken", token);
                localStorage.setItem("userData", JSON.stringify(user));

                return user;
            } catch (error) {
                this.logout();
                throw error;
            }
        },
        async signup(formData) {
            try {
                const response = await apiClient.post("auth/signup", formData);
                return response;
            } catch (error) {
                throw error;
            }
        },

        async logout() {
            await apiClient.post("/auth/logout").catch(() => {});
            this.user = null;
            this.token = null;
            localStorage.removeItem("authToken");
            localStorage.removeItem("userData");
        },

        checkAuth() {
            const token = localStorage.getItem("authToken");
            const userData = localStorage.getItem("userData");

            if (token && userData) {
                this.token = token;
                this.user = JSON.parse(userData);
                return true;
            }
            return false;
        },
    },
});

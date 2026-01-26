import axios from "axios";

const apiClient = axios.create({
    baseURL: "https://hospital-system-production-6d2b.up.railway.app/api",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

apiClient.interceptors.request.use((config) => {
    const token = localStorage.getItem("authToken");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default apiClient;

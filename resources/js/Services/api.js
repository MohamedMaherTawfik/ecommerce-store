// src/services/api.js
import axios from "axios";
import router from "@/router";

const api = axios.create({
    baseURL: "http://localhost:8000/api/v1",
    timeout: 30000,
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
});

// ─── Request Interceptor ───────────────────────────────────────────────────────
api.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem("auth_token");
        if (token) {
            config.headers.Authorization = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error),
);

// ─── Response Interceptor ─────────────────────────────────────────────────────
api.interceptors.response.use(
    (response) => response,
    (error) => {
        const status = error.response?.status;

        if (status === 401) {
            // Token expired or invalid → clear storage and redirect to login
            localStorage.removeItem("auth_token");
            router.push({ name: "Login" });
        }

        if (status === 403) {
            router.push({ name: "Forbidden" });
        }

        if (status === 500) {
            console.error(
                "Server error:",
                error.response?.data?.message || "Internal Server Error",
            );
        }

        return Promise.reject(error);
    },
);

export default api;

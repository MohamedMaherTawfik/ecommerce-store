// src/services/userService.js
import api from "./api";

/**
 * Fetch total user count
 * @returns {Promise<number>}
 */
export const getUserCount = async () => {
    const res = await api.get("/users/User/count");
    if (!res.data.success) throw new Error("Failed to fetch user count");
    return res.data.data;
};

/**
 * Fetch all users
 * @returns {Promise<Array>}
 */
export const getUsers = async () => {
    const res = await api.get("/users");
    return res.data.data;
};

/**
 * Update a user's name
 * @param {number|string} id
 * @param {string} name
 * @returns {Promise<Object>}
 */
export const updateUser = async (id, name) => {
    const res = await api.post(`/users/${id}`, { name });
    if (!res.data.success) throw new Error("Failed to update user");
    return res.data;
};

/**
 * Delete a user by ID
 * @param {number|string} id
 * @returns {Promise<Object>}
 */
export const deleteUser = async (id) => {
    const res = await api.delete(`/users/${id}`);
    if (!res.data.success) throw new Error("Failed to delete user");
    return res.data;
};

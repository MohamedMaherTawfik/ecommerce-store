// src/services/categoryService.js
import api from "./api";

/**
 * Fetch total category count
 * @returns {Promise<number>}
 */
export const getCategoryCount = async () => {
    const res = await api.get("/categories/category/count");
    if (!res.data.success) throw new Error("Failed to fetch category count");
    return res.data.data;
};

// src/services/brandService.js
import api from "./api";

/**
 * Fetch total brand count
 * @returns {Promise<number>}
 */
export const getBrandCount = async () => {
    const res = await api.get("/brands/brand/count");
    if (!res.data.success) throw new Error("Failed to fetch brand count");
    return res.data.data;
};

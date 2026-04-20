// src/services/productService.js
import api from "./api";

/**
 * Fetch total product count
 * @returns {Promise<number>}
 */
export const getProductCount = async () => {
    const res = await api.get("/products/products/count");
    if (!res.data.success) throw new Error("Failed to fetch product count");
    return res.data.data;
};

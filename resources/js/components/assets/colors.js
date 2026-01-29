// ثابت الألوان
const COLORS = {
    PRIMARY: "#1E293B",
    SECONDARY: "#F8FAFC",
    TEXT: "#E2E8F0",
    TEXT_MUTED: "#94A3B8",
    ACCENT: "#D4AF37",
    SUCCESS: "#10B981",
    BG: "#0F172A",
    LINK_HOVER: "#EAB308",
    CARD_BORDER: "#334155",
    BTN_HOVER_BG: "#C9A354",
    BTN_TEXT: "#0F172A",
    LINK_HOVER: "#EAB308",
};

// دالة لتحويل HEX لـ RGBA
function rgba(hex, alpha = 1) {
    const r = parseInt(hex.slice(1, 3), 16);
    const g = parseInt(hex.slice(3, 5), 16);
    const b = parseInt(hex.slice(5, 7), 16);
    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
}

export { COLORS, rgba };
export default COLORS;

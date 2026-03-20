import axios from "axios";

const apiClient = axios.create({
    baseURL: "/api",
    headers: {
        "Content-Type": "application/json",
    },
    withCredentials: true,
});

let csrfInitialized = false;

const initCSRF = async () => {
    if (csrfInitialized) return;
    await axios.get("/sanctum/csrf-cookie", { withCredentials: true });
    csrfInitialized = true;
};

export const voucherService = {
    async getVouchers(type = null) {
        const params = type ? { type } : {};
        const response = await apiClient.get("/vouchers", { params });
        return response.data;
    },

    async getAvailableVouchers() {
        const response = await apiClient.get("/vouchers/available");
        return response.data;
    },

    async validateVoucher(code) {
        const response = await apiClient.get(`/vouchers/${code}/validate`);
        return response.data;
    },

    async applyVoucher(code) {
        await initCSRF();
        const response = await apiClient.post("/vouchers/apply", { code });
        return response.data;
    },

    async removeVoucher(type) {
        await initCSRF();
        const response = await apiClient.post("/vouchers/remove", { type });
        return response.data;
    },

    async validateCookie() {
        const response = await apiClient.get("/vouchers/validate-cookie");
        return response.data;
    },

    async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand("copy");
                document.body.removeChild(textArea);
                return true;
            } catch (err) {
                document.body.removeChild(textArea);
                return false;
            }
        }
    },
};

export default voucherService;

import { ref, computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import voucherService from "../services/voucherService";

export function useVoucher() {
    const vouchers = ref([]);
    const appliedVouchers = ref({
        shipping: null,
        product: null,
    });
    const isLoading = ref(false);
    const error = ref(null);
    const copiedCode = ref(null);

    const page = usePage();

    const cart = computed(() => page.props.cart);

    const hasAppliedVoucher = computed(() => {
        return appliedVouchers.value.shipping !== null || appliedVouchers.value.product !== null;
    });

    const totalDiscount = computed(() => {
        let total = 0;
        if (appliedVouchers.value.shipping) {
            total += appliedVouchers.value.shipping.discount || 0;
        }
        if (appliedVouchers.value.product) {
            total += appliedVouchers.value.product.discount || 0;
        }
        return total;
    });

    async function fetchVouchers(type = null) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await voucherService.getVouchers(type);
            vouchers.value = response.data || [];
            return vouchers.value;
        } catch (err) {
            error.value = err.response?.data?.error?.message || "Gagal memuat voucher";
            return [];
        } finally {
            isLoading.value = false;
        }
    }

    async function validateVoucher(code) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await voucherService.validateVoucher(code);
            if (response.success) {
                return {
                    success: true,
                    voucher: response.data.voucher,
                    discount: response.data.discount,
                };
            }
            return {
                success: false,
                error: response.error?.message || "Voucher tidak valid",
            };
        } catch (err) {
            return {
                success: false,
                error: err.response?.data?.error?.message || "Gagal memvalidasi voucher",
            };
        } finally {
            isLoading.value = false;
        }
    }

    async function applyVoucher(code) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await voucherService.applyVoucher(code);
            if (response.success) {
                const cartData = response.data.cart;

                if (cartData.shipping_voucher) {
                    appliedVouchers.value.shipping = cartData.shipping_voucher;
                }
                if (cartData.product_voucher) {
                    appliedVouchers.value.product = cartData.product_voucher;
                }

                return {
                    success: true,
                    message: response.message,
                    cart: cartData,
                };
            }
            return {
                success: false,
                error: response.error?.message || "Gagal menggunakan voucher",
            };
        } catch (err) {
            return {
                success: false,
                error: err.response?.data?.error?.message || "Gagal menggunakan voucher",
            };
        } finally {
            isLoading.value = false;
        }
    }

    async function removeVoucher(type) {
        isLoading.value = true;
        error.value = null;

        try {
            const response = await voucherService.removeVoucher(type);
            if (response.success) {
                if (type === "shipping") {
                    appliedVouchers.value.shipping = null;
                } else if (type === "product") {
                    appliedVouchers.value.product = null;
                }

                return {
                    success: true,
                    message: response.message,
                    cart: response.data.cart,
                };
            }
            return {
                success: false,
                error: response.error?.message || "Gagal menghapus voucher",
            };
        } catch (err) {
            return {
                success: false,
                error: err.response?.data?.error?.message || "Gagal menghapus voucher",
            };
        } finally {
            isLoading.value = false;
        }
    }

    async function copyCode(code) {
        const success = await voucherService.copyToClipboard(code);
        if (success) {
            copiedCode.value = code;
            setTimeout(() => {
                copiedCode.value = null;
            }, 2000);
        }
        return success;
    }

    function setAppliedVouchers(shipping, product) {
        if (shipping) {
            appliedVouchers.value.shipping = shipping;
        }
        if (product) {
            appliedVouchers.value.product = product;
        }
    }

    return {
        vouchers,
        appliedVouchers,
        isLoading,
        error,
        copiedCode,
        cart,
        hasAppliedVoucher,
        totalDiscount,
        fetchVouchers,
        validateVoucher,
        applyVoucher,
        removeVoucher,
        copyCode,
        setAppliedVouchers,
    };
}

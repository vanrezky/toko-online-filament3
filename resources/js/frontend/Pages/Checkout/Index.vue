<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useForm, Link, router } from "@inertiajs/vue3";
import axios from "axios";
import { Loader2, Ticket, X, Truck, Tag, AlertCircle } from "lucide-vue-next";
import TemplateWrapper from "../../components/TemplateWrapper.vue";

const props = defineProps({
    cart: Object,
    addresses: Array,
    shippingMethods: Array,
    pendingVouchers: Object,
    validatedVouchers: Object,
    activeGateway: String,
});

const form = useForm({
    address_id: props.addresses?.find((a) => a.is_featured)?.id || props.addresses?.[0]?.id || null,
    shipping_methods: {},
    payment_method: props.activeGateway === 'midtrans' ? 'midtrans' : 'bank_transfer',
    notes: "",
});

const shippingResults = ref([]);
const isLoadingShipping = ref(false);
const isValidatingVouchers = ref(false);
const localValidatedVouchers = ref(props.validatedVouchers || { shipping: null, product: null });
const voucherCode = ref("");
const isApplyingVoucher = ref(false);
const voucherError = ref(null);
const isProcessingOrder = ref(false);

watch(
    () => props.validatedVouchers,
    (newVal) => {
        localValidatedVouchers.value = newVal || { shipping: null, product: null };
    },
    { deep: true },
);

const fetchShippingCosts = async () => {
    if (!form.address_id) return;

    isLoadingShipping.value = true;
    try {
        const response = await axios.get(route("frontend.checkout.shipping-costs"), {
            params: { address_id: form.address_id },
        });
        shippingResults.value = response.data;

        const methods = { ...form.shipping_methods };
        response.data.forEach((item) => {
            if (item.options && item.options.length > 0) {
                methods[item.warehouse_id] = {
                    ...item.options[0],
                    weight: item.weight,
                };
            }
        });
        form.shipping_methods = methods;
    } catch (error) {
        console.error("Failed to fetch shipping costs", error);
    } finally {
        isLoadingShipping.value = false;
    }
};

const validateVouchers = async () => {
    isValidatingVouchers.value = true;
    try {
        const response = await axios.get("/api/vouchers/validate-cookie", {
            withCredentials: true,
        });
        if (response.data.success) {
            localValidatedVouchers.value = response.data.data;
        }
    } catch (error) {
        console.error("Failed to validate vouchers:", error);
    } finally {
        isValidatingVouchers.value = false;
    }
};

watch(
    () => form.address_id,
    () => {
        fetchShippingCosts();
    },
);

onMounted(() => {
    if (form.address_id) {
        fetchShippingCosts();
    }
    validateVouchers();
});

const items = computed(() => props.cart?.items || []);
const subtotal = computed(() => items.value.reduce((total, item) => total + item.price * item.quantity, 0));

const shippingDiscount = computed(() => {
    return localValidatedVouchers.value?.shipping?.discount_amount || 0;
});

const productDiscount = computed(() => {
    return localValidatedVouchers.value?.product?.discount_amount || 0;
});

const totalVoucherDiscount = computed(() => {
    return shippingDiscount.value + productDiscount.value;
});

const hasAnyVoucher = computed(() => {
    return localValidatedVouchers.value?.shipping || localValidatedVouchers.value?.product;
});

const hasInvalidVoucher = computed(() => {
    const shipping = localValidatedVouchers.value?.shipping;
    const product = localValidatedVouchers.value?.product;
    return (shipping && !shipping.valid) || (product && !product.valid);
});

const shippingFee = computed(() => {
    return Object.values(form.shipping_methods).reduce((sum, method) => sum + (method.price || 0), 0);
});

const discountedShippingFee = computed(() => {
    return Math.max(0, shippingFee.value - shippingDiscount.value);
});

const total = computed(() => subtotal.value + discountedShippingFee.value);
const grandTotal = computed(() => {
    return subtotal.value + discountedShippingFee.value - productDiscount.value;
});

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(amount);
};

const submitOrder = async () => {
    if (isValidatingVouchers.value || isProcessingOrder.value) {
        return;
    }
    
    isProcessingOrder.value = true;

    try {
        const response = await axios.post(route("frontend.checkout.store"), form.data());

        if (response.data.success) {
            const payment = response.data.payment;

            if (payment && payment.provider === 'midtrans' && payment.snap_token) {
                const isProduction = payment.payment_url && payment.payment_url.includes('app.midtrans.com');
                const scriptUrl = isProduction 
                    ? 'https://app.midtrans.com/snap/snap.js'
                    : 'https://app.sandbox.midtrans.com/snap/snap.js';

                const loadSnapScript = new Promise((resolve) => {
                    if (document.getElementById('midtrans-script')) {
                        return resolve();
                    }
                    const script = document.createElement('script');
                    script.id = 'midtrans-script';
                    script.src = scriptUrl;
                    script.onload = resolve;
                    document.head.appendChild(script);
                });

                await loadSnapScript;

                window.snap.pay(payment.snap_token, {
                    onSuccess: function() {
                        window.location.href = route('frontend.orders.show', response.data.transaction_uuid);
                    },
                    onPending: function() {
                        window.location.href = route('frontend.orders.show', response.data.transaction_uuid);
                    },
                    onError: function() {
                        window.location.href = route('frontend.orders.show', response.data.transaction_uuid);
                    },
                    onClose: function() {
                        window.location.href = route('frontend.orders.show', response.data.transaction_uuid);
                    }
                });
            } else if (payment && payment.payment_url) {
                window.location.href = payment.payment_url;
            } else {
                window.location.href = route('frontend.orders.show', response.data.transaction_uuid);
            }
        }
    } catch (error) {
        console.error("Checkout failed", error);
        if (error.response && error.response.status === 422) {
            form.errors = error.response.data.errors || {};
        } else {
            alert(error.response?.data?.error || 'Gagal memproses pesanan. Silakan coba lagi.');
        }
    } finally {
        isProcessingOrder.value = false;
    }
};

const removeVoucher = async (type) => {
    try {
        await axios.post("/api/vouchers/remove", { type }, { withCredentials: true });
        localValidatedVouchers.value[type] = null;
    } catch (error) {
        console.error("Failed to remove voucher:", error);
    }
};

const canSubmitOrder = computed(() => {
    return !isValidatingVouchers.value && !hasInvalidVoucher.value && form.address_id && Object.keys(form.shipping_methods).length > 0;
});

const applyVoucher = async () => {
    if (!voucherCode.value.trim()) return;

    isApplyingVoucher.value = true;
    voucherError.value = null;

    try {
        const response = await axios.post("/api/vouchers/apply", { code: voucherCode.value.trim() }, { withCredentials: true });

        if (response.data.success) {
            voucherCode.value = "";
            if (response.data.data?.pending) {
                const pending = response.data.data.pending;
                localValidatedVouchers.value = response.data.data.vouchers || localValidatedVouchers.value;
            }
            await validateVouchers();
        } else {
            voucherError.value = response.data.error?.message || "Voucher tidak valid";
        }
    } catch (error) {
        voucherError.value = error.response?.data?.error?.message || "Terjadi kesalahan";
    } finally {
        isApplyingVoucher.value = false;
    }
};
</script>

<template>
    <TemplateWrapper title="Checkout">
        <div class="min-h-screen bg-[#f8f7fc] py-12 font-sans md:py-20">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-6xl">
                    <h1 class="mb-12 text-3xl font-bold text-[#2d1b0e]">Checkout</h1>

                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                        <!-- Left Side: Forms -->
                        <div class="space-y-6 lg:col-span-7">
                            <!-- Shipping Address Section -->
                            <section class="rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fa8456] text-sm font-bold text-white">
                                            1
                                        </div>
                                        <h2 class="text-base font-bold text-[#2d1b0e]">Alamat Pengiriman</h2>
                                    </div>
                                    <Link
                                        :href="route('frontend.account', { section: 'addresses' })"
                                        class="text-xs font-semibold text-[#fa8456] transition-colors hover:text-[#e56f3f]"
                                        >Kelola</Link
                                    >
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div
                                        v-for="address in addresses"
                                        :key="address.id"
                                        @click="form.address_id = address.id"
                                        class="relative cursor-pointer rounded-xl border p-5 transition-all"
                                        :class="
                                            form.address_id === address.id
                                                ? 'border-[#fa8456] bg-[#fff5f0] ring-2 ring-[#fa8456]/20'
                                                : 'border-[#e8e6ef] bg-[#fafafa] hover:border-[#fa8456]/50'
                                        "
                                    >
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <h3 class="text-sm font-bold text-[#2d1b0e]">{{ address.name }}</h3>
                                                <p class="mt-1 text-xs text-[#6b5a4d]">{{ address.phone }}</p>
                                            </div>
                                            <svg class="h-4 w-4 text-[#fa8456]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"
                                                ></path>
                                                <path
                                                    stroke-linecap="round"
                                                    stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                                                ></path>
                                            </svg>
                                        </div>
                                        <p class="mt-3 text-xs leading-relaxed text-[#6b5a4d]">
                                            {{ address.address }}<br />
                                            {{ address.sub_district_name }}, {{ address.district_name }}<br />
                                            {{ address.province_name }} {{ address.postal_code }}
                                        </p>
                                        <div v-if="form.address_id === address.id" class="absolute -right-1.5 -top-1.5">
                                            <svg class="h-5 w-5 text-[#fa8456] drop-shadow-sm" fill="currentColor" viewBox="0 0 20 20">
                                                <path
                                                    fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"
                                                ></path>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Add New Placeholder -->
                                    <Link
                                        :href="route('frontend.account', { section: 'addresses' })"
                                        class="flex flex-col items-center justify-center gap-2 rounded-xl border-2 border-dashed border-[#e8e6ef] p-6 text-[#6b5a4d] transition-all hover:border-[#fa8456] hover:text-[#fa8456]"
                                    >
                                        <div
                                            class="flex h-10 w-10 items-center justify-center rounded-full border-2 border-current text-xl font-light leading-none"
                                        >
                                            +
                                        </div>
                                        <span class="text-xs font-semibold">Alamat Baru</span>
                                    </Link>
                                </div>
                                <p v-if="form.errors.address_id" class="mt-3 text-xs text-red-500">{{ form.errors.address_id }}</p>
                            </section>

                            <!-- Shipping Method Section -->
                            <section class="rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fa8456] text-sm font-bold text-white">
                                            2
                                        </div>
                                        <h2 class="text-base font-bold text-[#2d1b0e]">Metode Pengiriman</h2>
                                    </div>
                                    <div v-if="isLoadingShipping" class="flex items-center gap-2">
                                        <Loader2 class="h-4 w-4 animate-spin text-[#fa8456]" />
                                        <span class="text-xs font-semibold text-[#6b5a4d]">Memperbarui...</span>
                                    </div>
                                </div>

                                <div v-if="isLoadingShipping" class="space-y-6 animate-pulse">
                                    <div v-for="i in 2" :key="i" class="space-y-4">
                                        <div class="h-4 w-1/3 rounded bg-gray-200"></div>
                                        <div class="space-y-2">
                                            <div v-for="j in 2" :key="j" class="flex items-center justify-between rounded-xl border border-[#e8e6ef] p-4">
                                                <div class="flex items-center gap-4">
                                                    <div class="h-5 w-5 rounded-full bg-gray-200"></div>
                                                    <div>
                                                        <div class="mb-2 h-4 w-24 rounded bg-gray-200"></div>
                                                        <div class="h-3 w-32 rounded bg-gray-200"></div>
                                                    </div>
                                                </div>
                                                <div class="h-4 w-16 rounded bg-gray-200"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div v-else-if="shippingResults.length > 0" class="space-y-6">
                                    <div v-for="warehouse in shippingResults" :key="warehouse.warehouse_id" class="space-y-4">
                                        <h3 class="border-b border-[#f0eef5] pb-2 text-xs font-semibold text-[#6b5a4d]">
                                            Dikirim dari {{ warehouse.warehouse_name }}
                                        </h3>
                                        <div class="space-y-2">
                                            <label
                                                v-for="option in warehouse.options"
                                                :key="option.courier_code"
                                                class="flex cursor-pointer items-center justify-between rounded-xl border p-4 transition-all hover:bg-[#fafafa]"
                                                :class="
                                                    form.shipping_methods[warehouse.warehouse_id]?.courier_code === option.courier_code
                                                        ? 'border-[#fa8456] bg-[#fff5f0]'
                                                        : 'border-[#e8e6ef]'
                                                "
                                            >
                                                <div class="flex items-center">
                                                    <div class="relative flex items-center justify-center">
                                                        <input
                                                            type="radio"
                                                            :name="'shipping_' + warehouse.warehouse_id"
                                                            @change="
                                                                form.shipping_methods[warehouse.warehouse_id] = {
                                                                    ...option,
                                                                    weight: warehouse.weight,
                                                                }
                                                            "
                                                            :checked="
                                                                form.shipping_methods[warehouse.warehouse_id]?.courier_code === option.courier_code
                                                            "
                                                            class="h-5 w-5 border-[#e8e6ef] text-[#fa8456] accent-[#fa8456] focus:ring-[#fa8456]"
                                                        />
                                                    </div>
                                                    <div class="ml-4">
                                                        <span class="block text-sm font-bold text-[#2d1b0e]">{{ option.courier_name }}</span>
                                                        <span class="text-xs text-[#6b5a4d]">Estimasi: {{ option.estimation }}</span>
                                                    </div>
                                                </div>
                                                <span class="text-sm font-bold text-[#fa8456]">{{ formatCurrency(option.price) }}</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div
                                    v-else-if="!isLoadingShipping && form.address_id"
                                    class="rounded-xl border border-dashed border-[#e8e6ef] p-8 text-center"
                                >
                                    <p class="text-xs text-[#6b5a4d]">Tidak ada opsi pengiriman untuk alamat ini.</p>
                                </div>
                                <div v-else-if="!form.address_id" class="rounded-xl border border-dashed border-[#e8e6ef] p-8 text-center">
                                    <p class="text-xs text-[#6b5a4d]">Silakan pilih alamat pengiriman terlebih dahulu.</p>
                                </div>
                                <p v-if="form.errors.shipping_methods" class="mt-3 text-xs text-red-500">{{ form.errors.shipping_methods }}</p>
                            </section>

                            <!-- Payment Method Section -->
                            <section v-if="activeGateway !== 'midtrans'" class="rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fa8456] text-sm font-bold text-white">3</div>
                                    <h2 class="text-base font-bold text-[#2d1b0e]">Metode Pembayaran</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label
                                        v-for="method in [
                                            { id: 'bank_transfer', name: 'Transfer Bank' },
                                            { id: 'qris', name: 'QRIS' },
                                        ]"
                                        :key="method.id"
                                        class="flex cursor-pointer items-center rounded-xl border p-4 transition-all hover:bg-[#fafafa]"
                                        :class="form.payment_method === method.id ? 'border-[#fa8456] bg-[#fff5f0]' : 'border-[#e8e6ef]'"
                                    >
                                        <input
                                            type="radio"
                                            :value="method.id"
                                            v-model="form.payment_method"
                                            class="h-5 w-5 border-[#e8e6ef] text-[#fa8456] accent-[#fa8456] focus:ring-[#fa8456]"
                                        />
                                        <span class="ml-3 text-sm font-semibold text-[#2d1b0e]">{{ method.name }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.payment_method" class="mt-3 text-xs text-red-500">{{ form.errors.payment_method }}</p>
                            </section>

                            <!-- Notes Section -->
                            <section class="rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <h2 class="mb-4 text-sm font-bold text-[#2d1b0e]">Catatan Pesanan (Opsional)</h2>
                                <textarea
                                    v-model="form.notes"
                                    rows="3"
                                    class="w-full rounded-xl border border-[#e8e6ef] p-4 text-sm transition-all focus:border-[#fa8456] focus:outline-none focus:ring-2 focus:ring-[#fa8456]/20"
                                    placeholder="Instruksi khusus untuk pengiriman Anda..."
                                ></textarea>
                            </section>
                        </div>

                        <!-- Right Side: Order Summary -->
                        <div class="lg:col-span-5">
                            <div class="sticky top-32 space-y-6 rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <h2 class="border-b border-[#f0eef5] pb-4 text-sm font-bold text-[#2d1b0e]">Ringkasan Pesanan</h2>

                                <!-- Pending Vouchers Section -->
                                <div v-if="hasAnyVoucher || isValidatingVouchers" class="space-y-3">
                                    <div class="mb-2 flex items-center gap-2">
                                        <Ticket class="h-4 w-4 text-[#fa8456]" />
                                        <span class="text-xs font-semibold text-[#2d1b0e]">Voucher Dipilih</span>
                                    </div>

                                    <!-- Shipping Voucher -->
                                    <div
                                        v-if="localValidatedVouchers?.shipping"
                                        class="rounded-lg border p-3"
                                        :class="localValidatedVouchers.shipping.valid ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-blue-100 text-blue-600">
                                                    <Truck class="h-4 w-4" />
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold text-[#2d1b0e]">
                                                        {{ localValidatedVouchers.shipping.name || localValidatedVouchers.shipping.code }}
                                                    </p>
                                                    <p v-if="localValidatedVouchers.shipping.valid" class="text-xs text-green-600">
                                                        Hemat {{ localValidatedVouchers.shipping.formatted_discount }}
                                                    </p>
                                                    <p v-else class="flex items-center gap-1 text-xs text-red-600">
                                                        <AlertCircle class="h-3 w-3" />
                                                        {{ localValidatedVouchers.shipping.error }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button @click="removeVoucher('shipping')" class="text-gray-400 hover:text-red-500">
                                                <X class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Product Voucher -->
                                    <div
                                        v-if="localValidatedVouchers?.product"
                                        class="rounded-lg border p-3"
                                        :class="localValidatedVouchers.product.valid ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
                                    >
                                        <div class="flex items-start justify-between gap-2">
                                            <div class="flex items-center gap-2">
                                                <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-green-100 text-green-600">
                                                    <Tag class="h-4 w-4" />
                                                </div>
                                                <div>
                                                    <p class="text-xs font-semibold text-[#2d1b0e]">
                                                        {{ localValidatedVouchers.product.name || localValidatedVouchers.product.code }}
                                                    </p>
                                                    <p v-if="localValidatedVouchers.product.valid" class="text-xs text-green-600">
                                                        Hemat {{ localValidatedVouchers.product.formatted_discount }}
                                                    </p>
                                                    <p v-else class="flex items-center gap-1 text-xs text-red-600">
                                                        <AlertCircle class="h-3 w-3" />
                                                        {{ localValidatedVouchers.product.error }}
                                                    </p>
                                                </div>
                                            </div>
                                            <button @click="removeVoucher('product')" class="text-gray-400 hover:text-red-500">
                                                <X class="h-4 w-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Validating State -->
                                    <div v-if="isValidatingVouchers" class="flex items-center justify-center gap-2 py-2">
                                        <Loader2 class="h-4 w-4 animate-spin text-[#fa8456]" />
                                        <span class="text-xs text-[#6b5a4d]">Memvalidasi voucher...</span>
                                    </div>
                                </div>

                                <!-- Voucher Input -->
                                <div class="rounded-lg border border-dashed border-[#e8e6ef] p-4">
                                    <div class="mb-2 flex items-center gap-2">
                                        <Ticket class="h-4 w-4 text-[#fa8456]" />
                                        <span class="text-xs font-semibold text-[#2d1b0e]">Masukkan Kode Voucher</span>
                                    </div>
                                    <div class="flex gap-2">
                                        <input
                                            v-model="voucherCode"
                                            type="text"
                                            placeholder="Contoh: DISKON10"
                                            class="flex-1 rounded-lg border border-[#e8e6ef] bg-[#fafafa] px-3 py-2 text-xs uppercase transition-all focus:border-[#fa8456] focus:bg-white focus:outline-none"
                                            @keyup.enter="applyVoucher"
                                        />
                                        <button
                                            @click="applyVoucher"
                                            :disabled="!voucherCode.trim() || isApplyingVoucher"
                                            class="flex items-center justify-center gap-1 rounded-lg bg-[#fa8456] px-4 py-2 text-xs font-semibold text-white transition-all hover:bg-[#e56f3f] disabled:bg-[#c4bfc9]"
                                        >
                                            <Loader2 v-if="isApplyingVoucher" class="h-3 w-3 animate-spin" />
                                            <span>Pakai</span>
                                        </button>
                                    </div>
                                    <p v-if="voucherError" class="mt-2 text-xs text-red-500">{{ voucherError }}</p>
                                </div>

                                <!-- List Voucher Link -->
                                <Link
                                    :href="route('frontend.vouchers')"
                                    class="flex items-center justify-center gap-2 rounded-lg border border-dashed border-[#e8e6ef] py-3 text-xs font-semibold text-[#6b5a4d] transition-all hover:border-[#fa8456] hover:text-[#fa8456]"
                                >
                                    <Ticket class="h-4 w-4" />
                                    <span>List Voucher</span>
                                </Link>

                                <!-- Order Items (Mini list) -->
                                <div class="max-h-[400px] space-y-4 overflow-y-auto pr-2">
                                    <div v-for="item in items" :key="item.id" class="flex items-start gap-4">
                                        <div class="h-20 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-[#f5f3fc]">
                                            <img
                                                :src="item.product?.thumbnail || 'https://placehold.co/100x120/f5f3fc/2d1b0e?text=Produk'"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div class="min-w-0 flex-grow py-1">
                                            <h4 class="truncate text-sm font-bold text-[#2d1b0e]">{{ item.product?.name }}</h4>
                                            <p v-if="item.product_variant" class="mt-0.5 text-xs text-[#6b5a4d]">
                                                {{ item.product_variant.variant_name }}
                                            </p>
                                            <p class="mt-2 text-xs text-[#6b5a4d]">Qty: {{ item.quantity }}</p>
                                            <p class="mt-2 text-sm font-bold text-[#fa8456]">{{ formatCurrency(item.price) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-3 border-t border-[#f0eef5] pt-6">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b5a4d]">Subtotal</span>
                                        <span class="font-semibold text-[#2d1b0e]">{{ formatCurrency(subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#6b5a4d]">Pengiriman</span>
                                        <span class="font-semibold text-[#2d1b0e]">
                                            {{ formatCurrency(discountedShippingFee) }}
                                            <span v-if="shippingDiscount > 0" class="ml-1 text-xs text-green-600">
                                                (-{{ formatCurrency(shippingDiscount) }})
                                            </span>
                                        </span>
                                    </div>
                                    <div v-if="productDiscount > 0" class="flex justify-between text-sm text-green-600">
                                        <span>Diskon Produk</span>
                                        <span class="font-semibold">-{{ formatCurrency(productDiscount) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-[#e8e6ef] pt-4">
                                        <span class="text-sm font-bold text-[#2d1b0e]">Total</span>
                                        <span class="text-xl font-bold text-[#fa8456]">{{ formatCurrency(grandTotal) }}</span>
                                    </div>
                                    <div v-if="totalVoucherDiscount > 0" class="rounded-lg bg-green-50 p-3 text-center">
                                        <p class="text-sm font-semibold text-green-700">💰 Hemat {{ formatCurrency(totalVoucherDiscount) }}</p>
                                    </div>
                                </div>

                                <button
                                    @click="submitOrder"
                                    :disabled="!canSubmitOrder || isProcessingOrder"
                                    class="flex w-full items-center justify-center gap-3 rounded-full bg-[#fa8456] py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-[#e56f3f] hover:shadow-lg disabled:bg-[#c4bfc9]"
                                >
                                    <svg v-if="isProcessingOrder" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path
                                            class="opacity-75"
                                            fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                                        ></path>
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"
                                        ></path>
                                    </svg>
                                    <span>{{ isProcessingOrder ? "Memproses..." : "Buat Pesanan" }}</span>
                                </button>

                                <div class="pt-2">
                                    <p class="text-center text-xs leading-relaxed text-[#6b5a4d]">
                                        Transaksi terenkripsi dengan aman. Dengan menyelesaikan pembelian, Anda menyetujui
                                        <a href="#" class="text-[#fa8456] underline underline-offset-2 hover:text-[#e56f3f]">Syarat & Ketentuan</a>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

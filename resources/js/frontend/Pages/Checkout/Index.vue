<script setup>
import { ref, computed, watch, onMounted } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import axios from "axios";
import { Loader2 } from "lucide-vue-next";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Truck, CreditCard, ShoppingBag, ChevronRight, MapPin, CheckCircle } from "lucide-vue-next";

const props = defineProps({
    cart: Object,
    addresses: Array,
    shippingMethods: Array,
});

const form = useForm({
    address_id: props.addresses?.find((a) => a.is_featured)?.id || props.addresses?.[0]?.id || null,
    shipping_methods: {},
    payment_method: "bank_transfer",
    notes: "",
});

const shippingResults = ref([]);
const isLoadingShipping = ref(false);

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
});

const items = computed(() => props.cart?.items || []);
const subtotal = computed(() => items.value.reduce((total, item) => total + item.price * item.quantity, 0));
const shippingFee = computed(() => {
    return Object.values(form.shipping_methods).reduce((sum, method) => sum + (method.price || 0), 0);
});
const total = computed(() => subtotal.value + shippingFee.value);

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(amount);
};

const submitOrder = () => {
    form.post(route("frontend.checkout.store"));
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
                                            <MapPin class="h-4 w-4 text-[#fa8456]" />
                                        </div>
                                        <p class="mt-3 text-xs leading-relaxed text-[#6b5a4d]">
                                            {{ address.address }}<br />
                                            {{ address.sub_district?.name }}, {{ address.district?.name }}<br />
                                            {{ address.province?.name }} {{ address.postal_code }}
                                        </p>
                                        <div v-if="form.address_id === address.id" class="absolute -right-1.5 -top-1.5">
                                            <CheckCircle class="h-5 w-5 text-[#fa8456] drop-shadow-sm" />
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

                                <div v-if="shippingResults.length > 0" class="space-y-6">
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
                            <section class="rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <div class="mb-6 flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#fa8456] text-sm font-bold text-white">3</div>
                                    <h2 class="text-base font-bold text-[#2d1b0e]">Metode Pembayaran</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                                    <label
                                        v-for="method in [
                                            { id: 'bank_transfer', name: 'Transfer Bank', icon: CreditCard },
                                            { id: 'credit_card', name: 'Kartu Kredit', icon: CreditCard },
                                            { id: 'qris', name: 'QRIS', icon: CreditCard },
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
                                        <component :is="method.icon" class="ml-4 h-5 w-5 text-[#6b5a4d]" />
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
                                        <span class="font-semibold text-[#2d1b0e]">{{ formatCurrency(shippingFee) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-[#e8e6ef] pt-4">
                                        <span class="text-sm font-bold text-[#2d1b0e]">Total</span>
                                        <span class="text-xl font-bold text-[#fa8456]">{{ formatCurrency(total) }}</span>
                                    </div>
                                </div>

                                <button
                                    @click="submitOrder"
                                    :disabled="form.processing"
                                    class="flex w-full items-center justify-center gap-3 rounded-full bg-[#fa8456] py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-[#e56f3f] hover:shadow-lg disabled:bg-[#c4bfc9]"
                                >
                                    <ShoppingBag class="h-5 w-5" />
                                    <span>{{ form.processing ? "Memproses..." : "Buat Pesanan" }}</span>
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

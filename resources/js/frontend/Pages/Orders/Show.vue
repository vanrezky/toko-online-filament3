<script setup>
import { computed, ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Package, ChevronLeft, Clock, MapPin, Truck, CreditCard, CheckCircle2, AlertCircle, ArrowRight, ExternalLink, X } from "lucide-vue-next";

const props = defineProps({
    order: Object,
    paymentMethods: Array,
});

const statusColors = {
    unpaid: "text-[#fa8456] bg-[#fff5f0] border-[#fed7aa]",
    shipped: "text-[#3b82f6] bg-[#eff6ff] border-[#bfdbfe]",
    delivered: "text-[#22c55e] bg-[#f0fdf4] border-[#bbf7d0]",
    completed: "text-[#16a34a] bg-[#dcfce7] border-[#86efac]",
    rejected: "text-[#ef4444] bg-[#fef2f2] border-[#fecaca]",
};

const statusLabels = {
    unpaid: "Menunggu Pembayaran",
    shipped: "Dikirim",
    delivered: "Diterima",
    completed: "Selesai",
    rejected: "Ditolak",
};

const formatCurrency = (amount) => {
    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(amount);
};

const formatDate = (dateString) => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    });
};

const subtotal = computed(() => {
    return props.order.products?.reduce((acc, p) => acc + p.price * p.quantity, 0) || 0;
});

const isExpired = computed(() => {
    return props.order.timelimit && new Date(props.order.timelimit) < new Date();
});

const showPaymentModal = ref(false);
const paymentForm = useForm({
    payment_method: props.order.payment_method,
});

const changePayment = () => {
    paymentForm.post(route("frontend.orders.payment-method", props.order.id), {
        onSuccess: () => (showPaymentModal.value = false),
        preserveScroll: true,
    });
};
</script>

<template>
    <TemplateWrapper title="Detail Pesanan">
        <div class="min-h-screen bg-[#f8f7fc] py-12 font-sans md:py-20">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-5xl space-y-8">
                    <!-- Header -->
                    <div class="flex flex-col gap-6 border-b border-[#e8e6ef] pb-8 md:flex-row md:items-center md:justify-between">
                        <div class="space-y-2">
                            <Link
                                :href="route('frontend.orders')"
                                class="group flex items-center gap-2 text-sm text-[#6b5a4d] transition-colors hover:text-[#fa8456]"
                            >
                                <ChevronLeft class="h-4 w-4" />
                                <span>Kembali ke Pesanan</span>
                            </Link>
                            <h1 class="text-2xl font-bold text-[#2d1b0e]">Pesanan #{{ order.id.substring(0, 8).toUpperCase() }}</h1>
                            <p class="text-sm text-[#6b5a4d]">{{ formatDate(order.created_at) }}</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="rounded-full border px-6 py-2 text-sm font-semibold" :class="statusColors[order.status]">
                                {{ statusLabels[order.status] || order.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Timelimit Warning -->
                    <div
                        v-if="order.status === 'unpaid' && order.timelimit"
                        class="flex flex-col gap-4 rounded-xl p-6 md:flex-row md:items-center md:justify-between"
                        :class="isExpired ? 'border border-red-100 bg-red-50 text-red-600' : 'border border-orange-100 bg-[#fff5f0] text-[#fa8456]'"
                    >
                        <div class="flex items-center gap-4">
                            <Clock class="h-6 w-6" />
                            <div>
                                <p class="font-semibold text-[#2d1b0e]">
                                    {{ isExpired ? "Pesanan ini sudah kedaluwarsa" : "Menunggu Pembayaran" }}
                                </p>
                                <p class="text-sm text-[#6b5a4d]">
                                    {{ isExpired ? "Silakan buat pesanan baru" : "Selesaikan pembayaran sebelum" }} {{ formatDate(order.timelimit) }}
                                </p>
                            </div>
                        </div>
                        <button
                            v-if="!isExpired"
                            class="rounded-full bg-[#fa8456] px-8 py-3 text-sm font-bold text-white shadow-md transition-all hover:bg-[#e56f3f] hover:shadow-lg"
                        >
                            Bayar Sekarang
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                        <!-- Items & Shipping -->
                        <div class="space-y-6 lg:col-span-8">
                            <!-- Items Section -->
                            <section class="rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <h2 class="mb-6 border-b border-[#f0eef5] pb-4 text-base font-bold text-[#2d1b0e]">Item Pesanan</h2>
                                <div class="space-y-6">
                                    <div v-for="item in order.products" :key="item.uuid" class="flex gap-5">
                                        <div class="h-24 w-20 flex-shrink-0 overflow-hidden rounded-xl bg-[#f5f3fc]">
                                            <img
                                                :src="item.product?.thumbnail || 'https://placehold.co/100x120/f5f3fc/2d1b0e?text=Produk'"
                                                class="h-full w-full object-cover"
                                            />
                                        </div>
                                        <div class="flex flex-grow flex-col py-1">
                                            <div class="flex justify-between">
                                                <div>
                                                    <h4 class="text-sm font-bold text-[#2d1b0e]">{{ item.product?.name }}</h4>
                                                    <p v-if="item.description" class="mt-1 text-xs text-[#6b5a4d]">{{ item.description }}</p>
                                                </div>
                                                <p class="text-sm font-bold text-[#fa8456]">{{ formatCurrency(item.price) }}</p>
                                            </div>
                                            <div class="mt-auto flex items-center justify-between text-sm text-[#6b5a4d]">
                                                <span>Jumlah: {{ item.quantity }}</span>
                                                <span class="font-semibold text-[#2d1b0e]"
                                                    >Total: {{ formatCurrency(item.price * item.quantity) }}</span
                                                >
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Shipping Details -->
                            <section class="grid grid-cols-1 gap-5 md:grid-cols-2">
                                <div class="space-y-4 rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm">
                                    <div class="flex items-center gap-3 text-[#fa8456]">
                                        <MapPin class="h-5 w-5" />
                                        <h2 class="text-sm font-bold text-[#2d1b0e]">Alamat Pengiriman</h2>
                                    </div>
                                    <div class="space-y-1 text-sm leading-relaxed text-[#6b5a4d]">
                                        <p class="font-semibold text-[#2d1b0e]">{{ order.address.name || "Alamat Pengiriman" }}</p>
                                        <p>{{ order.address.phone || "" }}</p>
                                        <p>{{ order.address.full_address }}</p>
                                        <p>{{ order.address.sub_district }}, {{ order.address.district }}</p>
                                        <p>{{ order.address.province }} {{ order.address.postal_code }}</p>
                                    </div>
                                </div>
                                <div class="space-y-4 rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm">
                                    <div class="flex items-center gap-3 text-[#fa8456]">
                                        <Truck class="h-5 w-5" />
                                        <h2 class="text-sm font-bold text-[#2d1b0e]">Metode Pengiriman</h2>
                                    </div>
                                    <div class="space-y-1 text-sm text-[#6b5a4d]">
                                        <p class="font-semibold text-[#2d1b0e]">{{ order.shipping_method || "Standard Shipping" }}</p>
                                        <p>Biaya: {{ formatCurrency(order.shipping_cost) }}</p>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Payment & Totals -->
                        <div class="space-y-6 lg:col-span-4">
                            <!-- Payment Section -->
                            <section class="space-y-5 rounded-xl border border-[#e8e6ef] bg-white p-6 shadow-sm md:p-8">
                                <div class="flex items-center justify-between border-b border-[#f0eef5] pb-4">
                                    <div class="flex items-center gap-3 text-[#fa8456]">
                                        <CreditCard class="h-5 w-5" />
                                        <h2 class="text-sm font-bold text-[#2d1b0e]">Pembayaran</h2>
                                    </div>
                                    <button
                                        v-if="order.status === 'unpaid' && !isExpired"
                                        @click="showPaymentModal = true"
                                        class="text-sm font-semibold text-[#fa8456] transition-colors hover:text-[#e56f3f]"
                                    >
                                        Ubah
                                    </button>
                                </div>
                                <div class="space-y-1 text-sm text-[#6b5a4d]">
                                    <p class="font-semibold text-[#2d1b0e]">{{ order.payment_method?.replace("_", " ") || "Belum dipilih" }}</p>
                                    <p>
                                        Status:
                                        <span :class="order.status === 'unpaid' ? 'text-[#fa8456]' : 'text-[#22c55e]'">{{
                                            order.status === "unpaid" ? "Menunggu Pembayaran" : "Lunas"
                                        }}</span>
                                    </p>
                                </div>
                            </section>

                            <!-- Totals Section -->
                            <section class="space-y-5 rounded-xl bg-[#2d1b0e] p-6 text-white shadow-lg md:p-8">
                                <h2 class="border-b border-white/10 pb-4 text-sm font-bold text-[#c4bfc9]">Ringkasan Pesanan</h2>
                                <div class="space-y-3">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#c4bfc9]">Subtotal</span>
                                        <span class="font-semibold">{{ formatCurrency(order.subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-[#c4bfc9]">Pengiriman</span>
                                        <span class="font-semibold">{{ formatCurrency(order.shipping_cost) }}</span>
                                    </div>
                                    <div v-if="order.discount > 0" class="flex justify-between text-sm text-[#86efac]">
                                        <span>Diskon</span>
                                        <span>- {{ formatCurrency(order.discount) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-white/20 pt-4">
                                        <span class="text-sm font-bold">Total</span>
                                        <span class="text-xl font-bold text-[#fa8456]">{{ formatCurrency(order.total) }}</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Selection Modal -->
        <transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-2xl bg-white p-8 shadow-2xl">
                    <div class="mb-8 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-[#2d1b0e]">Ubah Metode Pembayaran</h3>
                        <button @click="showPaymentModal = false" class="text-[#6b5a4d] transition-colors hover:text-[#2d1b0e]">
                            <X class="h-6 w-6" />
                        </button>
                    </div>

                    <form @submit.prevent="changePayment" class="space-y-5">
                        <div class="space-y-3">
                            <label
                                v-for="method in paymentMethods"
                                :key="method.id"
                                class="flex cursor-pointer items-center rounded-xl border p-4 transition-all"
                                :class="
                                    paymentForm.payment_method === method.id
                                        ? 'border-[#fa8456] bg-[#fff5f0]'
                                        : 'border-[#e8e6ef] hover:border-[#fa8456]/50'
                                "
                            >
                                <input
                                    type="radio"
                                    v-model="paymentForm.payment_method"
                                    :value="method.id"
                                    class="h-5 w-5 border-[#e8e6ef] text-[#fa8456] accent-[#fa8456] focus:ring-[#fa8456]"
                                />
                                <span class="ml-4 text-sm font-semibold text-[#2d1b0e]">{{ method.name }}</span>
                            </label>
                        </div>

                        <div class="flex flex-col gap-3 pt-4">
                            <button
                                type="submit"
                                :disabled="paymentForm.processing"
                                class="w-full rounded-full bg-[#fa8456] py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-[#e56f3f] hover:shadow-lg disabled:bg-[#c4bfc9]"
                            >
                                {{ paymentForm.processing ? "Memperbarui..." : "Konfirmasi" }}
                            </button>
                            <button
                                type="button"
                                @click="showPaymentModal = false"
                                class="w-full py-3 text-sm font-semibold text-[#6b5a4d] transition-colors hover:text-[#2d1b0e]"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </TemplateWrapper>
</template>

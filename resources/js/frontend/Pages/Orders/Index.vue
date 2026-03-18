<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Package, ChevronRight, Clock, CheckCircle2, Truck, AlertCircle } from "lucide-vue-next";

const props = defineProps({
    orders: Array,
});

const statusColors = {
    unpaid: "text-[#fa8456] bg-[#fff5f0]",
    shipped: "text-[#3b82f6] bg-[#eff6ff]",
    delivered: "text-[#22c55e] bg-[#f0fdf4]",
    completed: "text-[#16a34a] bg-[#dcfce7]",
    rejected: "text-[#ef4444] bg-[#fef2f2]",
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
    return new Date(dateString).toLocaleDateString("id-ID", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};

const formatTime = (dateString) => {
    return new Date(dateString).toLocaleTimeString("id-ID", {
        hour: "2-digit",
        minute: "2-digit",
    });
};

const isExpired = (dateString) => {
    return new Date(dateString) < new Date();
};
</script>

<template>
    <TemplateWrapper title="Pesanan Saya">
        <div class="min-h-screen bg-[#f8f7fc] py-12 font-sans md:py-20">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-4xl space-y-8">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <h1 class="text-3xl font-bold text-[#2d1b0e]">Pesanan Saya</h1>
                    </div>

                    <div v-if="orders && orders.length > 0" class="space-y-5">
                        <div
                            v-for="order in orders"
                            :key="order.id"
                            class="group space-y-5 rounded-xl border border-[#e8e6ef] bg-white p-5 shadow-sm transition-all hover:shadow-md"
                        >
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-[#fff5f0]">
                                        <Package class="h-5 w-5 text-[#fa8456]" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-[#2d1b0e]">Order #{{ order.id.substring(0, 8).toUpperCase() }}</h3>
                                        <p class="mt-0.5 text-xs text-[#6b5a4d]">{{ formatDate(order.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4">
                                    <span
                                        class="rounded-full px-4 py-1.5 text-xs font-semibold"
                                        :class="statusColors[order.status] || 'bg-[#f5f3fc] text-[#6b5a4d]'"
                                    >
                                        {{ statusLabels[order.status] || order.status }}
                                    </span>
                                    <p class="text-sm font-bold text-[#fa8456]">{{ formatCurrency(order.total) }}</p>
                                    <Link
                                        :href="route('frontend.orders.show', order.id)"
                                        class="p-2 text-[#6b5a4d] transition-colors group-hover:text-[#fa8456]"
                                    >
                                        <ChevronRight class="h-5 w-5" />
                                    </Link>
                                </div>
                            </div>

                            <!-- Timelimit Warning -->
                            <div
                                v-if="order.status === 'unpaid' && order.timelimit"
                                class="flex items-center gap-3 rounded-xl p-4"
                                :class="isExpired(order.timelimit) ? 'bg-red-50 text-[#ef4444]' : 'bg-[#fff5f0] text-[#fa8456]'"
                            >
                                <Clock class="h-4 w-4 flex-shrink-0" />
                                <span class="text-xs font-semibold">
                                    {{ isExpired(order.timelimit) ? "Pembayaran berakhir pada" : "Selesaikan pembayaran sebelum" }}
                                    {{ formatDate(order.timelimit) }} {{ formatTime(order.timelimit) }}
                                </span>
                            </div>

                            <!-- Preview Images -->
                            <div class="flex gap-3 overflow-x-auto border-t border-[#f0eef5] pt-5">
                                <div v-for="item in order.products" :key="item.uuid" class="group/item relative flex-shrink-0">
                                    <div class="h-20 w-16 overflow-hidden rounded-xl bg-[#f5f3fc]">
                                        <img
                                            :src="item.product?.thumbnail || 'https://placehold.co/100x120/f5f3fc/2d1b0e?text=Produk'"
                                            class="h-full w-full object-cover"
                                        />
                                    </div>
                                    <span
                                        class="absolute -right-1.5 -top-1.5 flex h-5 w-5 items-center justify-center rounded-full border-2 border-white bg-[#fa8456] text-[10px] font-bold text-white"
                                    >
                                        {{ item.quantity }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="space-y-6 border border-[#e8e6ef] bg-white py-24 text-center shadow-sm">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-[#f5f3fc]">
                            <Package class="h-10 w-10 text-[#c4bfc9]" />
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-base font-bold text-[#2d1b0e]">Belum Ada Pesanan</h2>
                            <p class="text-sm text-[#6b5a4d]">Anda belum melakukan transaksi apapun.</p>
                        </div>
                        <Link
                            :href="route('frontend.products')"
                            class="inline-block rounded-full bg-[#fa8456] px-10 py-4 text-sm font-bold text-white shadow-md transition-all hover:bg-[#e56f3f] hover:shadow-lg"
                        >
                            Mulai Belanja
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

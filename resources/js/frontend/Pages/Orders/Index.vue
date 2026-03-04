<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Package, ChevronRight, Clock, CheckCircle2, Truck, AlertCircle } from "lucide-vue-next";

const props = defineProps({
    orders: Array,
});

const statusColors = {
    unpaid: "text-orange-500 bg-orange-50",
    shipped: "text-blue-500 bg-blue-50",
    delivered: "text-green-500 bg-green-50",
    completed: "text-green-600 bg-green-100",
    rejected: "text-red-500 bg-red-50",
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
  });
};

const formatTime = (dateString) => {
  return new Date(dateString).toLocaleTimeString('id-ID', {
    hour: '2-digit',
    minute: '2-digit'
  });
};

const isExpired = (dateString) => {
  return new Date(dateString) < new Date();
};
</script>

<template>
    <TemplateWrapper title="My Orders">
        <div class="min-h-screen bg-gray-50 py-12 md:py-20 font-sans">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-4xl space-y-8">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <h1 class="text-3xl font-bold tracking-tight text-black italic uppercase">Order History</h1>
                    </div>

                    <div v-if="orders && orders.length > 0" class="space-y-6">
                        <div v-for="order in orders" :key="order.id" class="group space-y-6 bg-white p-6 shadow-sm border border-gray-100 transition-all hover:border-gray-200">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="rounded-full bg-gray-50 p-3">
                                        <Package class="h-6 w-6 text-black" />
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-bold text-black uppercase tracking-widest">Order #{{ order.id.substring(0, 8).toUpperCase() }}</h3>
                                        <p class="mt-1 text-[10px] uppercase tracking-widest text-gray-400 font-medium">Placed on {{ formatDate(order.created_at) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <span
                                        class="rounded-full px-4 py-1.5 text-[10px] font-bold uppercase tracking-widest"
                                        :class="statusColors[order.status] || 'bg-gray-50 text-gray-500'"
                                    >
                                        {{ order.status }}
                                    </span>
                                    <p class="text-sm font-bold text-black tracking-tight">{{ formatCurrency(order.total) }}</p>
                                    <Link :href="route('frontend.orders.show', order.id)" class="p-2 text-gray-400 transition-colors group-hover:text-black">
                                        <ChevronRight class="h-5 w-5" />
                                    </Link>
                                </div>
                            </div>

                            <!-- Timelimit Warning -->
                            <div v-if="order.status === 'unpaid' && order.timelimit" 
                                 class="flex items-center space-x-3 p-4 border"
                                 :class="isExpired(order.timelimit) ? 'bg-red-50 border-red-100 text-red-600' : 'bg-orange-50 border-orange-100 text-orange-600'">
                                <Clock class="h-4 w-4" />
                                <span class="text-[10px] font-bold uppercase tracking-widest">
                                    {{ isExpired(order.timelimit) ? 'Payment expired on' : 'Please pay before' }} 
                                    {{ formatDate(order.timelimit) }} {{ formatTime(order.timelimit) }}
                                </span>
                            </div>

                            <!-- Preview Images -->
                            <div class="scrollbar-hidden flex space-x-4 overflow-x-auto border-t border-gray-50 pb-2 pt-6">
                                <div v-for="item in order.products" :key="item.uuid" class="relative group/item flex-shrink-0">
                                    <div class="h-24 w-20 bg-gray-50 border border-gray-100 overflow-hidden">
                                        <img :src="item.product?.thumbnail || 'https://placehold.co/100x120?text=Product'" class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                                    </div>
                                    <span class="absolute -top-2 -right-2 bg-black text-white text-[8px] font-bold h-5 w-5 flex items-center justify-center rounded-full border-2 border-white">
                                        {{ item.quantity }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="space-y-8 bg-white py-32 text-center shadow-sm border border-gray-100">
                        <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-gray-50">
                            <Package class="h-10 w-10 text-gray-200" />
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-sm font-bold uppercase tracking-[0.2em] text-black">Your bag is empty</h2>
                            <p class="text-[10px] uppercase tracking-widest text-gray-400">You haven't placed any orders yet.</p>
                        </div>
                        <Link
                            :href="route('frontend.products')"
                            class="inline-block bg-black px-12 py-4 text-[11px] font-bold uppercase tracking-[0.3em] text-white transition-all hover:bg-gray-800 shadow-xl"
                        >
                            Start Shopping
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

<script setup>
import { ref } from "vue";
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
</script>

<template>
    <TemplateWrapper title="My Orders">
        <div class="min-h-screen bg-gray-50 py-12 md:py-20">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-4xl space-y-8">
                    <div class="flex flex-col justify-between gap-4 md:flex-row md:items-center">
                        <h1 class="text-3xl font-bold tracking-tight text-black">Order History</h1>
                        <div class="flex space-x-2">
                            <button class="border border-black bg-white px-4 py-2 text-xs font-bold uppercase tracking-widest transition-all">
                                All Orders
                            </button>
                            <button
                                class="border border-transparent bg-transparent px-4 py-2 text-xs font-bold uppercase tracking-widest text-gray-400 transition-all hover:text-black"
                            >
                                Unpaid
                            </button>
                        </div>
                    </div>

                    <div v-if="orders && orders.length > 0" class="space-y-6">
                        <!-- Mock Orders -->
                        <div v-for="i in 3" :key="i" class="group space-y-6 bg-white p-6 shadow-sm md:p-8">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                <div class="flex items-center space-x-4">
                                    <div class="rounded-full bg-gray-50 p-3">
                                        <Package class="h-6 w-6 text-black" />
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-bold text-black">Order #ORD-2024-000{{ i }}</h3>
                                        <p class="mt-1 text-[10px] uppercase tracking-widest text-gray-400">Placed on Jan {{ 10 + i }}, 2024</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-6">
                                    <span
                                        class="rounded-full px-3 py-1 text-[10px] font-bold uppercase tracking-widest"
                                        :class="statusColors['shipped']"
                                    >
                                        Shipped
                                    </span>
                                    <p class="text-sm font-bold text-black">$150.00</p>
                                    <Link :href="route('frontend.orders.show', i)" class="p-2 text-gray-400 transition-colors group-hover:text-black">
                                        <ChevronRight class="h-5 w-5" />
                                    </Link>
                                </div>
                            </div>

                            <!-- Preview Images -->
                            <div class="scrollbar-hidden flex space-x-3 overflow-x-auto border-t border-gray-100 pb-2 pt-6">
                                <div v-for="j in 2" :key="j" class="h-20 w-16 flex-shrink-0 bg-gray-50">
                                    <img src="https://placehold.co/100x120?text=Product" class="h-full w-full object-cover" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="space-y-8 bg-white py-20 text-center shadow-sm">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gray-50">
                            <Package class="h-8 w-8 text-gray-300" />
                        </div>
                        <div class="space-y-2">
                            <h2 class="text-xl font-bold text-black">No orders found</h2>
                            <p class="text-sm text-gray-500">You haven't placed any orders yet.</p>
                        </div>
                        <Link
                            :href="route('frontend.products')"
                            class="inline-block bg-black px-8 py-3 text-sm font-bold uppercase tracking-widest text-white transition-all hover:bg-gray-800"
                        >
                            Start Shopping
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

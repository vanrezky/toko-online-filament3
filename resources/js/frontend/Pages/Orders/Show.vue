<script setup>
import { computed, ref } from "vue";
import { Link, useForm } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { 
  Package, 
  ChevronLeft, 
  Clock, 
  MapPin, 
  Truck, 
  CreditCard, 
  CheckCircle2, 
  AlertCircle,
  ArrowRight,
  ExternalLink
} from "lucide-vue-next";

const props = defineProps({
    order: Object,
    paymentMethods: Array,
});

const statusColors = {
    unpaid: "text-orange-500 bg-orange-50 border-orange-100",
    shipped: "text-blue-500 bg-blue-50 border-blue-100",
    delivered: "text-green-500 bg-green-50 border-green-100",
    completed: "text-green-600 bg-green-100 border-green-200",
    rejected: "text-red-500 bg-red-50 border-red-100",
};

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  });
};

const subtotal = computed(() => {
    return props.order.products?.reduce((acc, p) => acc + (p.price * p.quantity), 0) || 0;
});

const isExpired = computed(() => {
  return props.order.timelimit && new Date(props.order.timelimit) < new Date();
});

const showPaymentModal = ref(false);
const paymentForm = useForm({
    payment_method: props.order.payment_method
});

const changePayment = () => {
    paymentForm.post(route('frontend.orders.payment-method', props.order.id), {
        onSuccess: () => showPaymentModal.value = false,
        preserveScroll: true
    });
};
</script>

<template>
    <TemplateWrapper title="Order Details">
        <div class="min-h-screen bg-gray-50 py-12 md:py-20 font-sans">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-5xl space-y-8">
                    <!-- Header -->
                    <div class="flex flex-col gap-6 md:flex-row md:items-center md:justify-between border-b border-gray-100 pb-8">
                        <div class="space-y-2">
                            <Link :href="route('frontend.orders')" class="group flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-black transition-colors">
                                <ChevronLeft class="h-3 w-3" />
                                <span>Back to History</span>
                            </Link>
                            <h1 class="text-3xl font-bold tracking-tight text-black italic uppercase">Order #{{ order.id.substring(0, 8).toUpperCase() }}</h1>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400">Placed on {{ formatDate(order.created_at) }}</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span
                                class="rounded-full border px-6 py-2 text-[10px] font-bold uppercase tracking-[0.2em]"
                                :class="statusColors[order.status]"
                            >
                                {{ order.status }}
                            </span>
                        </div>
                    </div>

                    <!-- Timelimit Warning -->
                    <div v-if="order.status === 'unpaid' && order.timelimit" 
                         class="flex flex-col md:flex-row md:items-center justify-between gap-4 p-6 border shadow-sm"
                         :class="isExpired ? 'bg-red-50 border-red-100 text-red-600' : 'bg-orange-50 border-orange-100 text-orange-600'">
                        <div class="flex items-center space-x-4">
                            <Clock class="h-6 w-6" />
                            <div>
                                <p class="text-[11px] font-bold uppercase tracking-[0.1em]">
                                    {{ isExpired ? 'This order has expired' : 'Awaiting Payment' }}
                                </p>
                                <p class="text-[10px] font-medium uppercase tracking-widest mt-0.5">
                                    {{ isExpired ? 'Please place a new order' : 'Complete payment before' }} {{ formatDate(order.timelimit) }}
                                </p>
                            </div>
                        </div>
                        <button v-if="!isExpired" class="bg-black text-white px-8 py-3 text-[10px] font-bold uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg">
                            Pay Now
                        </button>
                    </div>

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
                        <!-- Items & Shipping -->
                        <div class="space-y-8 lg:col-span-8">
                            <!-- Items Section -->
                            <section class="bg-white p-8 shadow-sm border border-gray-100">
                                <h2 class="mb-8 text-xs font-bold uppercase tracking-[0.2em] text-black border-b border-gray-50 pb-4">Order Items</h2>
                                <div class="space-y-8">
                                    <div v-for="item in order.products" :key="item.uuid" class="flex gap-6">
                                        <div class="h-32 w-24 flex-shrink-0 bg-gray-50 border border-gray-50 overflow-hidden">
                                            <img :src="item.product?.thumbnail || 'https://placehold.co/100x120?text=Product'" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="flex flex-grow flex-col py-1">
                                            <div class="flex justify-between">
                                                <div>
                                                    <h4 class="text-sm font-bold uppercase tracking-wider text-black">{{ item.product?.name }}</h4>
                                                    <p v-if="item.description" class="mt-1 text-[10px] font-medium uppercase tracking-widest text-gray-400">{{ item.description }}</p>
                                                </div>
                                                <p class="text-sm font-bold text-black">{{ formatCurrency(item.price) }}</p>
                                            </div>
                                            <div class="mt-auto flex items-center justify-between text-[10px] font-bold uppercase tracking-widest text-gray-500">
                                                <span>Quantity: {{ item.quantity }}</span>
                                                <span class="text-black">Total: {{ formatCurrency(item.price * item.quantity) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Shipping Details -->
                            <section class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="bg-white p-8 shadow-sm border border-gray-100 space-y-4">
                                    <div class="flex items-center space-x-3 text-black">
                                        <MapPin class="h-4 w-4" />
                                        <h2 class="text-[11px] font-bold uppercase tracking-widest">Delivery Address</h2>
                                    </div>
                                    <div class="space-y-2 text-[11px] leading-relaxed text-gray-500 uppercase tracking-tight">
                                        <p class="font-bold text-black">{{ order.address.name || 'Shipping Address' }}</p>
                                        <p>{{ order.address.phone || '' }}</p>
                                        <p>{{ order.address.full_address }}</p>
                                        <p>{{ order.address.sub_district }}, {{ order.address.district }}</p>
                                        <p>{{ order.address.province }} {{ order.address.postal_code }}</p>
                                    </div>
                                </div>
                                <div class="bg-white p-8 shadow-sm border border-gray-100 space-y-4">
                                    <div class="flex items-center space-x-3 text-black">
                                        <Truck class="h-4 w-4" />
                                        <h2 class="text-[11px] font-bold uppercase tracking-widest">Shipping Method</h2>
                                    </div>
                                    <div class="space-y-1 text-[11px] text-gray-500 uppercase tracking-widest">
                                        <p class="font-bold text-black">{{ order.shipping_method || 'Standard Shipping' }}</p>
                                        <p>Cost: {{ formatCurrency(order.shipping_cost) }}</p>
                                    </div>
                                </div>
                            </section>
                        </div>

                        <!-- Payment & Totals -->
                        <div class="space-y-8 lg:col-span-4">
                            <!-- Payment Section -->
                            <section class="bg-white p-8 shadow-sm border border-gray-100 space-y-6">
                                <div class="flex items-center justify-between border-b border-gray-50 pb-4">
                                    <div class="flex items-center space-x-3 text-black">
                                        <CreditCard class="h-4 w-4" />
                                        <h2 class="text-[11px] font-bold uppercase tracking-widest">Payment</h2>
                                    </div>
                                    <button 
                                        v-if="order.status === 'unpaid' && !isExpired"
                                        @click="showPaymentModal = true"
                                        class="text-[9px] font-bold uppercase tracking-widest text-blue-600 hover:text-blue-800 transition-colors"
                                    >
                                        Change
                                    </button>
                                </div>
                                <div class="space-y-1 text-[11px] text-gray-500 uppercase tracking-widest">
                                    <p class="font-bold text-black">{{ order.payment_method?.replace('_', ' ') || 'Not selected' }}</p>
                                    <p>Status: <span :class="order.status === 'unpaid' ? 'text-orange-500' : 'text-green-500'">{{ order.status === 'unpaid' ? 'Awaiting Payment' : 'Paid' }}</span></p>
                                </div>
                            </section>

                            <!-- Totals Section -->
                            <section class="bg-black p-8 text-white shadow-xl space-y-6">
                                <h2 class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 border-b border-white/10 pb-4">Order Summary</h2>
                                <div class="space-y-4">
                                    <div class="flex justify-between text-[11px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400">Subtotal</span>
                                        <span>{{ formatCurrency(order.subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400">Shipping</span>
                                        <span>{{ formatCurrency(order.shipping_cost) }}</span>
                                    </div>
                                    <div v-if="order.discount > 0" class="flex justify-between text-[11px] font-bold uppercase tracking-widest text-green-400">
                                        <span>Discount</span>
                                        <span>- {{ formatCurrency(order.discount) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-white/20 pt-6">
                                        <span class="text-xs font-bold uppercase tracking-[0.3em]">Total</span>
                                        <span class="text-2xl font-bold italic">{{ formatCurrency(order.total) }}</span>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Selection Modal -->
        <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0" enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in" leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="showPaymentModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                <div class="w-full max-w-md bg-white p-8 shadow-2xl border border-gray-100 animate-in zoom-in-95 duration-300">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-lg font-bold uppercase tracking-[0.2em] italic">Change Payment</h3>
                        <button @click="showPaymentModal = false" class="text-gray-400 hover:text-black transition-colors">
                            <span class="text-2xl">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="changePayment" class="space-y-6">
                        <div class="space-y-3">
                            <label v-for="method in paymentMethods" :key="method.id" 
                                class="flex items-center p-4 border cursor-pointer transition-all hover:bg-gray-50"
                                :class="paymentForm.payment_method === method.id ? 'border-black bg-gray-50' : 'border-gray-100'">
                                <input type="radio" v-model="paymentForm.payment_method" :value="method.id" class="h-4 w-4 text-black focus:ring-black rounded-none border-gray-300" />
                                <span class="ml-4 text-xs font-bold uppercase tracking-widest">{{ method.name }}</span>
                            </label>
                        </div>

                        <div class="flex flex-col gap-3 pt-4">
                            <button type="submit" :disabled="paymentForm.processing" class="w-full bg-black text-white py-4 text-[11px] font-bold uppercase tracking-[0.3em] hover:bg-gray-800 disabled:bg-gray-400 transition-all shadow-lg">
                                {{ paymentForm.processing ? 'Updating...' : 'Confirm Change' }}
                            </button>
                            <button type="button" @click="showPaymentModal = false" class="w-full py-4 text-[11px] font-bold uppercase tracking-[0.2em] text-gray-400 hover:text-black transition-all">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
    </TemplateWrapper>
</template>

<style scoped>
.scrollbar-hidden::-webkit-scrollbar {
    display: none;
}
.scrollbar-hidden {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

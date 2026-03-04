<script setup>
import { ref, computed } from "vue";
import { useForm, Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Truck, CreditCard, ShoppingBag, ChevronRight, MapPin } from "lucide-vue-next";

const props = defineProps({
    cart: Object,
    addresses: Array,
    shippingMethods: Array,
});

const form = useForm({
    address_id: props.addresses?.find(a => a.is_featured)?.id || props.addresses?.[0]?.id || null,
    shipping_method: props.shippingMethods?.[0]?.id || null,
    payment_method: "bank_transfer",
    notes: "",
});

const items = computed(() => props.cart?.items || []);
const subtotal = computed(() => items.value.reduce((total, item) => total + (item.price * item.quantity), 0));
const selectedShipping = computed(() => props.shippingMethods?.find(m => m.id === form.shipping_method));
const shippingFee = computed(() => selectedShipping.value?.price || 0);
const total = computed(() => subtotal.value + shippingFee.value);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(amount);
};

const submitOrder = () => {
    form.post(route("frontend.checkout.store"));
};
</script>

<template>
    <TemplateWrapper title="Checkout">
        <div class="min-h-screen bg-gray-50 py-12 md:py-20 font-sans">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-6xl">
                    <h1 class="mb-12 text-3xl font-bold tracking-tight text-black italic uppercase">Checkout</h1>

                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                        <!-- Left Side: Forms -->
                        <div class="space-y-8 lg:col-span-7">
                            <!-- Shipping Address Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm border border-gray-100">
                                <div class="mb-6 flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex h-8 w-8 items-center justify-center bg-black text-sm font-bold text-white">1</div>
                                        <h2 class="text-lg font-bold uppercase tracking-widest text-black">Shipping Address</h2>
                                    </div>
                                    <Link :href="route('frontend.account', { section: 'addresses' })" class="text-[10px] font-bold uppercase tracking-widest text-black border-b border-black pb-0.5 hover:text-gray-500 hover:border-gray-500 transition-all">Manage</Link>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <div
                                        v-for="address in addresses"
                                        :key="address.id"
                                        @click="form.address_id = address.id"
                                        class="relative cursor-pointer space-y-3 border p-6 transition-all"
                                        :class="form.address_id === address.id ? 'border-black ring-1 ring-black' : 'border-gray-100 hover:border-gray-200'"
                                    >
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h3 class="text-xs font-bold text-black uppercase tracking-wider">{{ address.name }}</h3>
                                                <p class="text-[10px] font-bold text-gray-400 mt-1 uppercase">{{ address.phone }}</p>
                                            </div>
                                            <MapPin class="h-4 w-4 text-gray-300" />
                                        </div>
                                        <p class="text-[11px] leading-relaxed text-gray-500 uppercase tracking-tight">
                                            {{ address.address }}<br />
                                            {{ address.sub_district?.name }}, {{ address.district?.name }}<br />
                                            {{ address.province?.name }} {{ address.postal_code }}
                                        </p>
                                        <div v-if="form.address_id === address.id" class="absolute right-2 top-2">
                                            <div class="flex h-4 w-4 items-center justify-center rounded-full bg-black">
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Add New Placeholder -->
                                    <Link :href="route('frontend.account', { section: 'addresses' })" 
                                          class="flex flex-col items-center justify-center space-y-2 border-2 border-dashed border-gray-100 p-6 text-gray-400 hover:border-gray-300 hover:text-gray-600 transition-all">
                                        <div class="h-8 w-8 rounded-full border border-current flex items-center justify-center text-xl font-light">+</div>
                                        <span class="text-[10px] font-bold uppercase tracking-widest">New Address</span>
                                    </Link>
                                </div>
                                <p v-if="form.errors.address_id" class="text-xs text-red-500">{{ form.errors.address_id }}</p>
                            </section>

                            <!-- Shipping Method Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm border border-gray-100">
                                <div class="mb-6 flex items-center space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center bg-black text-sm font-bold text-white">2</div>
                                    <h2 class="text-lg font-bold uppercase tracking-widest text-black">Shipping Method</h2>
                                </div>

                                <div class="space-y-3">
                                    <label
                                        v-for="method in shippingMethods"
                                        :key="method.id"
                                        class="flex cursor-pointer items-center justify-between border p-5 transition-all hover:bg-gray-50"
                                        :class="form.shipping_method === method.id ? 'border-black' : 'border-gray-100'"
                                    >
                                        <div class="flex items-center">
                                            <div class="relative flex items-center justify-center">
                                                <input
                                                    type="radio"
                                                    :value="method.id"
                                                    v-model="form.shipping_method"
                                                    class="h-4 w-4 border-gray-300 text-black focus:ring-black rounded-none"
                                                />
                                            </div>
                                            <div class="ml-4">
                                                <span class="block text-xs font-bold text-black uppercase tracking-widest">{{ method.name }}</span>
                                                <span class="text-[10px] text-gray-400 uppercase">Estimated arrival in {{ method.id === 'standard' ? '3-5' : '1-2' }} days</span>
                                            </div>
                                        </div>
                                        <span class="text-sm font-bold text-black">{{ formatCurrency(method.price) }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.shipping_method" class="text-xs text-red-500">{{ form.errors.shipping_method }}</p>
                            </section>

                            <!-- Payment Method Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm border border-gray-100">
                                <div class="mb-6 flex items-center space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center bg-black text-sm font-bold text-white">3</div>
                                    <h2 class="text-lg font-bold uppercase tracking-widest text-black">Payment Method</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <label
                                        v-for="method in [
                                            { id: 'bank_transfer', name: 'Bank Transfer', icon: CreditCard },
                                            { id: 'credit_card', name: 'Credit Card', icon: CreditCard },
                                            { id: 'paypal', name: 'PayPal', icon: CreditCard }
                                        ]"
                                        :key="method.id"
                                        class="flex cursor-pointer items-center border p-5 transition-all hover:bg-gray-50"
                                        :class="form.payment_method === method.id ? 'border-black ring-1 ring-black bg-gray-50' : 'border-gray-100'"
                                    >
                                        <input
                                            type="radio"
                                            :value="method.id"
                                            v-model="form.payment_method"
                                            class="h-4 w-4 border-gray-300 text-black focus:ring-black rounded-none"
                                        />
                                        <component :is="method.icon" class="ml-4 h-5 w-5 text-gray-400" />
                                        <span class="ml-3 text-xs font-bold uppercase tracking-widest text-black">{{ method.name }}</span>
                                    </label>
                                </div>
                                <p v-if="form.errors.payment_method" class="text-xs text-red-500">{{ form.errors.payment_method }}</p>
                            </section>

                            <!-- Notes Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm border border-gray-100">
                                <h2 class="text-xs font-bold uppercase tracking-widest text-black">Order Notes (Optional)</h2>
                                <textarea 
                                    v-model="form.notes"
                                    rows="3"
                                    class="w-full border border-gray-100 p-4 text-xs focus:border-black focus:outline-none transition-all"
                                    placeholder="Any special instructions for your delivery?"
                                ></textarea>
                            </section>
                        </div>

                        <!-- Right Side: Order Summary -->
                        <div class="lg:col-span-5">
                            <div class="sticky top-32 space-y-8 bg-white p-8 shadow-sm border border-gray-100">
                                <h2 class="text-xs font-bold uppercase tracking-widest text-black border-b border-gray-50 pb-4">Your Order Summary</h2>

                                <!-- Order Items (Mini list) -->
                                <div class="scrollbar-hidden max-h-[400px] space-y-6 overflow-y-auto pr-2">
                                    <div v-for="item in items" :key="item.id" class="flex items-start space-x-4">
                                        <div class="h-24 w-20 flex-shrink-0 bg-gray-50 border border-gray-50">
                                            <img :src="item.product?.thumbnail || 'https://placehold.co/100x120?text=Product'" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="min-w-0 flex-grow py-1">
                                            <h4 class="truncate text-[10px] font-bold uppercase tracking-widest text-black">{{ item.product?.name }}</h4>
                                            <p v-if="item.product_variant" class="text-[9px] uppercase tracking-tighter text-gray-400 mt-0.5">
                                                {{ item.product_variant.variant_name }}
                                            </p>
                                            <p class="text-[10px] uppercase tracking-widest text-gray-500 mt-2">Qty: {{ item.quantity }}</p>
                                            <p class="mt-2 text-xs font-bold text-black tracking-tight">{{ formatCurrency(item.price) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4 border-t border-gray-100 pt-8">
                                    <div class="flex justify-between text-[11px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400">Subtotal</span>
                                        <span class="text-black">{{ formatCurrency(subtotal) }}</span>
                                    </div>
                                    <div class="flex justify-between text-[11px] font-bold uppercase tracking-widest">
                                        <span class="text-gray-400">Shipping</span>
                                        <span class="text-black">{{ formatCurrency(shippingFee) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-black pt-6">
                                        <span class="text-xs font-bold uppercase tracking-[0.2em] text-black">Total Amount</span>
                                        <span class="text-xl font-bold text-black italic">{{ formatCurrency(total) }}</span>
                                    </div>
                                </div>

                                <button
                                    @click="submitOrder"
                                    :disabled="form.processing"
                                    class="flex w-full items-center justify-center space-x-4 bg-black py-5 text-center text-[11px] font-bold uppercase tracking-[0.3em] text-white shadow-2xl transition-all hover:bg-gray-800 disabled:bg-gray-400"
                                >
                                    <ShoppingBag class="h-4 w-4" />
                                    <span>{{ form.processing ? 'Processing...' : 'Place My Order' }}</span>
                                </button>

                                <div class="flex flex-col items-center space-y-4 pt-4">
                                    <p class="text-center text-[9px] leading-relaxed text-gray-400 uppercase tracking-tighter">
                                        Secure encrypted transaction. By completing your purchase, you agree to our <br />
                                        <a href="#" class="text-black underline underline-offset-2 hover:text-gray-500">Terms of Service</a>.
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

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
    address_id: null,
    shipping_method: null,
    payment_method: "bank_transfer",
    notes: "",
});

const submitOrder = () => {
    form.post(route("frontend.checkout.store"));
};
</script>

<template>
    <TemplateWrapper title="Checkout">
        <div class="min-h-screen bg-gray-50 py-12 md:py-20">
            <div class="container mx-auto px-4 md:px-6">
                <div class="mx-auto max-w-6xl">
                    <h1 class="mb-12 text-3xl font-bold tracking-tight text-black">Checkout</h1>

                    <div class="grid grid-cols-1 gap-12 lg:grid-cols-12">
                        <!-- Left Side: Forms -->
                        <div class="space-y-8 lg:col-span-7">
                            <!-- Shipping Address Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm">
                                <div class="mb-6 flex items-center space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center bg-black text-sm font-bold text-white">1</div>
                                    <h2 class="text-lg font-bold uppercase tracking-widest text-black">Shipping Address</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <!-- Mock Addresses -->
                                    <div
                                        v-for="i in 2"
                                        :key="i"
                                        @click="form.address_id = i"
                                        class="relative cursor-pointer space-y-2 border-2 p-6 transition-all"
                                        :class="form.address_id === i ? 'border-black' : 'border-gray-100 hover:border-gray-200'"
                                    >
                                        <div class="flex justify-between">
                                            <h3 class="text-sm font-bold text-black">Home Address {{ i }}</h3>
                                            <MapPin class="h-4 w-4 text-gray-400" />
                                        </div>
                                        <p class="text-xs leading-relaxed text-gray-500">
                                            123 Fashion Street, Suite {{ i }}01<br />
                                            Beverly Hills, CA 90210<br />
                                            United States
                                        </p>
                                        <div v-if="form.address_id === i" class="absolute right-2 top-2">
                                            <div class="flex h-4 w-4 items-center justify-center rounded-full bg-black">
                                                <div class="h-1.5 w-1.5 rounded-full bg-white"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    class="border-b border-black pb-1 text-xs font-bold uppercase tracking-widest text-black transition-all hover:border-gray-500 hover:text-gray-500"
                                >
                                    + Add New Address
                                </button>
                            </section>

                            <!-- Shipping Method Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm">
                                <div class="mb-6 flex items-center space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center bg-black text-sm font-bold text-white">2</div>
                                    <h2 class="text-lg font-bold uppercase tracking-widest text-black">Shipping Method</h2>
                                </div>

                                <div class="space-y-4">
                                    <label
                                        v-for="method in ['Standard Shipping (3-5 days)', 'Express Shipping (1-2 days)']"
                                        :key="method"
                                        class="flex cursor-pointer items-center justify-between border border-gray-100 p-4 transition-colors hover:bg-gray-50"
                                    >
                                        <div class="flex items-center">
                                            <input
                                                type="radio"
                                                :value="method"
                                                v-model="form.shipping_method"
                                                class="h-4 w-4 border-gray-300 text-black focus:ring-black"
                                            />
                                            <span class="ml-4 text-sm font-medium text-gray-900">{{ method }}</span>
                                        </div>
                                        <span class="text-sm font-bold text-black">$15.00</span>
                                    </label>
                                </div>
                            </section>

                            <!-- Payment Method Section -->
                            <section class="space-y-6 bg-white p-8 shadow-sm">
                                <div class="mb-6 flex items-center space-x-3">
                                    <div class="flex h-8 w-8 items-center justify-center bg-black text-sm font-bold text-white">3</div>
                                    <h2 class="text-lg font-bold uppercase tracking-widest text-black">Payment Method</h2>
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <label
                                        v-for="method in ['Credit Card', 'Bank Transfer', 'PayPal']"
                                        :key="method"
                                        class="flex cursor-pointer items-center border border-gray-100 p-4 transition-colors hover:bg-gray-50"
                                        :class="{ 'border-black bg-gray-50': form.payment_method === method }"
                                    >
                                        <input
                                            type="radio"
                                            :value="method"
                                            v-model="form.payment_method"
                                            class="h-4 w-4 border-gray-300 text-black focus:ring-black"
                                        />
                                        <CreditCard class="ml-4 h-5 w-5 text-gray-400" />
                                        <span class="ml-3 text-sm font-medium text-gray-900">{{ method }}</span>
                                    </label>
                                </div>
                            </section>
                        </div>

                        <!-- Right Side: Order Summary -->
                        <div class="lg:col-span-5">
                            <div class="sticky top-32 space-y-8 bg-white p-8 shadow-sm">
                                <h2 class="text-sm font-bold uppercase tracking-widest text-black">Your Order</h2>

                                <!-- Order Items (Mini list) -->
                                <div class="scrollbar-hidden max-h-60 space-y-4 overflow-y-auto pr-2">
                                    <div v-for="i in 3" :key="i" class="flex items-center space-x-4">
                                        <div class="h-20 w-16 flex-shrink-0 bg-gray-50">
                                            <img src="https://placehold.co/100x120?text=Product" class="h-full w-full object-cover" />
                                        </div>
                                        <div class="min-w-0 flex-grow">
                                            <h4 class="truncate text-xs font-bold text-black">Premium Cotton T-Shirt</h4>
                                            <p class="text-[10px] uppercase tracking-widest text-gray-400">Qty: 1 • Size: L</p>
                                            <p class="mt-1 text-xs font-bold text-black">$45.00</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-4 border-t border-gray-100 pt-8">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Subtotal</span>
                                        <span class="font-medium text-black">$135.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-500">Shipping</span>
                                        <span class="font-medium text-black">$15.00</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-6">
                                        <span class="text-base font-bold uppercase tracking-widest text-black">Total</span>
                                        <span class="text-2xl font-bold text-black">$150.00</span>
                                    </div>
                                </div>

                                <button
                                    @click="submitOrder"
                                    class="flex w-full items-center justify-center space-x-3 bg-black py-5 text-center text-sm font-bold uppercase tracking-widest text-white shadow-xl transition-all hover:bg-gray-800"
                                >
                                    <ShoppingBag class="h-5 w-5" />
                                    <span>Complete Purchase</span>
                                </button>

                                <p class="text-center text-[10px] leading-relaxed text-gray-400">
                                    By completing your purchase, you agree to our <br />
                                    <a href="#" class="underline hover:text-black">Terms of Service</a> and
                                    <a href="#" class="underline hover:text-black">Privacy Policy</a>.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

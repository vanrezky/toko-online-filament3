<script setup>
import { computed, ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { X, Minus, Plus, ShoppingBag, Trash2 } from "lucide-vue-next";
import { formatCurrency } from "../../lib/utils";

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    cart: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(["close"]);

const page = usePage();

const items = computed(() => props.cart?.items || []);
const subtotal = computed(() => {
    return items.value.reduce((total, item) => total + item.price * item.quantity, 0);
});
const tax = computed(() => Math.round(subtotal.value * 0.1));
const total = computed(() => subtotal.value + tax.value);
const itemCount = computed(() => items.value.reduce((count, item) => count + item.quantity, 0));

const updateQuantity = (item, delta) => {
    const newQty = item.quantity + delta;
    if (newQty < 1) return;

    router.patch(
        route("frontend.cart.update", item.id),
        {
            quantity: newQty,
        },
        {
            preserveScroll: true,
        },
    );
};

const removeItem = (id) => {
    router.delete(route("frontend.cart.destroy", id), {
        preserveScroll: true,
    });
};

const closeCart = () => {
    emit("close");
};

const proceedToCheckout = () => {
    router.visit(route("frontend.checkout"));
    closeCart();
};

const continueShopping = () => {
    router.visit(route("frontend.products"));
    closeCart();
};
</script>

<template>
    <Teleport to="body">
        <!-- Backdrop -->
        <Transition name="fade">
            <div v-if="isOpen" class="fixed inset-0 z-[100] bg-black/50 backdrop-blur-sm" @click="closeCart"></div>
        </Transition>

        <!-- Cart Panel -->
        <Transition name="slide">
            <div v-if="isOpen" class="fixed bottom-0 right-0 top-0 z-[110] flex w-full flex-col bg-white shadow-2xl sm:w-[360px]">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-border p-4">
                    <h2 class="text-lg font-bold text-foreground">Keranjang Anda</h2>
                    <button @click="closeCart" class="rounded-full p-2 text-muted-foreground transition-colors hover:bg-muted hover:text-foreground">
                        <X class="h-5 w-5" />
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="flex-grow overflow-y-auto">
                    <!-- Empty State -->
                    <div v-if="items.length === 0" class="flex h-full flex-col items-center justify-center p-8 text-center">
                        <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-secondary">
                            <ShoppingBag class="h-8 w-8 text-muted-foreground" />
                        </div>
                        <h3 class="mb-2 text-lg font-bold text-foreground">Keranjang Anda kosong</h3>
                        <p class="mb-6 text-sm text-muted-foreground">Yuk mulai belanja dan temukan produk favoritmu!</p>
                        <button
                            @click="continueShopping"
                            class="rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
                        >
                            Mulai Belanja
                        </button>
                    </div>

                    <!-- Items List -->
                    <div v-else class="space-y-4 p-4">
                        <div v-for="item in items" :key="item.id" class="flex gap-3 border-b border-border pb-4 last:border-0">
                            <!-- Product Image -->
                            <div class="h-16 w-16 flex-shrink-0 overflow-hidden rounded-lg bg-secondary">
                                <img
                                    :src="item.product?.thumbnail || 'https://placehold.co/100x100?text=No+Image'"
                                    :alt="item.product?.name"
                                    class="h-full w-full object-cover"
                                />
                            </div>

                            <!-- Item Details -->
                            <div class="min-w-0 flex-grow">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="truncate text-sm font-semibold text-foreground">{{ item.product?.name }}</h4>
                                        <p v-if="item.product_variant" class="text-xs text-muted-foreground">
                                            {{ item.product_variant.variant_name }}
                                        </p>
                                        <p class="mt-1 text-sm font-bold text-primary">
                                            {{ formatCurrency(item.price) }}
                                        </p>
                                    </div>
                                    <button
                                        @click="removeItem(item.id)"
                                        class="flex-shrink-0 p-1 text-muted-foreground transition-colors hover:text-destructive"
                                    >
                                        <Trash2 class="h-4 w-4" />
                                    </button>
                                </div>

                                <!-- Quantity Controls -->
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="flex items-center rounded-full border border-border">
                                        <button
                                            @click="updateQuantity(item, -1)"
                                            class="flex h-8 w-8 items-center justify-center text-muted-foreground transition-colors hover:text-foreground"
                                        >
                                            <Minus class="h-3 w-3" />
                                        </button>
                                        <span class="w-8 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                        <button
                                            @click="updateQuantity(item, 1)"
                                            class="flex h-8 w-8 items-center justify-center text-muted-foreground transition-colors hover:text-foreground"
                                        >
                                            <Plus class="h-3 w-3" />
                                        </button>
                                    </div>
                                    <span class="text-sm font-semibold text-foreground">
                                        {{ formatCurrency(item.price * item.quantity) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary & Actions -->
                <div v-if="items.length > 0" class="space-y-4 border-t border-border bg-white p-4">
                    <!-- Summary -->
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Subtotal</span>
                            <span class="font-medium text-foreground">{{ formatCurrency(subtotal) }}</span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Estimasi Pajak (10%)</span>
                            <span class="font-medium text-foreground">{{ formatCurrency(tax) }}</span>
                        </div>
                        <div class="flex justify-between border-t border-border pt-2">
                            <span class="text-base font-bold text-foreground">Total</span>
                            <span class="text-lg font-bold text-primary">{{ formatCurrency(total) }}</span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <button
                            @click="proceedToCheckout"
                            class="w-full rounded-full bg-primary py-3 text-sm font-semibold text-primary-foreground shadow-md transition-colors hover:bg-primary/90 hover:shadow-lg"
                        >
                            Lanjut ke Pembayaran
                        </button>
                        <button
                            @click="continueShopping"
                            class="w-full rounded-full bg-secondary py-3 text-sm font-semibold text-foreground transition-colors hover:bg-muted"
                        >
                            Lanjut Belanja
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.slide-enter-active,
.slide-leave-active {
    transition: transform 0.3s ease;
}

.slide-enter-from,
.slide-leave-to {
    transform: translateX(100%);
}
</style>

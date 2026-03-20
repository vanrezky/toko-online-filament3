<script setup>
import { computed, ref, watch } from "vue";
import { Link, router } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Trash2, ShoppingBag, ArrowRight, Minus, Plus } from "lucide-vue-next";
import { formatCurrency } from "../../lib/utils";
import debounce from "lodash/debounce";

const props = defineProps({
    cart: Object,
});

const localItems = ref([...(props.cart?.items || [])]);

watch(
    () => props.cart?.items,
    (newItems) => {
        localItems.value = [...(newItems || [])];
    },
    { deep: true },
);

const subtotal = computed(() => {
    return localItems.value.reduce((total, item) => total + item.price * item.quantity, 0);
});

const updateQuantity = debounce((itemId, newQty) => {
    if (newQty < 1) return;

    router.patch(
        route("frontend.cart.update", itemId),
        {
            quantity: newQty,
        },
        {
            preserveScroll: true,
        },
    );
}, 300);

const handleQuantityChange = (item, delta) => {
    const newQty = item.quantity + delta;
    if (newQty < 1) return;

    item.quantity = newQty;
    updateQuantity(item.id, newQty);
};

const removeItem = (id) => {
    router.delete(route("frontend.cart.destroy", id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <TemplateWrapper title="Keranjang Belanja">
        <div class="min-h-screen bg-secondary/30 py-8 md:py-12">
            <div class="container mx-auto px-4">
                <div v-if="localItems.length > 0">
                    <h1 class="mb-8 text-2xl font-bold text-foreground md:text-3xl">Keranjang Belanja</h1>

                    <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_380px]">
                        <!-- Cart Items List -->
                        <div class="space-y-4">
                            <div v-for="item in localItems" :key="item.id" class="overflow-hidden rounded-2xl bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-4 sm:flex-row">
                                    <div class="h-28 w-28 shrink-0 overflow-hidden rounded-xl bg-secondary sm:h-32 sm:w-32">
                                        <Link :href="route('frontend.product-detail', item.product?.slug)">
                                            <img
                                                :src="item.product?.thumbnail || 'https://placehold.co/200x200?text=No+Image'"
                                                :alt="item.product?.name"
                                                class="h-full w-full object-cover transition-transform hover:scale-105"
                                            />
                                        </Link>
                                    </div>

                                    <div class="flex min-w-0 flex-grow flex-col justify-between">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="min-w-0 flex-grow">
                                                <Link
                                                    :href="route('frontend.product-detail', item.product?.slug)"
                                                    class="line-clamp-2 text-sm font-bold text-foreground transition-colors hover:text-primary sm:text-base"
                                                >
                                                    {{ item.product?.name }}
                                                </Link>
                                                <p v-if="item.product_variant" class="mt-1 text-xs text-muted-foreground">
                                                    {{ item.product_variant.variant_name }}
                                                </p>
                                                <div class="mt-2 flex items-center gap-2">
                                                    <span class="text-sm font-bold text-primary sm:text-base">
                                                        {{ formatCurrency(item.price) }}
                                                    </span>
                                                    <span
                                                        v-if="item.original_price && item.original_price > item.price"
                                                        class="text-xs text-muted-foreground line-through"
                                                    >
                                                        {{ formatCurrency(item.original_price) }}
                                                    </span>
                                                </div>
                                            </div>
                                            <button
                                                @click="removeItem(item.id)"
                                                class="flex shrink-0 items-center justify-center rounded-full p-2 text-muted-foreground transition-all hover:bg-red-50 hover:text-red-500"
                                            >
                                                <Trash2 class="h-5 w-5" />
                                            </button>
                                        </div>

                                        <div class="mt-4 flex items-center justify-between sm:mt-0">
                                            <div class="flex items-center rounded-full border border-border bg-secondary/50">
                                                <button
                                                    @click="handleQuantityChange(item, -1)"
                                                    class="flex h-10 w-10 items-center justify-center rounded-l-full text-muted-foreground transition-all hover:bg-secondary disabled:cursor-not-allowed disabled:opacity-50"
                                                    :disabled="item.quantity <= 1"
                                                >
                                                    <Minus class="h-4 w-4" />
                                                </button>
                                                <span class="w-14 text-center text-sm font-semibold">{{ item.quantity }}</span>
                                                <button
                                                    @click="handleQuantityChange(item, 1)"
                                                    class="flex h-10 w-10 items-center justify-center rounded-r-full text-muted-foreground transition-all hover:bg-secondary"
                                                >
                                                    <Plus class="h-4 w-4" />
                                                </button>
                                            </div>

                                            <span class="text-lg font-bold text-foreground sm:text-xl">
                                                {{ formatCurrency(item.price * item.quantity) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="rounded-2xl border-2 border-dashed border-border bg-white p-4">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <Link
                                        :href="route('frontend.products')"
                                        class="flex items-center gap-2 text-sm font-semibold text-muted-foreground transition-colors hover:text-primary"
                                    >
                                        <ShoppingBag class="h-4 w-4" />
                                        Lanjut Belanja
                                    </Link>
                                    <Link
                                        :href="route('frontend.home')"
                                        class="text-sm font-semibold text-muted-foreground transition-colors hover:text-primary"
                                    >
                                        Beranda
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Order Summary Sidebar (Right) -->
                        <div>
                            <div class="sticky top-24 space-y-4">
                                <div class="rounded-2xl bg-white p-6 shadow-sm">
                                    <h2 class="mb-6 text-lg font-bold text-foreground">Ringkasan Belanja</h2>

                                    <div class="space-y-4 border-b border-border pb-4">
                                        <div class="flex justify-between text-sm">
                                            <span class="text-muted-foreground">Subtotal ({{ localItems.length }} item)</span>
                                            <span class="font-medium text-foreground">{{ formatCurrency(subtotal) }}</span>
                                        </div>
                                        <div class="flex justify-between text-sm">
                                            <span class="text-muted-foreground">Ongkos Kirim</span>
                                            <span class="font-medium text-foreground">Dihitung saat checkout</span>
                                        </div>
                                    </div>

                                    <div class="py-4">
                                        <div class="mb-2 flex items-center justify-between">
                                            <span class="text-lg font-bold text-foreground">Total</span>
                                            <span class="text-2xl font-bold text-primary">{{ formatCurrency(subtotal) }}</span>
                                        </div>
                                    </div>

                                    <Link
                                        :href="route('frontend.checkout')"
                                        class="flex w-full items-center justify-center gap-2 rounded-full bg-primary py-4 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg"
                                    >
                                        <span>Checkout</span>
                                        <ArrowRight class="h-4 w-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-else class="py-20 text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-secondary">
                        <ShoppingBag class="h-12 w-12 text-muted-foreground" />
                    </div>
                    <h2 class="mb-2 text-2xl font-bold text-foreground">Keranjang Anda kosong</h2>
                    <p class="mx-auto mb-8 max-w-md text-muted-foreground">Yuk mulai belanja dan temukan produk favoritmu!</p>
                    <Link
                        :href="route('frontend.products')"
                        class="inline-flex items-center gap-2 rounded-full bg-primary px-8 py-4 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg"
                    >
                        <ShoppingBag class="h-5 w-5" />
                        Mulai Belanja
                    </Link>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

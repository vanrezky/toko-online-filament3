<script setup>
import { Link, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { ShoppingCart, Heart } from "lucide-vue-next";
import { formatCurrency } from "../../lib/utils";

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    size: {
        type: String,
        default: "normal",
        validator: (value) => ["small", "normal", "large"].includes(value),
    },
});

const isHovered = ref(false);
const isWishlisted = ref(false);

const isSale = computed(() => !!props.product.sale_price);

const displayPrice = computed(() => {
    if (isSale.value) {
        return props.product.sale_price;
    }
    return props.product.price;
});

const originalPrice = computed(() => {
    if (isSale.value) {
        return props.product.price;
    }
    return null;
});

const discountPercentage = computed(() => {
    if (isSale.value && originalPrice.value) {
        return Math.round((1 - displayPrice.value / originalPrice.value) * 100);
    }
    return null;
});

const badge = computed(() => {
    if (isSale.value) {
        return { text: `${discountPercentage.value}%`, class: "bg-gradient-to-r from-destructive to-rose-500 text-white" };
    }
    if (props.product.is_new) {
        return { text: "Baru", class: "bg-gradient-to-r from-primary to-primary/80 text-primary-foreground" };
    }
    if (props.product.is_featured || props.product.is_best_seller) {
        return { text: "Terlaris", class: "bg-gradient-to-r from-amber-500 to-orange-500 text-white" };
    }
    return null;
});

const addToCart = (e) => {
    e.preventDefault();
    e.stopPropagation();
    router.post(
        route("frontend.cart.store"),
        { product_id: props.product.id, quantity: 1 },
        {
            preserveScroll: true,
            onSuccess: (page) => {},
            onError: (errors) => {
                if (errors?.redirect) {
                    window.location.href = errors.redirect;
                }
            },
        },
    );
};

const toggleWishlist = (e) => {
    e.preventDefault();
    e.stopPropagation();
    isWishlisted.value = !isWishlisted.value;
};

const sizeClasses = computed(() => {
    switch (props.size) {
        case "small":
            return "w-40";
        case "large":
            return "w-56";
        default:
            return "";
    }
});
</script>

<template>
    <div
        class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-primary/10"
        :class="sizeClasses"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
    >
        <!-- Image Container -->
        <Link :href="route('frontend.product-detail', product.slug)" class="relative aspect-square overflow-hidden bg-slate-100">
            <!-- Product Image -->
            <img
                v-if="product.thumbnail"
                :src="product.thumbnail"
                :alt="product.name"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-110"
            />

            <!-- No Image Placeholder -->
            <div
                v-else
                class="flex h-full w-full flex-col items-center justify-center gap-2 bg-gradient-to-br from-slate-100 to-slate-200 text-slate-400"
                aria-hidden="true"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-14 w-14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                    />
                </svg>
                <span class="text-xs font-medium">No Image</span>
            </div>

            <!-- Gradient Overlay on Hover -->
            <div
                class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"
            ></div>

            <!-- Badge with Gradient -->
            <div v-if="badge" class="absolute left-3 top-3 px-2.5 py-1 text-xs font-bold shadow-lg" :class="badge.class">
                {{ badge.text }}
            </div>

            <!-- Wishlist Button -->
            <button
                @click="toggleWishlist"
                class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-full bg-white/90 shadow-md backdrop-blur-sm transition-all duration-300 hover:bg-white hover:shadow-lg"
                :class="isWishlisted ? 'text-destructive opacity-100' : 'text-slate-400 opacity-0 group-hover:opacity-100'"
                aria-label="Tambah ke wishlist"
            >
                <Heart class="h-4 w-4" :fill="isWishlisted ? 'currentColor' : 'none'" />
            </button>

            <!-- Sale Price Tag -->
            <div v-if="isSale" class="absolute bottom-3 left-3">
                <div class="rounded-lg bg-white/95 px-2 py-1 text-xs font-bold text-destructive shadow-sm backdrop-blur-sm">
                    Hemat {{ formatCurrency(originalPrice - displayPrice) }}
                </div>
            </div>
        </Link>

        <!-- Content Section -->
        <div class="flex flex-grow flex-col p-4">
            <!-- Category Badge -->
            <div
                class="mb-2 inline-block h-5 min-h-5 self-start rounded-full px-2 py-0.5 text-xs font-semibold"
                :class="product.category_name ? 'bg-primary/10 text-primary' : 'bg-transparent'"
            >
                {{ product.category_name || "" }}
            </div>

            <!-- Product Name -->
            <Link :href="route('frontend.product-detail', product.slug)" class="block">
                <h3 class="mb-2 line-clamp-2 text-sm font-bold leading-tight text-foreground transition-colors group-hover:text-primary sm:text-base">
                    {{ product.name }}
                </h3>
            </Link>

            <!-- Price & Cart Section -->
            <div class="mt-auto flex items-end justify-between gap-2">
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center gap-1.5 rounded-lg bg-primary/5 px-2 py-1">
                        <span class="text-sm font-bold text-primary sm:text-base">
                            {{ formatCurrency(displayPrice) }}
                        </span>
                    </div>
                    <span v-if="originalPrice" class="ml-1.5 text-xs text-slate-400 line-through">
                        {{ formatCurrency(originalPrice) }}
                    </span>
                </div>

                <!-- Add to Cart Button -->
                <button
                    @click="addToCart"
                    class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary/90 text-primary-foreground shadow-lg shadow-primary/30 transition-all duration-300 hover:shadow-xl hover:shadow-primary/40 active:scale-90"
                    aria-label="Tambah ke keranjang"
                >
                    <ShoppingCart class="h-5 w-5" />
                </button>
            </div>
        </div>
    </div>
</template>

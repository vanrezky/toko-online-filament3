<script setup>
import { Link, router } from "@inertiajs/vue3";
import { computed } from "vue";
import { ShoppingCart } from "lucide-vue-next";
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

const badge = computed(() => {
    if (isSale.value) {
        return { text: "Diskon", class: "bg-destructive text-white" };
    }
    if (props.product.is_new) {
        return { text: "Baru", class: "bg-primary text-primary-foreground" };
    }
    if (props.product.is_featured || props.product.is_best_seller) {
        return { text: "Terlaris", class: "bg-destructive text-white" };
    }
    return null;
});

const addToCart = (e) => {
    e.preventDefault();
    e.stopPropagation();

    router.post(
        route("frontend.cart.store"),
        {
            product_id: props.product.id,
            quantity: 1,
        },
        {
            preserveScroll: true,
            onSuccess: (page) => {
                if (page.props?.flash?.success) {
                    // Show success message
                }
            },
            onError: (errors) => {
                if (errors?.redirect) {
                    window.location.href = errors.redirect;
                }
            },
        },
    );
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
        class="group relative flex flex-col overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:-translate-y-1 hover:shadow-lg"
        :class="sizeClasses"
    >
        <!-- Image Container -->
        <Link :href="route('frontend.product-detail', product.slug)" class="relative aspect-square overflow-hidden bg-slate-100">
            <!-- Product Image -->
            <img
                v-if="product.thumbnail"
                :src="product.thumbnail"
                :alt="product.name"
                class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
            />
            <!-- No Image Placeholder -->
            <div v-else class="flex h-full w-full flex-col items-center justify-center gap-2 text-muted-foreground" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z"
                    />
                </svg>
                <span class="text-xs">No Image</span>
            </div>

            <!-- Badge -->
            <div v-if="badge" class="absolute right-3 top-3 px-2 py-1 text-xs font-bold" :class="badge.class">
                {{ badge.text }}
            </div>
        </Link>

        <!-- Content Section -->
        <div class="flex flex-grow flex-col p-4">
            <!-- Category Badge -->
            <div
                class="mb-2 inline-block h-5 min-h-5 self-start rounded-full px-2 py-0.5 text-xs font-bold"
                :class="product.category_name ? 'bg-secondary text-foreground' : 'bg-transparent'"
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
            <div class="mt-auto flex items-end justify-between gap-2 border-t border-border pt-3">
                <div class="min-w-0 flex-1">
                    <span class="block text-[10px] text-muted-foreground">Harga</span>
                    <span class="block text-sm font-bold text-primary sm:text-base">
                        {{ formatCurrency(displayPrice) }}
                    </span>
                    <span v-if="originalPrice" class="block text-[10px] text-muted-foreground line-through">
                        {{ formatCurrency(originalPrice) }}
                    </span>
                </div>

                <!-- Add to Cart Button -->
                <button
                    @click="addToCart"
                    class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground shadow-md transition-all duration-300 hover:bg-accent hover:shadow-lg active:scale-95"
                    aria-label="Tambah ke keranjang"
                >
                    <ShoppingCart class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import ProductCard from "./ProductCard.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: "Semua Produk",
    },
    columns: {
        type: Object,
        default: () => ({ mobile: 2, tablet: 3, desktop: 4 }),
    },
});

const gridClasses = computed(() => {
    const { mobile, tablet, desktop } = props.columns;
    return `grid-cols-${mobile} ${tablet ? `md:grid-cols-${tablet}` : ""} ${desktop ? `lg:grid-cols-${desktop}` : ""}`;
});
</script>

<template>
    <section class="bg-muted/30 py-8 md:py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <h2 class="text-xl font-bold text-foreground md:text-2xl">{{ title }}</h2>
                <span class="text-sm text-muted-foreground">{{ products.length }} produk</span>
            </div>

            <!-- Product Grid -->
            <div class="grid grid-cols-2 gap-4 md:grid-cols-3 md:gap-6 lg:grid-cols-4">
                <ProductCard v-for="product in products" :key="product.uuid || product.id" :product="product" />
            </div>

            <!-- Empty State -->
            <div v-if="products.length === 0" class="py-16 text-center">
                <div class="mb-4 text-6xl">📭</div>
                <h3 class="mb-2 text-lg font-bold text-foreground">Tidak ada produk</h3>
                <p class="mb-4 text-sm text-muted-foreground">Saat ini belum ada produk yang tersedia.</p>
                <Link
                    :href="route('frontend.home')"
                    class="inline-block rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
                >
                    Kembali ke Beranda
                </Link>
            </div>

            <!-- Load More -->
            <div v-if="products.length > 0" class="mt-8 text-center">
                <Link
                    :href="route('frontend.products')"
                    class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-foreground shadow-sm transition-colors hover:bg-primary hover:text-primary-foreground"
                >
                    Lihat Semua Produk
                </Link>
            </div>
        </div>
    </section>
</template>

<script setup>
import { ref } from "vue";
import ProductCard from "./ProductCard.vue";
import { ChevronRight, ChevronLeft } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    products: {
        type: Array,
        default: () => [],
    },
    title: {
        type: String,
        default: "Pilihan Terbaik",
    },
    subtitle: {
        type: String,
        default: "",
    },
    showViewAll: {
        type: Boolean,
        default: true,
    },
});

const scrollContainer = ref(null);

const scroll = (direction) => {
    if (scrollContainer.value) {
        const scrollAmount = 280;
        scrollContainer.value.scrollBy({
            left: direction === "left" ? -scrollAmount : scrollAmount,
            behavior: "smooth",
        });
    }
};
</script>

<template>
    <section class="py-8 md:py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-foreground md:text-2xl">{{ title }}</h2>
                    <p v-if="subtitle" class="mt-1 text-sm text-muted-foreground">{{ subtitle }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        @click="scroll('left')"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                        aria-label="Scroll left"
                    >
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <button
                        @click="scroll('right')"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                        aria-label="Scroll right"
                    >
                        <ChevronRight class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Scrollable Container -->
            <div ref="scrollContainer" class="scrollbar-hidden -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4">
                <div v-for="product in products" :key="product.uuid || product.id" class="w-44 flex-shrink-0 snap-start md:w-52">
                    <ProductCard :product="product" />
                </div>

                <!-- View All Card (if enabled) -->
                <div v-if="showViewAll" class="w-44 flex-shrink-0 snap-start md:w-52">
                    <Link
                        :href="route('frontend.products')"
                        class="group flex h-full min-h-[320px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-secondary transition-colors hover:border-primary md:min-h-[360px]"
                    >
                        <div class="mb-3 text-5xl transition-transform group-hover:scale-110">📦</div>
                        <span class="mb-1 text-sm font-semibold text-foreground">Lihat Semua</span>
                        <span class="text-xs text-muted-foreground">{{ products.length }}+ Produk</span>
                    </Link>
                </div>
            </div>
        </div>
    </section>
</template>

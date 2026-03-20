<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
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
const canScrollLeft = ref(false);
const canScrollRight = ref(true);

const checkScroll = () => {
    if (scrollContainer.value) {
        const { scrollLeft, scrollWidth, clientWidth } = scrollContainer.value;
        canScrollLeft.value = scrollLeft > 0;
        canScrollRight.value = scrollLeft < scrollWidth - clientWidth - 10;
    }
};

const scroll = (direction) => {
    if (scrollContainer.value) {
        const scrollAmount = 280;
        scrollContainer.value.scrollBy({
            left: direction === "left" ? -scrollAmount : scrollAmount,
            behavior: "smooth",
        });
    }
};

onMounted(() => {
    if (scrollContainer.value) {
        scrollContainer.value.addEventListener("scroll", checkScroll);
        checkScroll();
    }
});

onUnmounted(() => {
    if (scrollContainer.value) {
        scrollContainer.value.removeEventListener("scroll", checkScroll);
    }
});
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
                        :disabled="!canScrollLeft"
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 text-foreground shadow-md transition-all duration-300 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-40 hover:disabled:shadow-md"
                        aria-label="Scroll left"
                    >
                        <ChevronLeft class="h-5 w-5" />
                    </button>
                    <button
                        @click="scroll('right')"
                        :disabled="!canScrollRight"
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-primary to-primary/80 text-primary-foreground shadow-lg shadow-primary/20 transition-all duration-300 hover:shadow-xl hover:shadow-primary/30 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                        aria-label="Scroll right"
                    >
                        <ChevronRight class="h-5 w-5" />
                    </button>
                </div>
            </div>

            <!-- Scrollable Container with Fade Edges -->
            <div class="relative">
                <!-- Left Fade -->
                <div
                    class="pointer-events-none absolute left-0 top-0 z-10 h-full w-8 bg-gradient-to-r from-white to-transparent transition-opacity duration-300"
                    :class="canScrollLeft ? 'opacity-100' : 'opacity-0'"
                ></div>

                <!-- Right Fade -->
                <div
                    class="pointer-events-none absolute right-0 top-0 z-10 h-full w-8 bg-gradient-to-l from-white to-transparent transition-opacity duration-300"
                    :class="canScrollRight ? 'opacity-100' : 'opacity-0'"
                ></div>

                <div ref="scrollContainer" class="scrollbar-hidden -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4">
                    <div v-for="product in products" :key="product.uuid || product.id" class="w-44 flex-shrink-0 snap-start md:w-52">
                        <ProductCard :product="product" />
                    </div>

                    <!-- View All Card (if enabled) -->
                    <div v-if="showViewAll" class="w-44 flex-shrink-0 snap-start md:w-52">
                        <Link
                            :href="route('frontend.products')"
                            class="group relative flex h-full min-h-[320px] flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-slate-300 bg-gradient-to-br from-slate-50 to-white transition-all duration-300 hover:border-primary hover:shadow-lg md:min-h-[360px]"
                        >
                            <!-- Decorative Circle -->
                            <div
                                class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-primary/5 transition-transform duration-500 group-hover:scale-150"
                            ></div>
                            <div
                                class="absolute -bottom-8 -left-8 h-32 w-32 rounded-full bg-primary/5 transition-transform duration-500 group-hover:scale-150"
                            ></div>

                            <div class="relative z-10 mb-3 text-5xl transition-transform duration-300 group-hover:scale-110">📦</div>
                            <span class="relative z-10 mb-1 text-sm font-semibold text-foreground">Lihat Semua</span>
                            <span class="relative z-10 text-xs text-muted-foreground">{{ products.length }}+ Produk</span>
                            <div
                                class="relative z-10 mt-3 rounded-full bg-primary/10 px-4 py-1 text-xs font-semibold text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground"
                            >
                                Jelajahi →
                            </div>
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

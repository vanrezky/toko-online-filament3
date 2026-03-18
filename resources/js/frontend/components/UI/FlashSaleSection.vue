<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import ProductCard from "./ProductCard.vue";
import { Link } from "@inertiajs/vue3";
import { Clock, ChevronRight, ChevronLeft } from "lucide-vue-next";

const props = defineProps({
    flashsales: {
        type: Object,
        required: true,
    },
    subtitle: {
        type: String,
        default: "Dapatkan harga spesial dengan periode terbatas",
    },
});

const timeLeft = ref({
    hours: "00",
    minutes: "00",
    seconds: "00",
});

let intervalId = null;

const calculateTimeLeft = () => {
    if (!props.flashsales?.end_time) return;

    const end = new Date(props.flashsales.end_time).getTime();
    const now = new Date().getTime();
    const diff = end - now;

    if (diff > 0) {
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);

        timeLeft.value = {
            hours: String(hours).padStart(2, "0"),
            minutes: String(minutes).padStart(2, "0"),
            seconds: String(seconds).padStart(2, "0"),
        };
    }
};

onMounted(() => {
    calculateTimeLeft();
    intervalId = setInterval(calculateTimeLeft, 1000);
});

onUnmounted(() => {
    if (intervalId) clearInterval(intervalId);
});

const flashSaleProducts = computed(() => {
    if (!props.flashsales?.products) return [];
    return props.flashsales.products.map((item) => item.product);
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
    <section class="bg-gradient-to-r from-destructive/5 via-destructive/10 to-destructive/5 py-8 md:py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6">
                <!-- Desktop Header -->
                <div class="hidden items-center justify-between md:flex">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-[2px] w-6 bg-destructive"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-destructive">Promo Terbatas</span>
                        </div>
                        <h2 class="mt-1 text-xl font-bold text-foreground md:text-2xl">{{ flashsales.name }}</h2>
                        <p class="mt-1 text-sm text-muted-foreground">{{ subtitle }}</p>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Countdown Timer -->
                        <div class="flex items-center gap-2 text-foreground">
                            <Clock class="h-5 w-5" />
                            <span class="text-sm font-semibold">Berakhir dalam:</span>
                            <div class="flex items-center gap-1 font-bold">
                                <span class="rounded bg-destructive px-2 py-1 text-white">{{ timeLeft.hours }}</span>
                                <span class="text-destructive">:</span>
                                <span class="rounded bg-destructive px-2 py-1 text-white">{{ timeLeft.minutes }}</span>
                                <span class="text-destructive">:</span>
                                <span class="rounded bg-destructive px-2 py-1 text-white">{{ timeLeft.seconds }}</span>
                            </div>
                        </div>

                        <!-- Scroll Buttons -->
                        <div class="flex items-center gap-2">
                            <button
                                @click="scroll('left')"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-foreground shadow-md transition-colors hover:bg-primary hover:text-primary-foreground"
                                aria-label="Scroll left"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button
                                @click="scroll('right')"
                                class="flex h-10 w-10 items-center justify-center rounded-full bg-white text-foreground shadow-md transition-colors hover:bg-primary hover:text-primary-foreground"
                                aria-label="Scroll right"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Mobile Header -->
                <div class="flex items-start justify-between gap-4 md:hidden">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="inline-block h-[2px] w-6 bg-destructive"></span>
                            <span class="text-xs font-bold uppercase tracking-widest text-destructive">Promo Terbatas</span>
                        </div>
                        <h2 class="mt-1 text-xl font-bold text-foreground">{{ flashsales.name }}</h2>
                    </div>

                    <!-- Mobile Timer & Scroll Buttons -->
                    <div class="flex flex-col items-end gap-2">
                        <div class="flex items-center gap-2 text-foreground">
                            <Clock class="h-4 w-4 text-destructive" />
                            <div class="flex items-center gap-1 text-sm font-bold">
                                <span class="rounded bg-destructive px-2 py-0.5 text-white">{{ timeLeft.hours }}</span>
                                <span class="text-destructive">:</span>
                                <span class="rounded bg-destructive px-2 py-0.5 text-white">{{ timeLeft.minutes }}</span>
                                <span class="text-destructive">:</span>
                                <span class="rounded bg-destructive px-2 py-0.5 text-white">{{ timeLeft.seconds }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                @click="scroll('left')"
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-foreground shadow-md transition-colors hover:bg-primary hover:text-primary-foreground"
                                aria-label="Scroll left"
                            >
                                <ChevronLeft class="h-4 w-4" />
                            </button>
                            <button
                                @click="scroll('right')"
                                class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-foreground shadow-md transition-colors hover:bg-primary hover:text-primary-foreground"
                                aria-label="Scroll right"
                            >
                                <ChevronRight class="h-4 w-4" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scrollable Container -->
            <div
                ref="scrollContainer"
                class="scrollbar-hidden -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 lg:flex lg:grid-cols-5 lg:gap-4 lg:overflow-visible"
            >
                <div
                    v-for="product in flashSaleProducts"
                    :key="product.uuid || product.id"
                    class="w-44 flex-shrink-0 snap-start md:w-52 lg:flex-shrink-0"
                >
                    <ProductCard :product="product" />
                </div>

                <!-- View All Card (Hidden on Desktop) -->
                <div class="w-44 flex-shrink-0 snap-start md:w-52 lg:hidden">
                    <Link
                        :href="route('frontend.flashsales')"
                        class="group flex h-full min-h-[320px] flex-col items-center justify-center rounded-2xl border-2 border-dashed border-border bg-white transition-colors hover:border-destructive md:min-h-[360px]"
                    >
                        <div class="mb-3 text-5xl transition-transform group-hover:scale-110">🔥</div>
                        <span class="mb-1 text-sm font-semibold text-foreground">Lihat Semua</span>
                        <span class="text-xs text-muted-foreground">{{ flashSaleProducts.length }}+ Promo</span>
                    </Link>
                </div>
            </div>

            <!-- View All Link (Desktop Only) -->
            <div class="mt-8 text-center lg:block">
                <Link
                    :href="route('frontend.flashsales')"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-destructive transition-colors hover:text-destructive/80"
                >
                    Lihat Semua Promo
                    <ChevronRight class="h-4 w-4" />
                </Link>
            </div>
        </div>
    </section>
</template>

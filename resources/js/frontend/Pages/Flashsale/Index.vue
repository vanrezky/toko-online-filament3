<script setup>
import { onMounted, onUnmounted, ref } from "vue";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import ProductCard from "../../components/UI/ProductCard.vue";
import { Clock, ChevronRight, ChevronLeft } from "lucide-vue-next";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    flashSale: Object,
});

const timeLeft = ref({ hours: "00", minutes: "00", seconds: "00" });

const updateCountdown = () => {
    if (!props.flashSale?.end_time) return;

    const now = new Date().getTime();
    const end = new Date(props.flashSale.end_time).getTime();
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
    } else {
        timeLeft.value = { hours: "00", minutes: "00", seconds: "00" };
    }
};

let timer;
onMounted(() => {
    updateCountdown();
    timer = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
    clearInterval(timer);
});

const flashSaleProducts = props.flashSale?.products?.map((item) => item.product) || [];

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
    <TemplateWrapper title="Flash Sale">
        <section class="bg-gradient-to-r from-destructive/5 via-destructive/10 to-destructive/5 py-8 md:py-12">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <div class="mb-4 flex items-center justify-center gap-4">
                        <span class="inline-block h-[2px] w-12 bg-destructive"></span>
                        <span class="text-xs font-bold uppercase tracking-widest text-destructive">Promo Terbatas</span>
                        <span class="inline-block h-[2px] w-12 bg-destructive"></span>
                    </div>
                    <h1 class="mb-3 text-3xl font-bold text-foreground md:text-4xl">{{ flashSale?.name || "Flash Sale" }}</h1>
                    <p v-if="flashSale?.description" class="mx-auto max-w-lg text-sm text-muted-foreground">
                        {{ flashSale.description }}
                    </p>
                </div>

                <!-- Sale Banner & Timer -->
                <div class="mb-8 flex flex-col items-center justify-between gap-6 rounded-2xl bg-destructive p-6 md:flex-row md:p-8">
                    <div class="space-y-1 text-center md:text-left">
                        <h2 class="text-xl font-bold text-white md:text-2xl">{{ flashSale?.name }}</h2>
                        <p v-if="flashSale?.description" class="text-xs text-white/70 md:text-sm">
                            {{ flashSale.description }}
                        </p>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-white/70 md:text-sm">Berakhir dalam:</span>
                        <div class="flex items-center gap-1 font-bold">
                            <div class="flex flex-col items-center">
                                <span class="min-w-[44px] rounded-lg bg-white px-3 py-2 text-center text-lg font-bold text-destructive">{{
                                    timeLeft.hours
                                }}</span>
                                <span class="mt-1 text-[10px] uppercase text-white/70">Jam</span>
                            </div>
                            <span class="mb-4 text-white">:</span>
                            <div class="flex flex-col items-center">
                                <span class="min-w-[44px] rounded-lg bg-white px-3 py-2 text-center text-lg font-bold text-destructive">{{
                                    timeLeft.minutes
                                }}</span>
                                <span class="mt-1 text-[10px] uppercase text-white/70">Min</span>
                            </div>
                            <span class="mb-4 text-white">:</span>
                            <div class="flex flex-col items-center">
                                <span class="min-w-[44px] rounded-lg bg-white px-3 py-2 text-center text-lg font-bold text-destructive">{{
                                    timeLeft.seconds
                                }}</span>
                                <span class="mt-1 text-[10px] uppercase text-white/70">Detik</span>
                            </div>
                        </div>
                    </div>
                </div>

                <template v-if="flashSaleProducts.length > 0">
                    <!-- Scroll Controls -->
                    <div class="mb-6 flex items-center justify-end gap-2">
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

                    <!-- Products Grid -->
                    <div
                        ref="scrollContainer"
                        class="scrollbar-hidden -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 lg:grid lg:snap-none lg:grid-cols-5 lg:gap-4 lg:overflow-visible"
                    >
                        <div
                            v-for="product in flashSaleProducts"
                            :key="product.uuid || product.id"
                            class="w-44 flex-shrink-0 snap-start md:w-52 lg:w-auto lg:flex-shrink lg:snap-start"
                        >
                            <ProductCard :product="product" />
                        </div>
                    </div>
                </template>

                <!-- Empty State -->
                <div v-else class="py-20 text-center">
                    <div class="mb-4 text-6xl">📭</div>
                    <h3 class="mb-2 text-xl font-bold text-foreground">Tidak Ada Flash Sale</h3>
                    <p class="mb-6 text-sm text-muted-foreground">Saat ini tidak ada flash sale yang aktif. Silakan cek kembali nanti.</p>
                    <Link
                        :href="route('frontend.products')"
                        class="inline-block rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
                    >
                        Lihat Koleksi Produk
                    </Link>
                </div>
            </div>
        </section>
    </TemplateWrapper>
</template>

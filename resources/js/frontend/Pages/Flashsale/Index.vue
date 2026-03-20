<script setup>
import { onMounted, onUnmounted, ref, computed } from "vue";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import ProductCard from "../../components/UI/ProductCard.vue";
import { Clock, ChevronRight, ChevronLeft, Zap, Tag, Percent } from "lucide-vue-next";
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

const flashSaleProducts = computed(() => {
    return props.flashSale?.products?.map((item) => item.product) || [];
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

const stats = computed(() => {
    if (!props.flashSale?.products?.length) return { count: 0, avgDiscount: 0 };
    const count = props.flashSale.products.length;
    const avgDiscount = Math.round(props.flashSale.products.reduce((acc, item) => acc + (item.discount_percentage || 0), 0) / count);
    return { count, avgDiscount };
});
</script>

<template>
    <TemplateWrapper title="Flash Sale">
        <section class="relative overflow-hidden bg-gradient-to-br from-destructive/5 via-white to-destructive/10 py-8 md:py-12">
            <!-- Decorative -->
            <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-destructive/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-20 h-96 w-96 rounded-full bg-destructive/5 blur-3xl"></div>
            <div class="absolute right-1/3 top-1/2 h-64 w-64 rounded-full bg-rose-500/5 blur-3xl"></div>

            <div class="container mx-auto px-4">
                <!-- Breadcrumb -->
                <div class="relative z-10 mb-6 flex items-center gap-2 text-sm text-muted-foreground">
                    <Link href="/" class="transition-colors hover:text-foreground">Beranda</Link>
                    <ChevronRight class="h-4 w-4" />
                    <span class="font-medium text-foreground">Flash Sale</span>
                </div>

                <!-- Header -->
                <div class="relative z-10 mb-8">
                    <div class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-destructive/20 to-rose-500/20 px-4 py-2">
                        <Zap class="h-5 w-5 text-destructive" />
                        <span class="text-sm font-bold uppercase tracking-widest text-destructive">Promo Terbatas</span>
                    </div>
                    <h1
                        class="mt-4 bg-gradient-to-r from-foreground to-foreground/70 bg-clip-text text-3xl font-bold text-transparent md:text-4xl lg:text-5xl"
                    >
                        {{ flashSale?.name || "Flash Sale" }}
                    </h1>
                    <p v-if="flashSale?.description" class="mt-2 max-w-xl text-base text-muted-foreground md:text-lg">
                        {{ flashSale.description }}
                    </p>

                    <!-- Stats -->
                    <div class="mt-6 flex flex-wrap items-center gap-4">
                        <div class="flex items-center gap-2 rounded-xl bg-white px-4 py-2 shadow-md">
                            <Tag class="h-5 w-5 text-primary" />
                            <div>
                                <span class="text-lg font-bold text-foreground">{{ stats.count }}</span>
                                <span class="ml-1 text-sm text-muted-foreground">Produk Promo</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 rounded-xl bg-white px-4 py-2 shadow-md">
                            <Percent class="h-5 w-5 text-destructive" />
                            <div>
                                <span class="text-lg font-bold text-destructive">~{{ stats.avgDiscount }}%</span>
                                <span class="ml-1 text-sm text-muted-foreground">Rata-rata Diskon</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sale Banner & Timer -->
                <div
                    class="relative z-10 mb-8 overflow-hidden rounded-3xl bg-gradient-to-r from-destructive to-rose-600 p-6 shadow-2xl shadow-destructive/30 md:p-8"
                >
                    <!-- Animated Decorative -->
                    <div class="absolute -right-16 -top-16 h-64 w-64 rounded-full bg-white/10 blur-2xl"></div>
                    <div class="absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-white/5 blur-2xl"></div>
                    <div class="absolute right-1/4 top-0 h-32 w-32 rounded-full bg-white/10 blur-xl"></div>

                    <div class="relative z-10 flex flex-col items-center justify-between gap-6 lg:flex-row">
                        <div class="flex items-center gap-4 text-center lg:text-left">
                            <div class="hidden h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm lg:flex">
                                <Zap class="h-8 w-8 text-white" />
                            </div>
                            <div class="space-y-1">
                                <h2 class="text-xl font-bold text-white md:text-2xl">{{ flashSale?.name }}</h2>
                                <p v-if="flashSale?.description" class="text-xs text-white/70 md:text-sm lg:max-w-md">
                                    {{ flashSale.description }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="hidden items-center gap-2 sm:flex">
                                <Clock class="h-5 w-5 text-white/70" />
                                <span class="text-sm font-medium text-white/70">Berakhir dalam:</span>
                            </div>
                            <div class="flex items-center gap-2 font-bold">
                                <div class="flex flex-col items-center">
                                    <span
                                        class="flex min-w-[48px] items-center justify-center rounded-xl bg-white px-3 py-2.5 text-xl font-bold text-destructive shadow-xl md:text-2xl"
                                        >{{ timeLeft.hours }}</span
                                    >
                                    <span class="mt-1.5 text-[10px] uppercase tracking-wider text-white/70">Jam</span>
                                </div>
                                <span class="mb-6 text-2xl font-bold text-white/50 md:text-3xl">:</span>
                                <div class="flex flex-col items-center">
                                    <span
                                        class="flex min-w-[48px] items-center justify-center rounded-xl bg-white px-3 py-2.5 text-xl font-bold text-destructive shadow-xl md:text-2xl"
                                        >{{ timeLeft.minutes }}</span
                                    >
                                    <span class="mt-1.5 text-[10px] uppercase tracking-wider text-white/70">Min</span>
                                </div>
                                <span class="mb-6 text-2xl font-bold text-white/50 md:text-3xl">:</span>
                                <div class="flex flex-col items-center">
                                    <span
                                        class="flex min-w-[48px] items-center justify-center rounded-xl bg-white px-3 py-2.5 text-xl font-bold text-destructive shadow-xl md:text-2xl"
                                        >{{ timeLeft.seconds }}</span
                                    >
                                    <span class="mt-1.5 text-[10px] uppercase tracking-wider text-white/70">Detik</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <template v-if="flashSaleProducts.length > 0">
                    <!-- Scroll Controls -->
                    <div class="relative z-10 mb-6 flex items-center justify-between">
                        <p class="text-sm text-muted-foreground">
                            <span class="font-semibold text-foreground">{{ flashSaleProducts.length }}</span> produk promo
                        </p>
                        <div class="flex items-center gap-2">
                            <button
                                @click="scroll('left')"
                                :disabled="!canScrollLeft"
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-foreground shadow-lg transition-all duration-300 hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-40"
                                aria-label="Scroll left"
                            >
                                <ChevronLeft class="h-5 w-5" />
                            </button>
                            <button
                                @click="scroll('right')"
                                :disabled="!canScrollRight"
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-destructive to-rose-600 text-white shadow-lg shadow-destructive/30 transition-all duration-300 hover:shadow-xl hover:shadow-destructive/40 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40"
                                aria-label="Scroll right"
                            >
                                <ChevronRight class="h-5 w-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Products Grid with Fade -->
                    <div class="relative">
                        <!-- Left Fade -->
                        <div
                            class="pointer-events-none absolute left-0 top-0 z-10 h-full w-8 bg-gradient-to-r from-white to-transparent transition-opacity duration-300 md:w-12 lg:hidden"
                            :class="canScrollLeft ? 'opacity-100' : 'opacity-0'"
                        ></div>

                        <!-- Right Fade -->
                        <div
                            class="pointer-events-none absolute right-0 top-0 z-10 h-full w-8 bg-gradient-to-l from-white to-transparent transition-opacity duration-300 md:w-12 lg:hidden"
                            :class="canScrollRight ? 'opacity-100' : 'opacity-0'"
                        ></div>

                        <div
                            ref="scrollContainer"
                            class="scrollbar-hidden -mx-4 flex snap-x snap-mandatory gap-4 overflow-x-auto px-4 pb-4 lg:grid lg:snap-none lg:grid-cols-5 lg:gap-5 lg:overflow-visible"
                        >
                            <div
                                v-for="product in flashSaleProducts"
                                :key="product.uuid || product.id"
                                class="w-44 flex-shrink-0 snap-start md:w-52 lg:w-auto lg:flex-shrink lg:snap-start"
                            >
                                <ProductCard :product="product" />
                            </div>

                            <!-- View All Card -->
                            <div class="w-44 flex-shrink-0 snap-start md:w-52 lg:hidden">
                                <Link
                                    :href="route('frontend.products')"
                                    class="group relative flex h-full min-h-[320px] flex-col items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-destructive/30 bg-gradient-to-br from-destructive/5 to-white transition-all duration-300 hover:border-destructive hover:shadow-xl md:min-h-[360px]"
                                >
                                    <div
                                        class="absolute -right-4 -top-4 h-20 w-20 rounded-full bg-destructive/10 transition-transform duration-500 group-hover:scale-150"
                                    ></div>
                                    <div class="mb-3 text-5xl transition-transform duration-300 group-hover:scale-110">🔥</div>
                                    <span class="mb-1 text-sm font-semibold text-foreground">Lihat Semua</span>
                                    <span class="text-xs text-muted-foreground">{{ flashSaleProducts.length }}+ Promo</span>
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Desktop CTA -->
                    <div class="mt-8 hidden justify-center lg:flex">
                        <Link
                            :href="route('frontend.products')"
                            class="inline-flex items-center gap-2 rounded-2xl bg-gradient-to-r from-primary to-primary/90 px-8 py-4 text-sm font-bold text-primary-foreground shadow-xl shadow-primary/30 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/40"
                        >
                            Lihat Semua Produk
                            <ChevronRight class="h-5 w-5" />
                        </Link>
                    </div>
                </template>

                <!-- Empty State -->
                <div v-else class="relative z-10 overflow-hidden rounded-3xl bg-gradient-to-br from-slate-50 to-slate-100 py-20 text-center">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-destructive/5"></div>
                    <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-destructive/5"></div>

                    <div class="relative z-10">
                        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-white shadow-xl">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 text-slate-300"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-foreground">Tidak Ada Flash Sale</h3>
                        <p class="mx-auto mb-8 max-w-sm text-sm text-muted-foreground">
                            Sepertinya tidak ada flash sale yang aktif saat ini. Yuk, cek kembali nanti!
                        </p>
                        <Link
                            :href="route('frontend.products')"
                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-8 py-3 text-sm font-semibold text-primary-foreground shadow-lg shadow-primary/30 transition-all duration-300 hover:shadow-xl hover:shadow-primary/40"
                        >
                            Lihat Koleksi Produk
                            <ChevronRight class="h-4 w-4" />
                        </Link>
                    </div>
                </div>
            </div>
        </section>
    </TemplateWrapper>
</template>

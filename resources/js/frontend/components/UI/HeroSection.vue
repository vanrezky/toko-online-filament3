<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import { ChevronRight, Sparkles } from "lucide-vue-next";

const props = defineProps({
    title: {
        type: String,
        default: "Diskon Spesial Hari Ini",
    },
    subtitle: {
        type: String,
        default: "Dapatkan penawaran terbaik untuk produk pilihan Anda. Promo terbatas, jangan sampai kehabisan!",
    },
    badge: {
        type: String,
        default: "PROMO SPESIAL",
    },
    imageUrl: {
        type: String,
        default: "",
    },
    overlayColor: {
        type: String,
        default: "#2D1B0E80",
    },
    buttonText: {
        type: String,
        default: "Belanja Sekarang",
    },
    buttonLink: {
        type: String,
        default: "",
    },
    secondaryButtonText: {
        type: String,
        default: "Lihat Flash Sale",
    },
    secondaryButtonLink: {
        type: String,
        default: "",
    },
});

const hasBackgroundImage = computed(() => props.imageUrl && props.imageUrl.length > 0);
const primaryLink = computed(() => props.buttonLink || route("frontend.products"));
const secondaryLink = computed(() => props.secondaryButtonLink || route("frontend.flashsales"));
</script>

<template>
    <section
        class="relative overflow-hidden py-10 md:py-16"
        :class="hasBackgroundImage ? '' : 'bg-gradient-to-br from-primary/5 via-white to-primary/10'"
    >
        <!-- Background Image -->
        <div v-if="hasBackgroundImage" class="absolute inset-0">
            <img :src="imageUrl" :alt="title" class="h-full w-full object-cover" />
            <div class="absolute inset-0" :style="{ backgroundColor: overlayColor }"></div>
        </div>

        <!-- Animated Background Elements (only if no image) -->
        <div v-if="!hasBackgroundImage" class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-20 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute right-1/4 top-1/2 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
        </div>

        <!-- Floating Elements -->
        <div v-if="!hasBackgroundImage" class="absolute right-8 top-1/4 hidden animate-bounce md:block" style="animation-duration: 3s">
            <div class="rounded-2xl bg-white/80 p-3 shadow-lg backdrop-blur-sm">
                <span class="text-3xl">⚡</span>
            </div>
        </div>
        <div v-if="!hasBackgroundImage" class="absolute left-1/4 top-1/3 hidden animate-pulse md:block" style="animation-duration: 2.5s">
            <div class="rounded-xl bg-white/80 p-2 shadow-lg backdrop-blur-sm">
                <span class="text-2xl">🎉</span>
            </div>
        </div>

        <div class="container mx-auto px-4">
            <div class="relative flex items-center justify-between gap-6">
                <!-- Content -->
                <div class="flex-grow md:max-w-2xl" :class="hasBackgroundImage ? 'relative z-10' : ''">
                    <!-- Badge -->
                    <div
                        v-if="!hasBackgroundImage"
                        class="mb-6 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary to-primary/80 px-4 py-2 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/30"
                    >
                        <Sparkles class="h-4 w-4" />
                        <span>{{ badge }}</span>
                    </div>

                    <!-- Heading -->
                    <h1
                        class="mb-4 text-4xl font-bold leading-tight md:text-5xl lg:text-6xl"
                        :class="
                            hasBackgroundImage
                                ? 'text-white'
                                : 'bg-gradient-to-r from-foreground via-foreground to-primary bg-clip-text text-transparent'
                        "
                    >
                        {{ title }}
                    </h1>

                    <!-- Subheading -->
                    <p
                        class="mb-8 max-w-lg text-base leading-relaxed md:text-lg"
                        :class="hasBackgroundImage ? 'text-white/80' : 'text-muted-foreground'"
                    >
                        {{ subtitle }}
                    </p>

                    <!-- CTA Button -->
                    <div class="flex flex-wrap items-center gap-4">
                        <Link
                            :href="primaryLink"
                            class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-8 py-4 text-sm font-bold text-primary-foreground shadow-xl shadow-primary/30 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/40 active:scale-95"
                        >
                            {{ buttonText }}
                            <ChevronRight class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" />
                        </Link>

                        <Link
                            :href="secondaryLink"
                            class="group inline-flex items-center gap-2 rounded-xl border-2 border-primary/30 bg-white/80 px-6 py-4 text-sm font-semibold text-foreground shadow-lg backdrop-blur-sm transition-all duration-300 hover:border-primary hover:bg-white dark:bg-gray-800/80 dark:text-white"
                        >
                            <span>🔥</span>
                            {{ secondaryButtonText }}
                        </Link>
                    </div>

                    <!-- Stats -->
                    <div v-if="!hasBackgroundImage" class="mt-10 flex items-center gap-8">
                        <div>
                            <div class="text-2xl font-bold text-primary">50+</div>
                            <div class="text-xs text-muted-foreground">Produk Promo</div>
                        </div>
                        <div class="h-10 w-px bg-border"></div>
                        <div>
                            <div class="text-2xl font-bold text-primary">70%</div>
                            <div class="text-xs text-muted-foreground">Diskon Terbesar</div>
                        </div>
                        <div class="h-10 w-px bg-border"></div>
                        <div>
                            <div class="text-2xl font-bold text-primary">24/7</div>
                            <div class="text-xs text-muted-foreground">Pelayanan</div>
                        </div>
                    </div>
                </div>

                <!-- Decorative Illustration (only if no background image) -->
                <div v-if="!hasBackgroundImage" class="relative hidden flex-shrink-0 md:block lg:block">
                    <!-- Main Circle -->
                    <div class="relative">
                        <div
                            class="flex h-72 w-72 items-center justify-center rounded-full bg-gradient-to-br from-primary/20 to-primary/5 shadow-2xl"
                        >
                            <span class="text-[120px] leading-none">🛍️</span>
                        </div>

                        <!-- Floating Badges -->
                        <div class="absolute -right-4 top-4 animate-bounce" style="animation-duration: 2s">
                            <div class="rounded-2xl bg-white p-3 shadow-xl">
                                <span class="text-3xl">⚡</span>
                            </div>
                        </div>
                        <div class="absolute -bottom-4 -left-4 animate-pulse" style="animation-duration: 2.5s">
                            <div class="rounded-2xl bg-white p-3 shadow-xl">
                                <span class="text-3xl">🎁</span>
                            </div>
                        </div>
                        <div class="absolute -right-8 bottom-1/3 animate-bounce" style="animation-duration: 3s">
                            <div class="rounded-full bg-gradient-to-br from-amber-400 to-orange-500 p-3 text-white shadow-xl">
                                <span class="text-xl font-bold">70%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

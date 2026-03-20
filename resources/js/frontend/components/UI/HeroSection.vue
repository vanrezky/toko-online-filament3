<script setup>
import { Link } from "@inertiajs/vue3";
import { ChevronRight, Sparkles } from "lucide-vue-next";
</script>

<template>
    <section class="relative overflow-hidden bg-gradient-to-br from-primary/5 via-white to-primary/10 py-10 md:py-16">
        <!-- Animated Background Elements -->
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -right-20 -top-20 h-80 w-80 rounded-full bg-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -left-20 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute right-1/4 top-1/2 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
        </div>

        <!-- Floating Elements -->
        <div class="absolute right-8 top-1/4 hidden animate-bounce md:block" style="animation-duration: 3s">
            <div class="rounded-2xl bg-white/80 p-3 shadow-lg backdrop-blur-sm">
                <span class="text-3xl">⚡</span>
            </div>
        </div>
        <div class="absolute left-1/4 top-1/3 hidden animate-pulse md:block" style="animation-duration: 2.5s">
            <div class="rounded-xl bg-white/80 p-2 shadow-lg backdrop-blur-sm">
                <span class="text-2xl">🎉</span>
            </div>
        </div>

        <div class="container mx-auto px-4">
            <div class="relative flex items-center justify-between gap-6">
                <!-- Content -->
                <div class="flex-grow md:max-w-2xl">
                    <!-- Badge -->
                    <div
                        class="mb-6 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary to-primary/80 px-4 py-2 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/30"
                    >
                        <Sparkles class="h-4 w-4" />
                        <span>PROMO SPESIAL</span>
                    </div>

                    <!-- Heading -->
                    <h1
                        class="mb-4 bg-gradient-to-r from-foreground via-foreground to-primary bg-clip-text text-4xl font-bold leading-tight text-transparent md:text-5xl lg:text-6xl"
                    >
                        Diskon Spesial Hari Ini
                    </h1>

                    <!-- Subheading -->
                    <p class="mb-8 max-w-lg text-base leading-relaxed text-muted-foreground md:text-lg">
                        Dapatkan penawaran terbaik untuk produk pilihan Anda. Promo terbatas, jangan sampai kehabisan!
                    </p>

                    <!-- CTA Button -->
                    <div class="flex flex-wrap items-center gap-4">
                        <Link
                            :href="route('frontend.products')"
                            class="group inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-8 py-4 text-sm font-bold text-primary-foreground shadow-xl shadow-primary/30 transition-all duration-300 hover:shadow-2xl hover:shadow-primary/40 active:scale-95"
                        >
                            Belanja Sekarang
                            <ChevronRight class="h-5 w-5 transition-transform duration-300 group-hover:translate-x-1" />
                        </Link>

                        <Link
                            :href="route('frontend.flashsales')"
                            class="group inline-flex items-center gap-2 rounded-xl border-2 border-primary/30 bg-white/80 px-6 py-4 text-sm font-semibold text-foreground shadow-lg backdrop-blur-sm transition-all duration-300 hover:border-primary hover:bg-white"
                        >
                            <span>🔥</span>
                            Lihat Flash Sale
                        </Link>
                    </div>

                    <!-- Stats -->
                    <div class="mt-10 flex items-center gap-8">
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

                <!-- Decorative Illustration -->
                <div class="relative hidden flex-shrink-0 md:block lg:block">
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

<script setup>
import { Link } from "@inertiajs/vue3";
import { ChevronRight } from "lucide-vue-next";
</script>

<template>
    <section class="relative overflow-hidden bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5">
        <div class="container mx-auto px-4 py-10 md:py-16">
            <div class="flex items-center justify-between gap-6">
                <!-- Content -->
                <div class="flex-grow md:max-w-xl">
                    <div class="mb-4 flex items-center gap-3">
                        <span class="text-4xl md:text-5xl">🛍️</span>
                        <span class="inline-block rounded-full bg-primary px-3 py-1 text-xs font-bold text-primary-foreground"> PROMO </span>
                    </div>
                    <h1 class="mb-3 text-3xl font-bold leading-tight text-foreground md:text-4xl lg:text-5xl">Diskon Spesial</h1>
                    <p class="mb-6 text-base text-muted-foreground md:text-lg">Dapatkan penawaran terbaik untuk produk pilihan Anda</p>
                    <Link
                        :href="route('frontend.home')"
                        class="inline-flex items-center gap-2 rounded-full bg-primary px-8 py-4 text-sm font-semibold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg"
                    >
                        Belanja Sekarang
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                </div>

                <!-- Decorative -->
                <div class="hidden flex-shrink-0 md:block">
                    <div class="flex h-56 w-56 items-center justify-center rounded-full bg-secondary text-9xl opacity-80">🛒</div>
                </div>
            </div>
        </div>
    </section>
</template>

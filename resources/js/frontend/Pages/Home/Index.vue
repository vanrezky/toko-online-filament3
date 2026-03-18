<script setup>
import { computed, ref, watch } from "vue";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import HeroSection from "../../components/UI/HeroSection.vue";
import FeaturedProducts from "../../components/UI/FeaturedProducts.vue";
import FlashSaleSection from "../../components/UI/FlashSaleSection.vue";
import ProductCard from "../../components/UI/ProductCard.vue";
import CategoryMenu from "../../components/UI/CategoryMenu.vue";
import { Link } from "@inertiajs/vue3";
import { ChevronRight } from "lucide-vue-next";

const props = defineProps({
    flashsales: Object,
    products: Object,
    categories: Array,
    filters: Object,
});

const allProducts = ref([...(props.products?.data || [])]);

// Featured products (first 4 from products list)
const featuredProducts = computed(() => {
    return allProducts.value.slice(0, 4);
});

// Watch for product changes
watch(
    () => props.products?.data,
    (newData) => {
        allProducts.value = [...newData];
    },
);
</script>

<template>
    <TemplateWrapper title="Home">
        <!-- Hero Section -->
        <HeroSection />

        <!-- Category Menu -->
        <CategoryMenu :categories="categories" :active-category="selectedCategory" />

        <!-- Pilihan Terbaik (Featured Products) -->
        <FeaturedProducts
            v-if="featuredProducts.length > 0 && !filters?.category"
            :products="featuredProducts"
            title="Pilihan Terbaik"
            subtitle="Produk paling diminati pelanggan kami"
        />

        <!-- Flash Sale Section -->
        <FlashSaleSection v-if="flashsales" :flashsales="flashsales" />

        <!-- All Products Section -->
        <section class="py-8 md:py-12">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-foreground md:text-2xl">
                            {{ filters?.category ? `${filters.category}` : "Semua Produk" }}
                        </h2>
                        <p v-if="!filters?.category" class="mt-1 text-sm text-muted-foreground">Jelajahi koleksi lengkap produk kami</p>
                    </div>
                </div>

                <!-- Product Grid -->
                <div v-if="allProducts.length > 0" class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
                    <ProductCard v-for="product in allProducts" :key="product.uuid || product.id" :product="product" />
                </div>

                <!-- Empty State -->
                <div v-else class="py-20 text-center">
                    <div class="mb-4 text-6xl">📭</div>
                    <h3 class="mb-2 text-xl font-bold text-foreground">Produk tidak ditemukan</h3>
                    <p class="mb-6 text-sm text-muted-foreground">Saat ini belum ada produk yang tersedia.</p>
                    <Link
                        :href="route('frontend.products')"
                        class="inline-block rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
                    >
                        Lihat Semua Produk
                    </Link>
                </div>

                <!-- View More -->
                <div v-if="allProducts.length > 0" class="flex justify-center pt-12">
                    <Link
                        :href="route('frontend.products')"
                        class="inline-flex items-center gap-2 rounded-full bg-secondary px-8 py-3 text-sm font-semibold text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                    >
                        Lihat Lebih Banyak
                        <ChevronRight class="h-4 w-4" />
                    </Link>
                </div>
            </div>
        </section>

        <!-- Newsletter Section -->
        <section class="bg-primary/5 py-12">
            <div class="container mx-auto px-4 text-center">
                <div class="mx-auto max-w-md space-y-4">
                    <h2 class="text-2xl font-bold text-foreground md:text-3xl">Dapatkan Penawaran Spesial</h2>
                    <p class="text-sm text-muted-foreground">Daftar newsletter untuk mendapatkan informasi tentang produk baru dan promo menarik.</p>
                    <form class="flex flex-col gap-3 sm:flex-row" @submit.prevent>
                        <input
                            type="email"
                            placeholder="Masukkan email Anda"
                            class="flex-grow rounded-full border border-border bg-white px-5 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"
                        />
                        <button
                            type="submit"
                            class="rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-md transition-colors hover:bg-primary/90"
                        >
                            Berlangganan
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </TemplateWrapper>
</template>

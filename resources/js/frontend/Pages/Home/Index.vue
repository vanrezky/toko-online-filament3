<script setup>
import { computed, ref, watch } from "vue";
import { usePage } from "@inertiajs/vue3";
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
    template: Object,
});

const page = usePage();
const colorScheme = computed(() => page.props.colorScheme);

const allProducts = ref([...(props.products?.data || [])]);

const featuredProducts = computed(() => {
    return allProducts.value.slice(0, 4);
});

watch(
    () => props.products?.data,
    (newData) => {
        allProducts.value = [...newData];
    },
);

const getSectionContent = (sectionType, key, defaultValue = "") => {
    if (!props.template?.sections) return defaultValue;
    const section = props.template.sections.find((s) => s.type === sectionType);
    return section?.contents?.[key] || defaultValue;
};

const newsletterTitle = computed(() => getSectionContent("newsletter", "title", "Dapatkan Penawaran Spesial"));
const newsletterSubtitle = computed(() =>
    getSectionContent("newsletter", "subtitle", "Daftar newsletter untuk mendapatkan informasi tentang produk baru dan promo menarik."),
);
const newsletterButtonText = computed(() => getSectionContent("newsletter", "button_text", "Berlangganan"));

const heroTitle = computed(() => getSectionContent("hero", "title", ""));
const heroSubtitle = computed(() => getSectionContent("hero", "subtitle", ""));
const heroImage = computed(() => getSectionContent("hero", "image_url", ""));
const heroOverlay = computed(() => getSectionContent("hero", "overlay_color", "#2D1B0E80"));
const heroButtonText = computed(() => getSectionContent("hero", "button_text", ""));
const heroButtonLink = computed(() => getSectionContent("hero", "button_link", ""));
const heroBadge = computed(() => getSectionContent("hero", "badge", ""));

const featuredTitle = computed(() => getSectionContent("featured_products", "title", "Pilihan Terbaik"));
const featuredSubtitle = computed(() => getSectionContent("featured_products", "subtitle", "Produk paling diminati pelanggan kami"));

const allProductsTitle = computed(() => {
    if (props.filters?.category) return props.filters.category;
    return getSectionContent("products_grid", "title", "Semua Produk");
});
const allProductsSubtitle = computed(() => getSectionContent("products_grid", "subtitle", "Jelajahi koleksi lengkap produk kami"));

const flashSaleTitle = computed(() => getSectionContent("flash_sale", "title", ""));
const flashSaleSubtitle = computed(() => getSectionContent("flash_sale", "subtitle", "Dapatkan harga spesial dengan periode terbatas"));
</script>

<template>
    <TemplateWrapper title="Home" :color-scheme="colorScheme">
        <!-- Hero Section -->
        <HeroSection
            :title="heroTitle || 'Diskon Spesial Hari Ini'"
            :subtitle="heroSubtitle || 'Dapatkan penawaran terbaik untuk produk pilihan Anda. Promo terbatas, jangan sampai kehabisan!'"
            :badge="heroBadge"
            :image-url="heroImage"
            :overlay-color="heroOverlay"
            :button-text="heroButtonText"
            :button-link="heroButtonLink"
        />

        <!-- Category Menu -->
        <CategoryMenu :categories="categories" />

        <!-- Pilihan Terbaik (Featured Products) -->
        <FeaturedProducts
            v-if="featuredProducts.length > 0 && !filters?.category"
            :products="featuredProducts"
            :title="featuredTitle"
            :subtitle="featuredSubtitle"
        />

        <!-- Flash Sale Section -->
        <FlashSaleSection v-if="flashsales" :flashsales="flashsales" :title="flashSaleTitle" :subtitle="flashSaleSubtitle" />

        <!-- All Products Section -->
        <section class="py-8 md:py-12">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-foreground md:text-2xl">
                            {{ allProductsTitle }}
                        </h2>
                        <p v-if="!filters?.category" class="mt-1 text-sm text-muted-foreground">{{ allProductsSubtitle }}</p>
                    </div>
                </div>

                <!-- Product Grid -->
                <div v-if="allProducts.length > 0" class="grid grid-cols-2 gap-4 md:grid-cols-4 lg:grid-cols-5">
                    <ProductCard v-for="product in allProducts" :key="product.uuid || product.id" :product="product" />
                </div>

                <!-- Empty State -->
                <div v-else class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-50 to-slate-100 py-20 text-center">
                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-primary/5"></div>
                    <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-primary/5"></div>

                    <div class="relative z-10">
                        <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-white shadow-lg">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-12 w-12 text-slate-300"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="1.5"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m6 4.125 2.25 2.25m0 0 2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z"
                                />
                            </svg>
                        </div>
                        <h3 class="mb-2 text-xl font-bold text-foreground">Belum Ada Produk</h3>
                        <p class="mx-auto mb-8 max-w-sm text-sm text-muted-foreground">
                            Sepertinya belum ada produk yang tersedia saat ini. Yuk, cek kembali nanti!
                        </p>
                        <Link
                            :href="route('frontend.home')"
                            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-primary to-primary/90 px-8 py-3 text-sm font-semibold text-primary-foreground shadow-lg shadow-primary/30 transition-all duration-300 hover:shadow-xl hover:shadow-primary/40"
                        >
                            Kembali ke Beranda
                            <ChevronRight class="h-4 w-4" />
                        </Link>
                    </div>
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
        <section class="relative overflow-hidden bg-gradient-to-r from-primary/5 via-primary/10 to-primary/5 py-12 md:py-16">
            <div class="absolute -left-20 -top-20 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>
            <div class="absolute -bottom-20 -right-20 h-64 w-64 rounded-full bg-primary/10 blur-3xl"></div>

            <div class="container mx-auto px-4">
                <div class="relative z-10 mx-auto max-w-lg text-center">
                    <div
                        class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-primary to-primary/80 shadow-lg shadow-primary/30"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-8 w-8 text-primary-foreground"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"
                            />
                        </svg>
                    </div>

                    <h2 class="mb-3 text-2xl font-bold text-foreground md:text-3xl">{{ newsletterTitle }}</h2>
                    <p class="mb-8 text-sm text-muted-foreground">
                        {{ newsletterSubtitle }}
                    </p>

                    <form class="flex flex-col gap-3 sm:flex-row" @submit.prevent>
                        <input
                            type="email"
                            placeholder="Masukkan email Anda"
                            class="flex-grow rounded-xl border border-white/50 bg-white px-5 py-3.5 text-sm shadow-lg focus:outline-none focus:ring-2 focus:ring-primary/30"
                        />
                        <button
                            type="submit"
                            class="rounded-xl bg-gradient-to-r from-primary to-primary/90 px-8 py-3.5 text-sm font-semibold text-primary-foreground shadow-lg shadow-primary/30 transition-all duration-300 hover:shadow-xl hover:shadow-primary/40 active:scale-95"
                        >
                            {{ newsletterButtonText }}
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </TemplateWrapper>
</template>

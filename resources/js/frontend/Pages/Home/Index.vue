<script setup>
import Layout from "../../Layouts/Layout.vue";
import Autoplay from "embla-carousel-autoplay";
import Carousel from "@frontend/Components/ui/carousel/Carousel.vue";
import CarouselContent from "@frontend/Components/ui/carousel/CarouselContent.vue";
import CarouselPrevious from "@frontend/Components/ui/carousel/CarouselPrevious.vue";
import CarouselNext from "@frontend/Components/ui/carousel/CarouselNext.vue";
import CarouselItem from "@frontend/Components/ui/carousel/CarouselItem.vue";
import { computed, ref, watchEffect } from "vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
import Product from "@frontend/Components/Product.vue";
const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
    flashsales: {
        type: Object,
        default: () => ({}),
    },
    featuredCategories: {
        type: Object,
        default: () => ({}),
    },
    sliders: {
        type: Object,
        default: () => ({}),
    },
});

const currentIndex = ref(0);
const itemsPerPage = ref(6);

// Mengubah itemsPerPage berdasarkan ukuran layar
const updateItemsPerPage = () => {
    itemsPerPage.value = window.innerWidth <= 640 ? 4 : 6; // 640px adalah breakpoint untuk mobile
};

// Pantau perubahan ukuran layar
watchEffect(() => {
    updateItemsPerPage();
    window.addEventListener("resize", updateItemsPerPage);
    return () => window.removeEventListener("resize", updateItemsPerPage);
});

const productsFlashsales = computed(() => props.flashsales.products);

const visibleProducts = computed(() => {
    return productsFlashsales.value.slice(currentIndex.value, currentIndex.value + itemsPerPage.value);
});

const next = () => {
    if (currentIndex.value + itemsPerPage.value < productsFlashsales.value.length) {
        currentIndex.value += itemsPerPage.value;
    }
};

const prev = () => {
    if (currentIndex.value - itemsPerPage.value >= 0) {
        currentIndex.value -= itemsPerPage.value;
    }
};
</script>
<template>
    <Layout>
        <div v-if="sliders.length" class="relative mx-auto w-full overflow-hidden rounded-2xl shadow-lg">
            <Carousel
                class="h-[170px] w-full md:h-[280px] lg:h-[480px]"
                :plugins="[
                    Autoplay({
                        delay: 3000,
                        pauseOnHover: true,
                    }),
                ]"
            >
                <CarouselContent>
                    <CarouselItem v-for="(slider, index) in sliders" :key="index">
                        <div class="flex h-full w-full items-center justify-center">
                            <img :src="slider.image_url" :alt="`slider ${index}`" class="h-full w-full rounded-2xl object-cover" />
                        </div>
                    </CarouselItem>
                </CarouselContent>
                <CarouselPrevious class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white p-2 shadow" />
                <CarouselNext class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white p-2 shadow" />
            </Carousel>
        </div>
        <div class="scrollbar-hidden group overflow-x-auto py-5 pt-10">
            <div class="grid min-w-full auto-cols-max grid-flow-col gap-4">
                <a
                    href="#"
                    class="hover:scrollbar-visible transform flex min-w-[320px] items-start justify-between rounded-xl border p-4 transition duration-300 hover:scale-105 hover:shadow-lg"
                    v-for="(category, index) in featuredCategories"
                    :key="index"
                >
                    <div class="flex items-center gap-5">
                        <div class="rounded-xl bg-secondary p-3">
                            <img :src="category.image_url" :alt="category.name" class="w-7" />
                        </div>
                        <div>
                            <h4 class="font-semibold uppercase tracking-wider">{{ category.name }}</h4>
                            <p class="text-sm text-gray-500">Show All</p>
                        </div>
                    </div>
                    <p class="text-xs font-light text-gray-500">({{ category.products_count }})</p>
                </a>
            </div>
        </div>
        <div class="mx-auto w-full py-5">
            <div class="relative col-span-3 w-full">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-xl font-semibold tracking-wider">Flash Sales</h2>
                    <div class="flex gap-2">
                        <Button variant="ghost" @click="prev" class="p-2" :disabled="currentIndex === 0">
                            <ChevronLeft class="h-5 w-5" />
                        </Button>
                        <Button variant="ghost" @click="next" class="p-2" :disabled="currentIndex + itemsPerPage >= products.length">
                            <ChevronRight class="h-5 w-5"
                        /></Button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6">
                    <div v-for="product in visibleProducts" :key="product.id">
                        <Product :product="product.product" />
                    </div>
                </div>
            </div>
        </div>
    </Layout>
</template>

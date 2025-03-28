<script setup>
import Layout from "../../Layouts/Layout.vue";
import Autoplay from "embla-carousel-autoplay";
import Carousel from "@frontend/Components/ui/carousel/Carousel.vue";
import CarouselContent from "@frontend/Components/ui/carousel/CarouselContent.vue";
import CarouselPrevious from "@frontend/Components/ui/carousel/CarouselPrevious.vue";
import CarouselNext from "@frontend/Components/ui/carousel/CarouselNext.vue";
import CarouselItem from "@frontend/Components/ui/carousel/CarouselItem.vue";
import { ref } from "vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
// import { Card, CardContent } from "@frontend/Component";
const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    },
    sliders: {
        type: Object,
        default: () => ({}),
    },
});
const products = ref([
    { name: "Product 1", price: "Rp 100.000", image: "https://via.placeholder.com/150" },
    { name: "Product 2", price: "Rp 150.000", image: "https://via.placeholder.com/150" },
    { name: "Product 3", price: "Rp 200.000", image: "https://via.placeholder.com/150" },
    { name: "Product 4", price: "Rp 250.000", image: "https://via.placeholder.com/150" },
    { name: "Product 5", price: "Rp 300.000", image: "https://via.placeholder.com/150" },
    { name: "Product 6", price: "Rp 350.000", image: "https://via.placeholder.com/150" },
]);

const carouselRef = ref(null);

const nextSlide = () => {
    if (carouselRef.value) carouselRef.value.next();
};

const prevSlide = () => {
    if (carouselRef.value) carouselRef.value.prev();
};
</script>
<template>
    <Layout>
        <div v-if="sliders.length" class="relative mx-auto w-full overflow-hidden rounded-2xl shadow-lg">
            <Carousel
                class="h-[400px] w-full"
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
        <div class="mx-auto w-full max-w-6xl p-4">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-bold">Flash Sale</h2>
                <div class="flex gap-2">
                    <Button variant="ghost" @click="prevSlide" class="p-2"><ChevronLeft class="h-5 w-5" /></Button>
                    <Button variant="ghost" @click="nextSlide" class="p-2"><ChevronRight class="h-5 w-5" /></Button>
                </div>
            </div>
            <Carousel ref="carouselRef" class="relative">
                <CarouselContent class="flex space-x-4">
                    <CarouselItem v-for="(product, index) in products" :key="index" class="w-1/2 md:w-1/4 lg:w-1/6">
                        <Card>
                            <CardContent class="p-3">
                                <img :src="product.image" :alt="product.name" class="h-32 w-full rounded-md object-cover" />
                                <h3 class="mt-2 text-sm font-semibold">{{ product.name }}</h3>
                                <p class="font-bold text-red-500">{{ product.price }}</p>
                                <Button class="mt-2 w-full rounded-md bg-red-500 py-1 text-white hover:bg-red-600">Beli Sekarang</Button>
                            </CardContent>
                        </Card>
                    </CarouselItem>
                </CarouselContent>
            </Carousel>
        </div>
    </Layout>
</template>

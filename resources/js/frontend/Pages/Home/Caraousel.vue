<script setup>
import Autoplay from "embla-carousel-autoplay";
import Carousel from "@frontend/Components/ui/carousel/Carousel.vue";
import CarouselContent from "@frontend/Components/ui/carousel/CarouselContent.vue";
import CarouselPrevious from "@frontend/Components/ui/carousel/CarouselPrevious.vue";
import CarouselNext from "@frontend/Components/ui/carousel/CarouselNext.vue";
import CarouselItem from "@frontend/Components/ui/carousel/CarouselItem.vue";

const props = defineProps({
    sliders: {
        required: true,
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <div v-if="sliders.length" class="relative mx-auto w-full overflow-hidden rounded-lg shadow lg:rounded-2xl">
        <Carousel
            class="h-[160px] w-full md:h-[280px] lg:h-[480px]"
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
                        <img :src="slider.image_url" :alt="`slider ${index}`" class="h-full w-full rounded-lg object-cover lg:rounded-2xl" />
                    </div>
                </CarouselItem>
            </CarouselContent>
            <CarouselPrevious class="absolute left-2 top-1/2 -translate-y-1/2 rounded-full bg-white p-2 shadow" />
            <CarouselNext class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full bg-white p-2 shadow" />
        </Carousel>
    </div>
</template>

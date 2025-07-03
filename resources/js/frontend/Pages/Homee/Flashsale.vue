<script setup>
import Product from "@frontend/Components/Product.vue";
import Button from "@frontend/Components/ui/button/Button.vue";
import { ChevronLeft, ChevronRight } from "lucide-vue-next";
import { computed, ref, watchEffect } from "vue";

const props = defineProps({
    flashsales: {
        required: true,
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
        currentIndex.value += 1;
    }
};

const prev = () => {
    if (currentIndex.value >= 0) {
        currentIndex.value -= 1;
    }
};
</script>
<template>
    <div class="mx-auto w-full py-2 lg:py-5">
        <div class="relative col-span-3 w-full">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-semibold tracking-wider">Flash Sales</h2>
                <div class="flex gap-2">
                    <Button variant="ghost" @click="prev" class="p-2" :disabled="currentIndex === 0">
                        <ChevronLeft class="h-5 w-5" />
                    </Button>
                    <Button variant="ghost" @click="next" class="p-2" :disabled="currentIndex + itemsPerPage >= productsFlashsales.length">
                        <ChevronRight class="h-5 w-5"
                    /></Button>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-6 lg:gap-4">
                <div v-for="product in visibleProducts" :key="product.id">
                    <Product :product="product.product" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Heart } from "lucide-vue-next";
import Card from "./ui/card/Card.vue";
import CardContent from "./ui/card/CardContent.vue";
import CardFooter from "./ui/card/CardFooter.vue";

const props = defineProps({
    product: {
        required: true,
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <a href="#">
        <Card class="rounded-lg border shadow-md">
            <CardContent class="min-h-64 p-0 lg:min-h-80">
                <img :src="product.thumbnail" :alt="product.name" class="w-full rounded-md rounded-b-none" />
                <div class="px-2">
                    <p class="mt-2 break-words text-left text-sm leading-snug text-primary">{{ product.name }}</p>
                    <h3 class="p-0 text-lg font-semibold leading-snug">{{ product.price }}</h3>
                    <span class="text-sm italic line-through" v-if="product.sale_price">{{ product.sale_price }}</span>
                </div>
            </CardContent>
            <CardFooter class="mb-0 flex justify-end px-2 py-1">
                <a href="#" class="hover:text-red-500"><Heart class="size-5" /></a>
            </CardFooter>
        </Card>
    </a>
</template>

<script setup>
import { Link } from "@inertiajs/vue3";
import Card from "./ui/card/Card.vue";
import CardContent from "./ui/card/CardContent.vue";

const props = defineProps({
    product: {
        required: true,
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <Link :href="product.slug">
        <Card class="transform overflow-hidden rounded-lg border p-1 shadow-md transition duration-300 hover:scale-105 hover:shadow-lg">
            <CardContent class="min-h-64 p-0 lg:min-h-80">
                <img :src="product.thumbnail" :alt="product.name" class="w-full rounded-md rounded-b-none" draggable="false" />
                <div class="px-2">
                    <p class="mt-2 break-words text-left text-sm leading-snug text-primary">{{ product.name }}</p>
                    <h3 class="p-0 text-lg font-semibold leading-snug">{{ product.price }}</h3>
                    <span class="text-sm italic line-through" v-if="product.sale_price">{{ product.sale_price }}</span>
                </div>
            </CardContent>
        </Card>
    </Link>
</template>

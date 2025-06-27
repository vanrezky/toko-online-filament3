<script setup>
import Button from "@frontend/Components/ui/button/Button.vue";
import Card from "@frontend/Components/ui/card/Card.vue";
import CardContent from "@frontend/Components/ui/card/CardContent.vue";
import CardHeader from "@frontend/Components/ui/card/CardHeader.vue";
import CardTitle from "@frontend/Components/ui/card/CardTitle.vue";
import Layout from "@frontend/Layouts/Layout.vue";
import { ShoppingCart, Warehouse } from "lucide-vue-next";

const props = defineProps({
    product: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <Layout :title="product.name">
        <div class="space-y-5">
            <section class="grid grid-cols-1 items-start gap-4 md:grid-cols-2">
                <div>
                    
                </div>
                <div class="space-y-4">
                    <!-- title -->
                    <h3 class="text-start text-xl font-semibold tracking-normal">{{ product.name }}</h3>
                    <!-- price -->
                    <div class="flex items-center gap-2 rounded-sm bg-gray-100/50 p-4">
                        <p class="text-2xl font-semibold tracking-wide">{{ product.price }}</p>
                        <p class="text-sm italic tracking-wide text-black line-through" v-if="product.sale_price">{{ product.sale_price }}</p>
                    </div>
                    <!-- warehouse -->
                    <div class="flex items-center gap-2">
                        <Warehouse class="size-5" />
                        <span class="font-thin leading-relaxed tracking-normal">{{
                            `${product.warehouse.sub_district}, ${product.warehouse.district}`
                        }}</span>
                    </div>

                    <!-- buttons -->
                    <div class="flex gap-2">
                        <Button class="">
                            <ShoppingCart />
                            <span>Add to Cart</span>
                        </Button>
                        <Button class="bg-primary">Buy Now</Button>
                    </div>
                </div>
            </section>
            <section>
                <Card>
                    <CardHeader>
                        <CardTitle class="text-lg">Description</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div v-html="product.description" class="text-sm leading-relaxed"></div>
                    </CardContent>
                </Card>
            </section>
        </div>
    </Layout>
</template>

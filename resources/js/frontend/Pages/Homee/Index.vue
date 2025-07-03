<script setup>
import Layout from "../../Layouts/Layout.vue";
import Caraousel from "./Caraousel.vue";
import FeaturedCategory from "./FeaturedCategory.vue";
import Flashsale from "./Flashsale.vue";
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
</script>
<template>
    <Layout>
        <Caraousel :sliders="sliders" />
        <FeaturedCategory :featuredCategories="featuredCategories" />
        <Flashsale :flashsales="flashsales" />
    </Layout>
</template>

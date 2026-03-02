<script setup>
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";

defineProps({
    title: String,
    description: String,
    keywords: String,
});

const isProductionEnv = import.meta.env.PROD;
const settings = computed(() => usePage().props.settings);
</script>

<template>
    <Head :title="title ? `${title} - ${settings.site_name}` : settings.site_name">
        <link v-if="settings.favicon" rel="shortcut icon" type="image/x-icon" :href="settings.favicon" />
        
        <meta v-if="description" name="description" :content="description" />
        <meta v-if="keywords" name="keywords" :content="keywords" />

        <meta v-if="isProductionEnv" http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />

        <slot />
    </Head>
</template>

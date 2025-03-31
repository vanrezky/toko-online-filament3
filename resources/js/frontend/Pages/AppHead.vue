<script setup>
import { computed } from "vue";
import { Head, usePage } from "@inertiajs/vue3";

defineProps({
    title: String,
});

const isProductionEnv = import.meta.env.PROD;
const settings = computed(() => usePage().props.settings);

console.log(settings);
</script>

<template>
    <Head :title="title ? `${title} - ${settings.site_name}` : settings.site_name">
        <link v-if="settings.favicon" rel="shortcut icon" type="image/x-icon" :href="settings.favicon" />

        <meta v-if="isProductionEnv" http-equiv="Content-Security-Policy" content="upgrade-insecure-requests" />

        <slot />
    </Head>
</template>

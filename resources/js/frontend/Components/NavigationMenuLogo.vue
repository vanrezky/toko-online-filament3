<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const setting = computed(() => usePage().props.settings);
</script>

<template>
    <div class="flex items-center justify-between">
        <Link class="flex flex-none items-center text-2xl font-semibold text-black" :aria-label="setting.site_name" href="/">
            <img v-if="setting.logo" :src="setting.logo" :alt="setting.site_name" class="h-auto max-w-16 lg:max-w-16" />
            <span v-else>{{ setting.site_name }}</span>
        </Link>
    </div>
</template>

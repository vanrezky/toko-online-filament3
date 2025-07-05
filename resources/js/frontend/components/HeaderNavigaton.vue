<script setup>
import { Link } from "@inertiajs/vue3";
import { ArrowLeft } from "lucide-vue-next";

defineProps({
    title: {
        type: String,
        default: () => "",
    },
    backwardIcon: {
        type: String,
        default: () => null,
    },
    backwardUrl: {
        type: String,
        default: () => "",
    },
});
</script>

<template>
    <div class="flex items-center justify-between bg-white p-4 pb-2">
        <Link class="flex size-12 shrink-0 items-center text-[#181111]" data-icon="X" data-size="24px" data-weight="regular" :href="backwardUrl">
            <ArrowLeft />
        </Link>
        <h2 class="flex-1 pr-12 text-center text-lg font-bold leading-tight tracking-[-0.015em] text-[#181111]">{{ title }}</h2>
    </div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    class: {
        type: String,
        default: "",
    },
});

const componentClass = computed(() => {
    return `mx-auto w-full max-w-sm px-2 text-black md:max-w-[960px] lg:max-w-[1350px] ${props.class}`.trim();
});
</script>
<template>
    <div :class="componentClass">
        <slot />
    </div>
</template>

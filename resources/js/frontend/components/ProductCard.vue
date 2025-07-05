<template>
    <div :class="componentClass">
        <div :class="componentClassImage" :style="{ backgroundImage: `url(${image})` }"></div>
        <div>
            <p class="text-base font-medium leading-normal text-[#181111]">{{ title }}</p>
            <p v-if="price" class="text-sm leading-normal text-[#886364]">{{ price }}</p>
        </div>
    </div>
</template>

<script setup>
import { twMerge } from "tailwind-merge";
import { computed } from "vue";

const props = defineProps({
    image: String,
    title: String,
    price: [String, Number],
    type: {
        type: String,
        default: () => "square",
    },
    class: {
        type: String,
        default: () => "",
    },
});

const componentClass = computed(() => {
    const baseType = props.type === "square" ? "min-w-[240px] max-w-[240px]" : "min-w-[240px] max-w-60 lg:max-w-64";
    const base = [baseType, "flex flex-col gap-3 rounded-lg"];
    return twMerge(base, props.class);
});

const componentClassImage = computed(() => {
    const base =
        props.type === "square"
            ? "aspect-video w-full rounded-lg bg-cover bg-center"
            : "w-full bg-center bg-no-repeat aspect-[4/4] bg-cover rounded-lg flex flex-col";

    return base;
});
</script>

<template>
    <div class="flex flex-col gap-3 pb-3">
        <div class="aspect-square w-full rounded-lg bg-cover bg-center" :style="{ backgroundImage: `url(${image})` }"></div>
        <p class="text-base font-medium text-[#181111]">{{ title }}</p>
    </div>
</template>

<script setup>
defineProps({
    image: String,
    title: String,
});
</script>

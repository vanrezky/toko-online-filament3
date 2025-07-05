<template>
    <div class="px-4 pb-3 pt-5">
        <h2 class="text-[22px] font-semibold leading-tight tracking-tight text-[#181111]">{{ title }}</h2>
        <div
            ref="scroller"
            class="mt-3 flex touch-pan-x gap-3 overflow-x-auto scroll-smooth"
            @mousedown="onDragStart"
            @mousemove="onDragMove"
            @mouseup="onDragEnd"
            @mouseleave="onDragEnd"
            @touchstart="onDragStart"
            @touchmove="onDragMove"
            @touchend="onDragEnd"
        >
            <slot />
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    title: String,
});

const scroller = ref(null);
let isDragging = false;
let startX;
let scrollLeft;

const onDragStart = (e) => {
    isDragging = true;
    scroller.value.classList.add("cursor-grabbing");

    startX = e.pageX || e.touches[0].pageX;
    scrollLeft = scroller.value.scrollLeft;
};

const onDragMove = (e) => {
    if (!isDragging) return;
    e.preventDefault();
    const x = e.pageX || e.touches[0].pageX;
    const walk = (x - startX) * 1.5;
    scroller.value.scrollLeft = scrollLeft - walk;
};

const onDragEnd = () => {
    isDragging = false;
    scroller.value.classList.remove("cursor-grabbing");
};
</script>

<style scoped>
::-webkit-scrollbar {
    display: none;
}
</style>

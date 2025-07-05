<script setup>
import AppHead from "./AppHead.vue";
import BottomNavigation from "./BottomNavigation.vue";
const props = defineProps({
    title: {
        type: String,
        default: () => "",
    },
    bottomNavigation: {
        type: Boolean,
        default: () => true,
    },
});
</script>

<template>
    <AppHead :title="title" />
    <div class="bg-gray-100">
        <div
            class="group/design-root relative mx-auto flex size-full min-h-screen w-full max-w-[450px] flex-col justify-between overflow-x-hidden bg-white pb-10 font-sans lg:pb-16"
        >
            <!--  content -->
            <slot />
            <!--  bottom navigation -->
            <BottomNavigation v-if="bottomNavigation" />
        </div>
    </div>
</template>

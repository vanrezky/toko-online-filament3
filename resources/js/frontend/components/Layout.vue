<script setup>
import AppHead from "./AppHead.vue";
import { Toaster } from "vue-sonner";
import FlashMessages from "./FlashMessages.vue";

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
    <Toaster position="top-right" richColors closeButton />
    <FlashMessages />
    <vue3-confirm-dialog />
    <div class="bg-gray-100">
        <div
            class="group/design-root relative mx-auto flex size-full min-h-screen w-full max-w-[450px] flex-col justify-between overflow-x-hidden bg-white pb-10 font-sans lg:pb-16"
        >
            <!--  content -->
            <slot />
            <!--  bottom navigation -->
        </div>
    </div>
</template>

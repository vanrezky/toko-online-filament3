<script setup>
import { toast } from "vue-sonner";
import { usePage } from "@inertiajs/vue3";
import { watch } from "vue";

const page = usePage();

watch(
    () => page.props.flash,
    (flash) => {
        if (!flash) return;
        
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
        if (flash.warning) {
            toast.warning(flash.warning);
        }
        if (flash.info) {
            toast.info(flash.info);
        }
    },
    { deep: true, immediate: true }
);
</script>

<template>
    <!--  This component doesn't render anything visually, but it triggers toast notifications. -->
</template>

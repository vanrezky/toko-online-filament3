<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppHead from '../../AppHead.vue';
import TopbarNotice from './TopbarNotice.vue';
import Header from './Header.vue';
import Footer from './Footer.vue';
import { Toaster } from "vue-sonner";
import FlashMessages from "@frontend/components/FlashMessages.vue";

const props = defineProps({
  title: String,
  description: String,
  keywords: String,
});

const settings = computed(() => usePage().props.settings);
</script>

<template>
  <AppHead :title="title" :description="description" :keywords="keywords" />
  <Toaster position="top-right" richColors closeButton />
  <FlashMessages />
  <vue3-confirm-dialog />
  
  <div class="min-h-screen flex flex-col bg-white">
    <TopbarNotice />
    <Header />
    
    <main class="flex-grow">
      <slot />
    </main>
    
    <Footer />
  </div>
</template>

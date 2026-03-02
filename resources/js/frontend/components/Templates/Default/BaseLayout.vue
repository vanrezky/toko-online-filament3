<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppHead from '../../AppHead.vue';
import TopbarNotice from './TopbarNotice.vue';
import Header from './Header.vue';
import Footer from './Footer.vue';

const props = defineProps({
  title: String,
  description: String,
  keywords: String,
});

const settings = computed(() => usePage().props.settings);
</script>

<template>
  <AppHead :title="title" :description="description" :keywords="keywords" />
  
  <div class="min-h-screen flex flex-col bg-white">
    <TopbarNotice />
    <Header />
    
    <main class="flex-grow">
      <slot />
    </main>
    
    <Footer />
  </div>
</template>

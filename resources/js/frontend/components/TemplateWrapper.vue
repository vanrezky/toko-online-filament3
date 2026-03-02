<script setup>
import { computed } from 'vue';
import DefaultLayout from './Templates/Default/BaseLayout.vue';

const props = defineProps({
  title: String,
  description: String,
  keywords: String,
  template: {
    type: String,
    default: 'default'
  }
});

const Layout = computed(() => {
  return DefaultLayout;
});
</script>

<template>
  <component :is="Layout" :title="title" :description="description" :keywords="keywords">
    <slot />
  </component>
</template>

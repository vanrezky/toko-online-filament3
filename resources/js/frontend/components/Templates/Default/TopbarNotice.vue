<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Mock promotions for now, or fetch from settings/props
const promotions = [
  { id: 1, text: 'Free Shipping Worldwide', link: '#' },
  { id: 2, text: 'Flash Sale ends in 2 hours', link: '#' }
];
</script>

<template>
  <div class="bg-black text-white py-2 text-center text-xs sm:text-sm font-medium">
    <div class="container mx-auto px-4 overflow-hidden relative h-5">
      <div class="animate-marquee whitespace-nowrap">
        <span v-for="(promo, index) in promotions" :key="promo.id" class="mx-8">
          {{ promo.text }}
          <a v-if="promo.link !== '#'" :href="promo.link" class="underline ml-2">Shop Now</a>
        </span>
      </div>
    </div>
  </div>
</template>

<style scoped>
.animate-marquee {
  display: inline-block;
  animation: marquee 20s linear infinite;
}

@keyframes marquee {
  0% { transform: translateX(100%); }
  100% { transform: translateX(-100%); }
}
</style>

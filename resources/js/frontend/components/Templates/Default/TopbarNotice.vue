<script setup>
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const promotions = computed(() => {
  const promos = usePage().props.promotions;
  return (Array.isArray(promos) ? promos : promos?.data || []).filter(p => p.position === 'topbar');
});
</script>

<template>
  <div v-if="promotions.length > 0" class="bg-black text-white py-2 text-center text-xs sm:text-sm font-medium">
    <div class="container mx-auto px-4 overflow-hidden relative h-5">
      <div class="animate-marquee whitespace-nowrap">
        <span v-for="(promo, index) in promotions" :key="promo.id" class="mx-8">
          <component 
            :is="promo.target_link ? 'a' : 'span'" 
            :href="promo.target_link" 
            :target="promo.target_anchor"
            class="hover:underline cursor-pointer"
          >
            {{ promo.title }}
          </component>
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

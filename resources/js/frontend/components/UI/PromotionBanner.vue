<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  promotion: {
    type: Object,
    required: true
  }
});
</script>

<template>
  <div class="group relative overflow-hidden bg-gray-100">
    <!-- Image -->
    <img 
      v-if="promotion.image_url" 
      :src="promotion.image_url" 
      :alt="promotion.title"
      class="w-full h-auto transition-transform duration-700 group-hover:scale-105"
      :class="{ 'absolute inset-0 h-full object-cover': !$attrs.class?.includes('aspect-auto') }"
    />
    
    <!-- Content Overlay (only if description exists or if we want to force text) -->
    <div 
      class="absolute inset-0 flex flex-col justify-center px-8 md:px-16 lg:px-24 transition-colors duration-300"
      :class="promotion.image_url ? 'bg-black/20 group-hover:bg-black/40' : 'bg-gray-100'"
    >
      <div class="max-w-xl space-y-2 md:space-y-4">
        <h3 
          class="text-2xl md:text-3xl lg:text-5xl font-bold leading-tight"
          :class="promotion.image_url ? 'text-white drop-shadow-md' : 'text-black'"
        >
          {{ promotion.title }}
        </h3>
        <p 
          v-if="promotion.description" 
          class="text-sm md:text-base lg:text-lg line-clamp-2 md:line-clamp-none"
          :class="promotion.image_url ? 'text-white/90 drop-shadow-sm' : 'text-gray-600'"
        >
          {{ promotion.description }}
        </p>
        
        <div v-if="promotion.target_link" class="pt-2 md:pt-4">
          <Link 
            :href="promotion.target_link" 
            :target="promotion.target_anchor"
            class="inline-block px-6 py-2 md:px-8 md:py-3 text-xs md:text-sm font-bold uppercase tracking-widest transition-all duration-300 shadow-lg"
            :class="promotion.image_url 
              ? 'bg-white text-black hover:bg-black hover:text-white' 
              : 'bg-black text-white hover:bg-gray-800'"
          >
            Learn More
          </Link>
        </div>
      </div>
    </div>
    
    <!-- Clickable overlay if entire thing should be clickable (when link exists) -->
    <Link 
      v-if="promotion.target_link" 
      :href="promotion.target_link" 
      :target="promotion.target_anchor"
      class="absolute inset-0 z-0"
    >
      <span class="sr-only">View {{ promotion.title }}</span>
    </Link>
  </div>
</template>

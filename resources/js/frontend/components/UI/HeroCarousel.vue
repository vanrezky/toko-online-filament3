<script setup>
import { onMounted } from 'vue';
import emblaCarouselVue from 'embla-carousel-vue';
import Autoplay from 'embla-carousel-autoplay';
import { ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  sliders: {
    type: Array,
    required: true
  }
});

const [emblaRef, emblaApi] = emblaCarouselVue({ loop: true }, [Autoplay({ delay: 5000 })]);

const scrollPrev = () => emblaApi.value?.scrollPrev();
const scrollNext = () => emblaApi.value?.scrollNext();
</script>

<template>
  <section class="relative group overflow-hidden bg-gray-100">
    <div class="embla" ref="emblaRef">
      <div class="embla__container flex">
        <div 
          v-for="(slider, index) in sliders" 
          :key="index" 
          class="embla__slide flex-[0_0_100%] min-w-0 relative aspect-[21/9] md:aspect-[3/1]"
        >
          <img 
            :src="slider.image_url" 
            class="absolute inset-0 w-full h-full object-cover" 
            alt="Hero Image" 
          />
          <!-- Content Overlay -->
          <div class="absolute inset-0 bg-black/10 flex flex-col justify-center px-10 md:px-20 lg:px-40">
            <div class="max-w-xl space-y-6">
              <p class="text-white text-sm md:text-base font-medium tracking-[0.2em] uppercase animate-fade-in-up">New Collection</p>
              <h2 class="text-white text-4xl md:text-6xl lg:text-7xl font-bold leading-tight animate-fade-in-up delay-100">
                {{ slider.description || 'Elevate Your Style' }}
              </h2>
              <div class="pt-4 animate-fade-in-up delay-200">
                <Link 
                  v-if="slider.target_link" 
                  :href="slider.target_link"
                  class="inline-block bg-white text-black px-8 py-3 text-sm font-bold uppercase tracking-widest hover:bg-black hover:text-white transition-all duration-300"
                >
                  {{ slider.target_anchor || 'Shop Now' }}
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Buttons -->
    <button 
      @click="scrollPrev" 
      class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white text-white hover:text-black flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 backdrop-blur-sm"
    >
      <ChevronLeft class="w-6 h-6" />
    </button>
    <button 
      @click="scrollNext" 
      class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 rounded-full bg-white/20 hover:bg-white text-white hover:text-black flex items-center justify-center transition-all duration-300 opacity-0 group-hover:opacity-100 backdrop-blur-sm"
    >
      <ChevronRight class="w-6 h-6" />
    </button>
  </section>
</template>

<style scoped>
.embla__slide {
  position: relative;
}

@keyframes fade-in-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in-up {
  animation: fade-in-up 0.8s ease-out forwards;
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
</style>

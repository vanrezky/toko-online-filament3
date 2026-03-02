<script setup>
import { onMounted, onUnmounted, ref } from 'vue';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import FlashSaleCard from '../../components/UI/FlashSaleCard.vue';
import { Clock } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  flashSale: Object,
});

const timeLeft = ref({ h: '00', m: '00', s: '00' });

const updateCountdown = () => {
  if (!props.flashSale?.end_time) return;
  
  const now = new Date().getTime();
  const end = new Date(props.flashSale.end_time).getTime();
  const diff = end - now;
  
  if (diff > 0) {
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    timeLeft.value = {
      h: String(hours).padStart(2, '0'),
      m: String(minutes).padStart(2, '0'),
      s: String(seconds).padStart(2, '0')
    };
  } else {
    timeLeft.value = { h: '00', m: '00', s: '00' };
  }
};

let timer;
onMounted(() => {
  updateCountdown();
  timer = setInterval(updateCountdown, 1000);
});

onUnmounted(() => {
  clearInterval(timer);
});
</script>

<template>
  <TemplateWrapper title="Flash Sale">
    <div class="py-12 md:py-20 bg-gray-50 min-h-screen">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-6xl mx-auto space-y-16">
          
          <!-- Header -->
          <div class="text-center space-y-4">
            <div class="flex items-center justify-center space-x-3 text-red-600">
              <span class="inline-block w-12 h-[2px] bg-red-600"></span>
              <span class="font-bold uppercase tracking-[0.3em] text-xs">Don't Miss Out</span>
              <span class="inline-block w-12 h-[2px] bg-red-600"></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-black tracking-tight uppercase italic">Flash Sale</h1>
            <p class="text-gray-500 text-sm max-w-lg mx-auto leading-relaxed">
              Our most exclusive deals are here for a very limited time. Grab your favorites before they're gone!
            </p>
          </div>

          <template v-if="flashSale && flashSale.products && flashSale.products.length > 0">
            <div class="space-y-8">
              <!-- Sale Banner & Timer -->
              <div class="bg-black text-white p-6 md:p-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2">
                  <h2 class="text-2xl font-bold uppercase tracking-widest">{{ flashSale.name }}</h2>
                  <p class="text-gray-400 text-xs uppercase tracking-widest">{{ flashSale.description }}</p>
                </div>
                
                <div class="flex items-center space-x-6">
                  <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Ends In:</span>
                  <div class="flex items-center space-x-2 font-bold text-xl md:text-2xl">
                    <div class="flex flex-col items-center">
                      <span class="bg-white text-black px-3 py-2 leading-none">{{ timeLeft.h }}</span>
                      <span class="text-[8px] uppercase mt-1">Hrs</span>
                    </div>
                    <span class="mb-5">:</span>
                    <div class="flex flex-col items-center">
                      <span class="bg-white text-black px-3 py-2 leading-none">{{ timeLeft.m }}</span>
                      <span class="text-[8px] uppercase mt-1">Min</span>
                    </div>
                    <span class="mb-5">:</span>
                    <div class="flex flex-col items-center">
                      <span class="bg-white text-black px-3 py-2 leading-none">{{ timeLeft.s }}</span>
                      <span class="text-[8px] uppercase mt-1">Sec</span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Product Grid -->
              <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
                <FlashSaleCard 
                  v-for="item in flashSale.products" 
                  :key="item.product.uuid" 
                  :product="item" 
                />
              </div>
            </div>
          </template>

          <!-- Empty State -->
          <div v-else class="py-20 text-center space-y-8 bg-white shadow-sm">
            <div class="w-24 h-24 bg-gray-50 flex items-center justify-center mx-auto rounded-full">
              <Clock class="w-10 h-10 text-gray-300" />
            </div>
            <div class="space-y-3">
              <h2 class="text-2xl font-bold text-black">No Active Flash Sales</h2>
              <p class="text-gray-500 text-sm max-w-sm mx-auto">We don't have any active flash sales right now. Please check back later or explore our regular collection.</p>
            </div>
            <Link :href="route('frontend.products')" class="inline-block bg-black text-white px-10 py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition-all">
              Shop Regular Collection
            </Link>
          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

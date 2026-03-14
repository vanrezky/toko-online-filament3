<script setup>
import { computed, ref, onMounted } from 'vue';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import HeroCarousel from '../../components/UI/HeroCarousel.vue';
import ProductCard from '../../components/UI/ProductCard.vue';
import FlashSaleCard from '../../components/UI/FlashSaleCard.vue';
import { ChevronRight, Clock, X } from 'lucide-vue-next';
import { Link } from '@inertiajs/vue3';
import PromotionBanner from '../../components/UI/PromotionBanner.vue';

const props = defineProps({
  sliders: Array,
  flashsales: Object,
  newArrivals: Array,
  featuredCategories: Array,
  promotions: [Array, Object],
});

const allPromos = computed(() => Array.isArray(props.promotions) ? props.promotions : props.promotions?.data || []);
const heroPromos = computed(() => allPromos.value.filter(p => p.position === 'home_hero'));
const sidebarPromos = computed(() => allPromos.value.filter(p => p.position === 'home_sidebar'));
const popupPromos = computed(() => allPromos.value.filter(p => p.position === 'home_popup'));
const showPopup = ref(false);

// For countdown logic
const timeLeft = ref({
  hours: '00',
  minutes: '00',
  seconds: '00'
});

const calculateTimeLeft = () => {
  if (!props.flashsales?.end_time) return;
  
  const end = new Date(props.flashsales.end_time).getTime();
  const now = new Date().getTime();
  const diff = end - now;
  
  if (diff > 0) {
    const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);
    
    timeLeft.value = {
      hours: String(hours).padStart(2, '0'),
      minutes: String(minutes).padStart(2, '0'),
      seconds: String(seconds).padStart(2, '0')
    };
  }
};

onMounted(() => {
  calculateTimeLeft();
  setInterval(calculateTimeLeft, 1000);

  if (popupPromos.value.length > 0) {
    setTimeout(() => {
      showPopup.value = true;
    }, 3000);
  }
});
</script>

<template>
  <TemplateWrapper title="Home">
    <!-- Hero Section -->
    <HeroCarousel v-if="sliders && sliders.length > 0" :sliders="sliders" />

    <!-- Promotions: Hero Position (New Section) -->
    <section v-if="heroPromos.length > 0" class="py-12 border-b border-gray-100">
      <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <PromotionBanner v-for="promo in heroPromos" :key="promo.id" :promotion="promo" class="aspect-[16/7] md:aspect-[21/9]" />
        </div>
      </div>
    </section>

    <!-- Flash Sale -->
    <section v-if="flashsales && flashsales.products && flashsales.products.length > 0" class="py-12 md:py-20 bg-gray-50">
      <div class="container mx-auto px-4 md:px-6">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 md:mb-12 space-y-4 md:space-y-0">
          <div class="space-y-2">
            <div class="flex items-center space-x-3">
              <span class="inline-block w-8 h-[2px] bg-red-600"></span>
              <span class="text-red-600 font-bold uppercase tracking-widest text-xs">Limited Offer</span>
            </div>
            <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight">{{ flashsales.name }}</h2>
          </div>
          
          <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-3 text-black">
              <Clock class="w-5 h-5" />
              <div class="flex items-center space-x-1 font-bold">
                <span class="bg-black text-white px-2 py-1 rounded-sm">{{ timeLeft.hours }}</span>
                <span>:</span>
                <span class="bg-black text-white px-2 py-1 rounded-sm">{{ timeLeft.minutes }}</span>
                <span>:</span>
                <span class="bg-black text-white px-2 py-1 rounded-sm">{{ timeLeft.seconds }}</span>
              </div>
            </div>
            <Link :href="route('frontend.flashsales')" class="hidden md:flex items-center text-sm font-bold uppercase tracking-wider text-black hover:text-gray-600 transition-colors">
              View All <ChevronRight class="ml-1 w-4 h-4" />
            </Link>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
          <FlashSaleCard 
            v-for="item in flashsales.products" 
            :key="item.product.uuid" 
            :product="item" 
          />
        </div>
      </div>
    </section>

    <!-- New Arrivals -->
    <section class="py-12 md:py-20">
      <div class="container mx-auto px-4 md:px-6 text-center">
        <div class="max-w-2xl mx-auto space-y-4 mb-12">
          <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight">New Arrivals</h2>
          <p class="text-gray-500 text-sm leading-relaxed">
            Discover our latest pieces, carefully selected to keep you ahead of the trends.
          </p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8 text-left">
          <ProductCard 
            v-for="product in newArrivals" 
            :key="product.uuid" 
            :product="product" 
          />
        </div>
        
        <div class="mt-12">
          <Link :href="route('frontend.products')" class="inline-block border-b-2 border-black pb-1 text-sm font-bold uppercase tracking-widest hover:text-gray-500 hover:border-gray-500 transition-all">
            Explore All Products
          </Link>
        </div>
      </div>
    </section>

    <!-- Promotions: Sidebar Position (as a new standalone section to not break New Arrivals layout) -->
    <section v-if="sidebarPromos.length > 0" class="py-12 bg-white">
      <div class="container mx-auto px-4 md:px-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
           <PromotionBanner v-for="promo in sidebarPromos" :key="promo.id" :promotion="promo" class="aspect-[4/5]" />
        </div>
      </div>
    </section>

    <!-- Category Highlights -->
    <section v-for="category in featuredCategories" :key="category.id" class="py-12 md:py-20 border-t border-gray-100">
      <div class="container mx-auto px-4 md:px-6">
        <div class="flex items-center justify-between mb-8 md:mb-12">
          <h2 class="text-2xl md:text-3xl font-bold text-black tracking-tight uppercase">{{ category.name }}</h2>
          <Link :href="route('frontend.products', { category: category.slug })" class="flex items-center text-sm font-bold uppercase tracking-wider text-black hover:text-gray-600 transition-colors">
            Shop Category <ChevronRight class="ml-1 w-4 h-4" />
          </Link>
        </div>

        <div v-if="category.products && category.products.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-6">
          <ProductCard 
            v-for="product in category.products" 
            :key="`${category.id}-${product.uuid}`" 
            :product="product" 
          />
        </div>
        <div v-else class="text-center py-12 bg-gray-50 text-gray-400 text-sm italic uppercase tracking-widest">
          Coming Soon
        </div>
      </div>
    </section>

    <!-- Newsletter -->
    <section class="py-20 bg-gray-50">
      <div class="container mx-auto px-4 md:px-6 text-center">
        <div class="max-w-2xl mx-auto space-y-8">
          <h2 class="text-3xl md:text-4xl font-bold text-black tracking-tight uppercase">Stay in the Loop</h2>
          <p class="text-gray-500 text-sm leading-relaxed max-w-md mx-auto">
            Join our mailing list for exclusive access to new arrivals, private sales, and fashion events.
          </p>
          <form class="flex flex-col md:flex-row max-w-md mx-auto gap-4" @submit.prevent>
            <input 
              type="email" 
              placeholder="Your email address" 
              class="flex-grow bg-white border border-gray-200 py-3 px-6 rounded-none focus:outline-none focus:border-black text-sm transition-all"
            />
            <button class="bg-black text-white px-8 py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl">
              Subscribe
            </button>
          </form>
        </div>
      </div>
    </section>

    <!-- Popup Promotion Modal -->
    <div v-if="showPopup && popupPromos.length > 0" class="fixed inset-0 z-[100] flex items-center justify-center p-4 md:p-6 bg-black/70 backdrop-blur-md">
      <div class="relative bg-white max-w-lg w-full shadow-2xl overflow-hidden animate-zoom-in max-h-[85vh] flex flex-col">
        <!-- Close Button -->
        <button 
          @click="showPopup = false" 
          class="absolute top-3 right-3 z-[110] w-10 h-10 flex items-center justify-center bg-black/40 text-white hover:bg-black transition-all duration-300 rounded-full backdrop-blur-md border border-white/20 shadow-lg"
        >
          <X class="w-6 h-6" />
        </button>

        <div class="overflow-y-auto flex-grow">
          <PromotionBanner 
            :promotion="popupPromos[0]" 
            :class="popupPromos[0].image_url ? 'aspect-auto' : 'aspect-square'" 
          />
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

<style scoped>
@keyframes zoom-in {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-zoom-in {
  animation: zoom-in 0.4s ease-out forwards;
}
</style>

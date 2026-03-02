<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import ProductCard from '../../components/UI/ProductCard.vue';
import { Heart, ShoppingBag, ArrowRight } from 'lucide-vue-next';

const props = defineProps({
  products: Object
});

const items = computed(() => props.products.data || []);
</script>

<template>
  <TemplateWrapper title="My Wishlist">
    <div class="py-12 md:py-20">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-6xl mx-auto space-y-12">
          <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div class="space-y-2">
              <h1 class="text-3xl md:text-4xl font-bold text-black tracking-tight uppercase">My Wishlist</h1>
              <p class="text-gray-500 text-sm">Products you've saved for later.</p>
            </div>
            <span v-if="items.length > 0" class="text-xs font-bold uppercase tracking-widest text-gray-400">
              {{ items.length }} Items Saved
            </span>
          </div>

          <div v-if="items.length > 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-8">
            <ProductCard 
              v-for="product in items" 
              :key="product.uuid" 
              :product="product" 
            />
          </div>

          <!-- Empty State -->
          <div v-else class="py-20 text-center space-y-8 bg-white shadow-sm border border-gray-100">
            <div class="w-24 h-24 bg-gray-50 flex items-center justify-center mx-auto rounded-full">
              <Heart class="w-10 h-10 text-gray-300" />
            </div>
            <div class="space-y-3">
              <h2 class="text-2xl font-bold text-black uppercase tracking-tight">Your wishlist is empty</h2>
              <p class="text-gray-500 text-sm max-w-sm mx-auto">Save your favorite items to your wishlist so you can find them easily later.</p>
            </div>
            <Link :href="route('frontend.products')" class="inline-block bg-black text-white px-10 py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg">
              Explore Products
            </Link>
          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

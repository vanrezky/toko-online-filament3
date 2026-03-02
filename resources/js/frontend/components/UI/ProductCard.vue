<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Heart } from 'lucide-vue-next';

const props = defineProps({
  product: {
    type: Object,
    required: true
  }
});

const page = usePage();
const isWishlisted = computed(() => {
  return page.props.wishlist_product_ids?.includes(props.product.id);
});

const toggleWishlist = (e) => {
  e.preventDefault();
  e.stopPropagation();
  
  if (!page.props.auth.user) {
    router.get(route('frontend.login'));
    return;
  }

  router.post(route('frontend.wishlist.toggle'), {
    product_id: props.product.id
  }, {
    preserveScroll: true,
  });
};

const isSale = computed(() => !!props.product.sale_price);

const badge = computed(() => {
  if (isSale.value && props.product.discount_percentage) {
    return { text: `${props.product.discount_percentage}% OFF`, class: 'bg-black text-white' };
  }
  if (props.product.digital) return { text: 'Digital', class: 'bg-blue-500 text-white' };
  return null;
});
</script>

<template>
  <div class="group relative flex flex-col bg-white overflow-hidden transition-all duration-300 hover:shadow-lg rounded-sm border border-transparent hover:border-gray-100">
    <!-- Image -->
    <Link :href="route('frontend.product-detail', product.slug)" class="aspect-[4/5] overflow-hidden bg-gray-50 relative">
      <img 
        :src="product.thumbnail || 'https://placehold.co/400x500?text=No+Image'" 
        :alt="product.name"
        class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
      />
      
      <!-- Badge -->
      <div v-if="badge" class="absolute top-3 left-3 px-2 py-1 text-[10px] font-bold uppercase tracking-wider" :class="badge.class">
        {{ badge.text }}
      </div>

      <!-- Wishlist Toggle -->
      <button 
        @click="toggleWishlist"
        class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-sm md:opacity-0 md:group-hover:opacity-100 transition-all duration-300 hover:scale-110 z-10"
      >
        <Heart 
          class="w-4 h-4 transition-colors" 
          :class="isWishlisted ? 'fill-red-500 text-red-500' : 'text-gray-400 hover:text-black'"
        />
      </button>
    </Link>

    <!-- Content -->
    <div class="flex flex-col flex-grow p-4 space-y-2">
      <!-- Category Name -->
      <div v-if="product.category_name" class="text-[10px] font-bold uppercase tracking-widest text-gray-400">
        {{ product.category_name }}
      </div>

      <Link :href="route('frontend.product-detail', product.slug)" class="block">
        <h3 class="text-sm font-medium text-gray-900 line-clamp-2 leading-tight min-h-[2.5rem] group-hover:text-gray-600 transition-colors">
          {{ product.name }}
        </h3>
      </Link>
      
      <div class="flex items-center space-x-2">
        <template v-if="isSale">
          <span class="text-sm font-bold text-black">{{ product.sale_price }}</span>
          <span class="text-xs text-gray-400 line-through">{{ product.price }}</span>
        </template>
        <span v-else class="text-sm font-bold text-black">{{ product.price }}</span>
      </div>
    </div>
  </div>
</template>

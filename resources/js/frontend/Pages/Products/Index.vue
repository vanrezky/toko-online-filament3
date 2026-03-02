<script setup>
import { ref, watch, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import ProductCard from '../../components/UI/ProductCard.vue';
import { Search, SlidersHorizontal, ChevronDown, X, Loader2 } from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps({
  products: Object,
  categories: Array,
  filters: Object
});

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');
const selectedSort = ref(props.filters.sort || 'newest');
const isFilterOpen = ref(false);
const isLoadingMore = ref(false);
const allProducts = ref([...props.products.data]);

// Watch for props changes when filtering/sorting (reset list)
watch(() => props.products.data, (newData) => {
  if (!isLoadingMore.value) {
    allProducts.value = [...newData];
  }
});

const applyFilters = debounce(() => {
  router.get(route('frontend.products'), {
    search: search.value,
    category: selectedCategory.value,
    sort: selectedSort.value
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true,
    onStart: () => { isLoadingMore.value = false; }
  });
}, 500);

watch([search, selectedCategory, selectedSort], () => {
  applyFilters();
});

const loadMore = () => {
  if (props.products.links.next && !isLoadingMore.value) {
    isLoadingMore.value = true;
    router.get(props.products.links.next, {
      search: search.value,
      category: selectedCategory.value,
      sort: selectedSort.value
    }, {
      preserveState: true,
      preserveScroll: true,
      only: ['products'],
      onSuccess: (page) => {
        allProducts.value = [...allProducts.value, ...page.props.products.data];
        isLoadingMore.value = false;
      },
      onFinish: () => {
        isLoadingMore.value = false;
      }
    });
  }
};

const resetFilters = () => {
  search.value = '';
  selectedCategory.value = '';
  selectedSort.value = 'newest';
};
</script>

<template>
  <TemplateWrapper title="All Products">
    <div class="py-12 md:py-16">
      <div class="container mx-auto px-4 md:px-6">
        <div class="flex flex-col md:flex-row gap-12 lg:gap-16">
          
          <!-- Filters Sidebar (Desktop) -->
          <aside class="hidden md:block w-64 shrink-0">
            <div class="sticky top-32 space-y-12">
              <!-- Floating Label Search -->
              <div class="relative group">
                <input 
                  id="desktop-search"
                  v-model="search"
                  type="text" 
                  placeholder=" " 
                  class="peer block w-full appearance-none border-b border-gray-200 bg-transparent px-0 py-4 text-sm text-gray-900 focus:border-black focus:outline-none focus:ring-0 transition-all"
                />
                <label 
                  for="desktop-search"
                  class="absolute top-4 -z-10 origin-[0] -translate-y-6 scale-75 transform text-xs font-bold uppercase tracking-[0.2em] text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:text-black"
                >
                  Search Products
                </label>
                <Search class="absolute right-0 top-4 w-4 h-4 text-gray-300 group-focus-within:text-black transition-colors" />
              </div>

              <div class="space-y-6">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-black">Categories</h3>
                <div class="space-y-4">
                  <label class="flex items-center group cursor-pointer">
                    <input 
                      type="radio" 
                      value="" 
                      v-model="selectedCategory"
                      class="w-4 h-4 border-gray-300 text-black focus:ring-black rounded-none"
                    />
                    <span class="ml-3 text-xs uppercase tracking-widest text-gray-500 group-hover:text-black transition-colors" :class="{ 'font-bold text-black': selectedCategory === '' }">
                      All Categories
                    </span>
                  </label>
                  <label v-for="cat in categories" :key="cat.id" class="flex items-center group cursor-pointer">
                    <input 
                      type="radio" 
                      :value="cat.slug" 
                      v-model="selectedCategory"
                      class="w-4 h-4 border-gray-300 text-black focus:ring-black rounded-none"
                    />
                    <span class="ml-3 text-xs uppercase tracking-widest text-gray-500 group-hover:text-black transition-colors" :class="{ 'font-bold text-black': selectedCategory === cat.slug }">
                      {{ cat.name }}
                    </span>
                  </label>
                </div>
              </div>

              <div class="space-y-6">
                <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-black">Sort By</h3>
                <select v-model="selectedSort" class="w-full bg-gray-50 border border-gray-100 py-3 px-4 text-xs font-bold uppercase tracking-widest focus:outline-none focus:border-black rounded-none appearance-none cursor-pointer transition-all">
                  <option value="newest">Newest Arrival</option>
                  <option value="price_low">Price: Low to High</option>
                  <option value="price_high">Price: High to Low</option>
                </select>
              </div>

              <button @click="resetFilters" class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 hover:text-black transition-colors flex items-center">
                <X class="w-4 h-4 mr-2" /> Reset Filters
              </button>
            </div>
          </aside>

          <!-- Mobile Filter Button -->
          <div class="md:hidden flex items-center justify-between py-4 border-y border-gray-100 mb-8">
            <button @click="isFilterOpen = true" class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest">
              <SlidersHorizontal class="w-4 h-4" />
              <span>Filters</span>
            </button>
            <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ allProducts.length }} Products</span>
          </div>

          <!-- Product Grid -->
          <div class="flex-grow space-y-12">
            <div v-if="allProducts.length > 0" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 md:gap-8">
              <ProductCard 
                v-for="product in allProducts" 
                :key="product.uuid" 
                :product="product" 
              />
            </div>
            
            <!-- Empty State -->
            <div v-else class="py-20 text-center space-y-6">
              <div class="w-20 h-20 bg-gray-50 flex items-center justify-center mx-auto rounded-full">
                <Search class="w-8 h-8 text-gray-300" />
              </div>
              <div class="space-y-2">
                <h3 class="text-xl font-bold text-black uppercase tracking-tight">No products found</h3>
                <p class="text-gray-500 text-sm">Try adjusting your filters or search keywords.</p>
              </div>
              <button @click="resetFilters" class="inline-block bg-black text-white px-8 py-4 text-[10px] font-bold uppercase tracking-[0.2em] hover:bg-gray-800 transition-all shadow-lg">
                Clear all filters
              </button>
            </div>

            <!-- Load More Button -->
            <div v-if="products.links.next" class="pt-12 border-t border-gray-100 flex justify-center">
              <button 
                @click="loadMore" 
                :disabled="isLoadingMore"
                class="min-w-[200px] border border-black bg-white px-8 py-4 text-[10px] font-bold uppercase tracking-[0.2em] text-black transition-all hover:bg-black hover:text-white disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center shadow-sm hover:shadow-md"
              >
                <Loader2 v-if="isLoadingMore" class="w-4 h-4 mr-2 animate-spin" />
                <span>{{ isLoadingMore ? 'Loading...' : 'Show More' }}</span>
              </button>
            </div>
            <div v-else-if="allProducts.length > 0" class="pt-12 text-center">
               <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">You've reached the end</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Filters Backdrop/Overlay -->
    <div v-if="isFilterOpen" class="fixed inset-0 z-[60] bg-black/50 md:hidden" @click="isFilterOpen = false"></div>
    <!-- Mobile Filters Drawer -->
    <div class="fixed inset-y-0 right-0 z-[70] w-full max-w-xs bg-white shadow-xl md:hidden transition-transform duration-300" :class="isFilterOpen ? 'translate-x-0' : 'translate-x-full'">
      <div class="h-full flex flex-col p-8 space-y-12 overflow-y-auto">
        <div class="flex items-center justify-between">
          <h2 class="text-xs font-bold uppercase tracking-[0.3em] text-black">Filters</h2>
          <button @click="isFilterOpen = false"><X class="w-6 h-6" /></button>
        </div>
        
        <!-- Mobile Floating Label Search -->
        <div class="relative group">
          <input 
            id="mobile-search"
            v-model="search"
            type="text" 
            placeholder=" " 
            class="peer block w-full appearance-none border-b border-gray-200 bg-transparent px-0 py-4 text-sm text-gray-900 focus:border-black focus:outline-none focus:ring-0 transition-all"
          />
          <label 
            for="mobile-search"
            class="absolute top-4 -z-10 origin-[0] -translate-y-6 scale-75 transform text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400 duration-300 peer-placeholder-shown:translate-y-0 peer-placeholder-shown:scale-100 peer-focus:left-0 peer-focus:-translate-y-6 peer-focus:scale-75 peer-focus:text-black"
          >
            Search Products
          </label>
        </div>

        <!-- Mobile Categories -->
        <div class="space-y-6">
          <h3 class="text-[10px] font-bold uppercase tracking-[0.3em] text-black">Categories</h3>
          <div class="space-y-4">
            <button 
              @click="selectedCategory = ''; isFilterOpen = false" 
              class="block w-full text-left text-xs uppercase tracking-widest py-2" 
              :class="selectedCategory === '' ? 'font-bold text-black' : 'text-gray-500'"
            >
              All Categories
            </button>
            <button 
              v-for="cat in categories" 
              :key="cat.id" 
              @click="selectedCategory = cat.slug; isFilterOpen = false" 
              class="block w-full text-left text-xs uppercase tracking-widest py-2" 
              :class="selectedCategory === cat.slug ? 'font-bold text-black' : 'text-gray-500'"
            >
              {{ cat.name }}
            </button>
          </div>
        </div>

        <button @click="isFilterOpen = false" class="mt-auto w-full bg-black text-white py-5 text-[10px] font-bold uppercase tracking-[0.3em] shadow-lg">
          Close Filters
        </button>
      </div>
    </div>
  </TemplateWrapper>
</template>

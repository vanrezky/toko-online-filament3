<script setup>
import { ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { Search, Calendar, User, ArrowRight, Tag } from 'lucide-vue-next';
import debounce from 'lodash/debounce';

const props = defineProps({
  posts: Object,
  categories: Array,
  filters: Object
});

const search = ref(props.filters.search || '');
const selectedCategory = ref(props.filters.category || '');

const applyFilters = debounce(() => {
  router.get(route('frontend.blog.index'), {
    search: search.value,
    category: selectedCategory.value,
  }, {
    preserveState: true,
    preserveScroll: true,
    replace: true
  });
}, 500);

watch([search, selectedCategory], () => {
  applyFilters();
});
</script>

<template>
  <TemplateWrapper 
    title="Our Blog"
    description="Stay updated with our latest news, fashion tips, and lifestyle articles."
  >
    <div class="py-12 md:py-20 bg-white">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-6xl mx-auto space-y-12">
          <!-- Header -->
          <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 border-b border-gray-100 pb-12">
            <div class="space-y-4">
              <h1 class="text-4xl md:text-5xl font-bold text-black tracking-tight uppercase">The Journal</h1>
              <p class="text-gray-500 text-lg max-w-md">Reflections on style, culture, and the art of living well.</p>
            </div>
            
            <div class="w-full md:w-80">
              <div class="relative">
                <input 
                  v-model="search"
                  type="text" 
                  placeholder="Search articles..." 
                  class="w-full bg-gray-50 border-none py-4 pl-12 pr-6 text-sm focus:ring-1 focus:ring-black transition-all"
                />
                <Search class="absolute left-4 top-4 w-4 h-4 text-gray-400" />
              </div>
            </div>
          </div>

          <!-- Categories Filter -->
          <div class="flex flex-wrap gap-4 overflow-x-auto pb-4 no-scrollbar">
            <button 
              @click="selectedCategory = ''"
              class="px-6 py-2 text-[10px] font-bold uppercase tracking-widest transition-all border rounded-full"
              :class="selectedCategory === '' ? 'bg-black text-white border-black' : 'bg-transparent text-gray-400 border-gray-100 hover:border-gray-300'"
            >
              All Articles
            </button>
            <button 
              v-for="cat in categories" 
              :key="cat.slug"
              @click="selectedCategory = cat.slug"
              class="px-6 py-2 text-[10px] font-bold uppercase tracking-widest transition-all border rounded-full whitespace-nowrap"
              :class="selectedCategory === cat.slug ? 'bg-black text-white border-black' : 'bg-transparent text-gray-400 border-gray-100 hover:border-gray-300'"
            >
              {{ cat.name }}
            </button>
          </div>

          <!-- Posts Grid -->
          <div v-if="posts.data.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 md:gap-12">
            <article 
              v-for="post in posts.data" 
              :key="post.id" 
              class="group flex flex-col space-y-6"
            >
              <Link :href="route('frontend.blog.show', post.slug)" class="aspect-[16/10] overflow-hidden bg-gray-100 block">
                <img 
                  v-if="post.image_url" 
                  :src="post.image_url" 
                  :alt="post.title"
                  class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                />
                <div v-else class="w-full h-full flex items-center justify-center text-gray-300 uppercase tracking-widest text-[10px] font-bold">
                  No Image Available
                </div>
              </Link>
              
              <div class="space-y-4">
                <div class="flex items-center space-x-4 text-[10px] font-bold uppercase tracking-widest text-gray-400">
                  <span class="text-black">{{ post.category?.name }}</span>
                  <span>/</span>
                  <span>{{ post.published_at }}</span>
                </div>
                
                <Link :href="route('frontend.blog.show', post.slug)" class="block">
                  <h3 class="text-xl md:text-2xl font-bold text-black leading-tight group-hover:text-gray-600 transition-colors line-clamp-2">
                    {{ post.title }}
                  </h3>
                </Link>
                
                <p class="text-gray-500 text-sm leading-relaxed line-clamp-3">
                  {{ post.excerpt }}
                </p>
                
                <Link 
                  :href="route('frontend.blog.show', post.slug)" 
                  class="inline-flex items-center text-[10px] font-bold uppercase tracking-[0.2em] text-black group-hover:translate-x-2 transition-transform"
                >
                  Read Article <ArrowRight class="ml-2 w-3 h-3" />
                </Link>
              </div>
            </article>
          </div>

          <!-- Empty State -->
          <div v-else class="py-24 text-center space-y-6 bg-gray-50">
            <div class="w-16 h-16 bg-white flex items-center justify-center mx-auto rounded-full shadow-sm">
              <Search class="w-6 h-6 text-gray-300" />
            </div>
            <div class="space-y-2">
              <h3 class="text-xl font-bold text-black uppercase tracking-tight">No articles found</h3>
              <p class="text-gray-500 text-sm">We couldn't find any articles matching your search criteria.</p>
            </div>
            <button @click="search = ''; selectedCategory = ''" class="text-[10px] font-bold uppercase tracking-widest border-b-2 border-black pb-1 hover:text-gray-500 hover:border-gray-500 transition-all">
              Clear Filters
            </button>
          </div>

          <!-- Pagination -->
          <div v-if="posts.links.length > 3" class="pt-12 border-t border-gray-100 flex justify-center">
            <div class="flex space-x-2">
              <Link 
                v-for="(link, k) in posts.links" 
                :key="k"
                :href="link.url || '#'"
                v-html="link.label"
                class="px-4 py-2 text-[10px] font-bold uppercase tracking-widest transition-all"
                :class="{ 
                  'bg-black text-white': link.active,
                  'text-gray-400 hover:text-black': !link.active && link.url,
                  'opacity-20 cursor-not-allowed': !link.url
                }"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}
</style>

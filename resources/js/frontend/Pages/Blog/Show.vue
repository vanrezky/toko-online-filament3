<script setup>
import { Link } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { Calendar, User, ArrowLeft, Tag, Share2, Facebook, Twitter, Link as LinkIcon } from 'lucide-vue-next';

const props = defineProps({
  post: Object,
  relatedPosts: Object
});

const share = (platform) => {
  const url = window.location.href;
  const title = props.post.title;
  
  if (platform === 'facebook') {
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
  } else if (platform === 'twitter') {
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank');
  } else if (platform === 'copy') {
    navigator.clipboard.writeText(url);
    // You could add a toast notification here
  }
};
</script>

<template>
  <TemplateWrapper 
    :title="post.meta?.title || post.title"
    :description="post.meta?.description || post.excerpt"
    :keywords="post.meta?.keyword"
  >
    <article class="bg-white">
      <!-- Hero Section -->
      <div class="relative h-[60vh] md:h-[70vh] bg-gray-900 overflow-hidden">
        <img 
          v-if="post.image_url" 
          :src="post.image_url" 
          :alt="post.title"
          class="w-full h-full object-cover opacity-60"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
        
        <div class="absolute inset-0 flex items-center justify-center">
          <div class="container mx-auto px-4 md:px-6">
            <div class="max-w-4xl mx-auto text-center space-y-6">
              <Link :href="route('frontend.blog.index', { category: post.category?.slug })" class="inline-block text-[10px] font-bold uppercase tracking-[0.3em] text-white/80 hover:text-white transition-colors border-b border-white/30 pb-1">
                {{ post.category?.name }}
              </Link>
              <h1 class="text-4xl md:text-6xl font-bold text-white tracking-tight leading-tight">
                {{ post.title }}
              </h1>
              <div class="flex items-center justify-center space-x-6 text-[10px] font-bold uppercase tracking-widest text-white/60">
                <span class="flex items-center"><User class="w-3 h-3 mr-2" /> By {{ post.author?.name }}</span>
                <span class="flex items-center"><Calendar class="w-3 h-3 mr-2" /> {{ post.published_at }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="py-20">
        <div class="container mx-auto px-4 md:px-6">
          <div class="max-w-4xl mx-auto">
            <div class="flex flex-col lg:flex-row gap-16">
              <!-- Sidebar/Meta (Desktop) -->
              <aside class="hidden lg:block w-48 shrink-0 space-y-12">
                <div class="space-y-4">
                  <h4 class="text-[10px] font-bold uppercase tracking-widest text-black">Share</h4>
                  <div class="flex flex-col space-y-4">
                    <button @click="share('facebook')" class="text-gray-400 hover:text-black transition-colors flex items-center text-[10px] font-bold uppercase tracking-widest">
                      <Facebook class="w-4 h-4 mr-3" /> Facebook
                    </button>
                    <button @click="share('twitter')" class="text-gray-400 hover:text-black transition-colors flex items-center text-[10px] font-bold uppercase tracking-widest">
                      <Twitter class="w-4 h-4 mr-3" /> Twitter
                    </button>
                    <button @click="share('copy')" class="text-gray-400 hover:text-black transition-colors flex items-center text-[10px] font-bold uppercase tracking-widest">
                      <LinkIcon class="w-4 h-4 mr-3" /> Copy Link
                    </button>
                  </div>
                </div>

                <div v-if="post.tags && post.tags.length > 0" class="space-y-4">
                  <h4 class="text-[10px] font-bold uppercase tracking-widest text-black">Tags</h4>
                  <div class="flex flex-wrap gap-2">
                    <Link 
                      v-for="tag in post.tags" 
                      :key="tag"
                      :href="route('frontend.blog.index', { tag })"
                      class="text-[10px] font-bold uppercase tracking-widest text-gray-400 hover:text-black transition-colors"
                    >
                      #{{ tag }}
                    </Link>
                  </div>
                </div>
              </aside>

              <!-- Main Content -->
              <div class="flex-grow">
                <div class="prose prose-lg prose-black max-w-none prose-img:rounded-none prose-headings:uppercase prose-headings:tracking-tight" v-html="post.content"></div>
                
                <!-- Mobile Meta -->
                <div class="lg:hidden mt-16 pt-12 border-t border-gray-100 space-y-8">
                  <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap gap-4">
                    <Link 
                      v-for="tag in post.tags" 
                      :key="tag"
                      :href="route('frontend.blog.index', { tag })"
                      class="text-[10px] font-bold uppercase tracking-widest text-gray-400 border border-gray-100 px-4 py-2 rounded-full"
                    >
                      #{{ tag }}
                    </Link>
                  </div>
                  <div class="flex items-center space-x-6">
                    <span class="text-[10px] font-bold uppercase tracking-widest text-black mr-2">Share:</span>
                    <button @click="share('facebook')" class="text-gray-400 hover:text-black"><Facebook class="w-4 h-4" /></button>
                    <button @click="share('twitter')" class="text-gray-400 hover:text-black"><Twitter class="w-4 h-4" /></button>
                    <button @click="share('copy')" class="text-gray-400 hover:text-black"><LinkIcon class="w-4 h-4" /></button>
                  </div>
                </div>

                <!-- Related Posts -->
                <div v-if="relatedPosts.data.length > 0" class="mt-24 pt-20 border-t border-gray-100">
                  <h3 class="text-2xl font-bold text-black uppercase tracking-tight mb-12">Related Reading</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div v-for="rp in relatedPosts.data" :key="rp.id" class="group space-y-4">
                      <Link :href="route('frontend.blog.show', rp.slug)" class="aspect-[16/9] block overflow-hidden bg-gray-100">
                        <img 
                          v-if="rp.image_url" 
                          :src="rp.image_url" 
                          :alt="rp.title"
                          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                      </Link>
                      <div class="space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-widest text-gray-400">{{ rp.category?.name }}</span>
                        <Link :href="route('frontend.blog.show', rp.slug)" class="block">
                          <h4 class="text-lg font-bold text-black group-hover:text-gray-600 transition-colors">{{ rp.title }}</h4>
                        </Link>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="mt-20">
                  <Link :href="route('frontend.blog.index')" class="inline-flex items-center text-[10px] font-bold uppercase tracking-widest text-black hover:text-gray-500 transition-all border-b-2 border-black pb-1">
                    <ArrowLeft class="w-3 h-3 mr-2" /> Back to Journal
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </article>
  </TemplateWrapper>
</template>

<style>
/* Custom prose styles for better typography */
.prose blockquote {
  font-style: italic;
  border-left-width: 4px;
  border-left-color: #000;
  padding-left: 1.5rem;
  margin-left: 0;
  margin-right: 0;
}
.prose img {
  margin-top: 3rem;
  margin-bottom: 3rem;
}
</style>

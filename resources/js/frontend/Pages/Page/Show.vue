<script setup>
import { Link } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';

const props = defineProps({
  page: Object
});
</script>

<template>
  <TemplateWrapper 
    :title="page.meta?.title || page.title"
    :description="page.meta?.description"
    :keywords="page.meta?.keyword"
  >
    <div class="py-12 md:py-20 bg-white">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-4xl mx-auto space-y-12">
          <!-- Page Header -->
          <div class="space-y-6 text-center">
            <h1 class="text-4xl md:text-5xl font-bold text-black tracking-tight uppercase">
              {{ page.title }}
            </h1>
          </div>

          <!-- Featured Image -->
          <div v-if="page.image_url" class="aspect-video overflow-hidden bg-gray-100">
            <img 
              :src="page.image_url" 
              :alt="page.title"
              class="w-full h-full object-cover"
            />
          </div>

          <!-- Page Content -->
          <div class="prose prose-lg prose-black max-w-none" v-html="page.content"></div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

<style>
/* Custom prose styles */
.prose blockquote {
  font-style: italic;
  border-left-width: 4px;
  border-left-color: #000;
  padding-left: 1.5rem;
}
</style>

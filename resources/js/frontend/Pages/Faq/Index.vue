<script setup>
import { ref, computed } from 'vue';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { ChevronDown, HelpCircle } from 'lucide-vue-next';

const props = defineProps({
  faqs: [Object, Array]
});

const items = computed(() => {
  if (Array.isArray(props.faqs)) return props.faqs;
  return props.faqs?.data || [];
});

const openIndex = ref(null);

const toggle = (index) => {
  openIndex.value = openIndex.value === index ? null : index;
};
</script>

<template>
  <TemplateWrapper 
    title="FAQ"
    description="Frequently asked questions about our products, shipping, and services."
  >
    <div class="py-12 md:py-24 bg-white">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-3xl mx-auto space-y-12">
          <!-- Header -->
          <div class="text-center space-y-4">
            <h1 class="text-4xl md:text-5xl font-bold text-black tracking-tight uppercase">How can we help?</h1>
            <p class="text-gray-500 text-lg">Find answers to the most frequently asked questions below.</p>
          </div>

          <!-- FAQ Accordion -->
          <div class="space-y-4 pt-8">
            <div 
              v-for="(faq, index) in items" 
              :key="index"
              class="border border-gray-100 overflow-hidden"
            >
              <button 
                @click="toggle(index)"
                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-colors"
                :class="{ 'bg-gray-50': openIndex === index }"
              >
                <span class="font-bold text-black text-sm uppercase tracking-wider">{{ faq.question }}</span>
                <ChevronDown 
                  class="w-5 h-5 text-gray-400 transition-transform duration-300"
                  :class="{ 'rotate-180': openIndex === index }"
                />
              </button>
              
              <div 
                v-show="openIndex === index"
                class="p-6 pt-0 text-gray-500 text-sm leading-relaxed prose prose-sm max-w-none"
                v-html="faq.answer"
              ></div>
            </div>
          </div>

          <!-- Still need help? -->
          <div class="mt-20 p-12 bg-gray-50 text-center space-y-6">
            <div class="w-16 h-16 bg-white flex items-center justify-center mx-auto rounded-full shadow-sm">
              <HelpCircle class="w-6 h-6 text-black" />
            </div>
            <div class="space-y-2">
              <h3 class="text-xl font-bold text-black uppercase tracking-tight">Still have questions?</h3>
              <p class="text-gray-500 text-sm">If you couldn't find the answer you were looking for, please don't hesitate to reach out to our team.</p>
            </div>
            <a 
              href="mailto:support@example.com" 
              class="inline-block bg-black text-white px-8 py-4 text-[10px] font-bold uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg"
            >
              Contact Support
            </a>
          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

<style scoped>
.prose :first-child {
  margin-top: 0;
}
.prose :last-child {
  margin-bottom: 0;
}
</style>

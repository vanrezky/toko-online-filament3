<script setup>
import { computed, ref, watch } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { ShoppingBag, Heart, ShieldCheck, Truck, RefreshCw, ChevronRight, Plus, Minus, Warehouse, Scale } from 'lucide-vue-next';

const props = defineProps({
  product: Object
});

const page = usePage();
const selectedImage = ref(props.product.thumbnail);
const quantity = ref(1);
const selectedAttributes = ref({});
const activeFaq = ref(null);

const isWishlisted = computed(() => {
  return page.props.wishlist_product_ids?.includes(props.product.id);
});

const toggleWishlist = () => {
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

// Variant Logic
const hasVariants = computed(() => props.product.variants && props.product.variants.length > 0);

// Get unique attributes and their options for selection UI
const attributeGroups = computed(() => {
  if (!hasVariants.value) return {};
  
  const groups = {};
  props.product.variants.forEach(variant => {
    variant.attributes.forEach(attr => {
      if (!groups[attr.name]) {
        groups[attr.name] = new Set();
      }
      groups[attr.name].add(attr.option);
    });
  });
  
  // Convert sets to arrays
  Object.keys(groups).forEach(key => {
    groups[key] = Array.from(groups[key]);
  });
  
  return groups;
});

// Find the variant that matches all selected attributes
const selectedVariant = computed(() => {
  if (!hasVariants.value) return null;
  
  const selectedKeys = Object.keys(selectedAttributes.value);
  if (selectedKeys.length !== Object.keys(attributeGroups.value).length) return null;
  
  return props.product.variants.find(variant => {
    return variant.attributes.every(attr => 
      selectedAttributes.value[attr.name] === attr.option
    );
  });
});

const displayPrice = computed(() => {
  if (selectedVariant.value) return selectedVariant.value.price;
  return props.product.sale_price || props.product.price;
});

const displayStock = computed(() => {
  if (selectedVariant.value) return selectedVariant.value.stock;
  return props.product.stock;
});

const isSale = computed(() => !selectedVariant.value && !!props.product.sale_price);

// SEO helpers
const seoTitle = computed(() => props.product.meta?.title || props.product.name);
const seoDescription = computed(() => props.product.meta?.description || props.product.description?.substring(0, 160));
const seoKeywords = computed(() => props.product.meta?.keyword);

const updateQuantity = (val) => {
  const maxStock = selectedVariant.value ? selectedVariant.value.stock : props.product.stock;
  const newQty = quantity.value + val;
  if (newQty >= props.product.min_order && newQty <= maxStock) {
    quantity.value = newQty;
  }
};

const selectAttribute = (name, option) => {
  selectedAttributes.value[name] = option;
  quantity.value = 1; // Reset quantity on variant change
};

const toggleFaq = (index) => {
  activeFaq.value = activeFaq.value === index ? null : index;
};

const addToCart = () => {
  if (hasVariants.value && !selectedVariant.value) {
    alert('Please select all options before adding to bag.');
    return;
  }
  
  console.log('Adding to cart:', {
    product_uuid: props.product.uuid,
    variant_id: selectedVariant.value?.id,
    quantity: quantity.value,
  });
};
</script>

<template>
  <TemplateWrapper :title="seoTitle" :description="seoDescription" :keywords="seoKeywords">
    <div class="py-8 md:py-12">
      <div class="container mx-auto px-4 md:px-6">
        <!-- Breadcrumbs -->
        <nav class="flex items-center space-x-2 text-xs text-gray-400 mb-8 uppercase tracking-widest">
          <Link :href="route('frontend.home')" class="hover:text-black transition-colors">Home</Link>
          <ChevronRight class="w-3 h-3" />
          <Link :href="route('frontend.products')" class="hover:text-black transition-colors">Products</Link>
          <ChevronRight v-if="product.category" class="w-3 h-3" />
          <Link v-if="product.category" :href="route('frontend.products', { category: product.category.slug })" class="hover:text-black transition-colors">
            {{ product.category.name }}
          </Link>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
          <!-- Gallery -->
          <div class="lg:col-span-7 space-y-4">
            <div class="aspect-[4/5] bg-gray-50 overflow-hidden">
              <img 
                :src="selectedImage || 'https://placehold.co/800x1000?text=No+Image'" 
                :alt="product.name"
                class="w-full h-full object-cover"
              />
            </div>
            <div v-if="product.images && product.images.length > 1" class="grid grid-cols-5 gap-4">
              <button 
                v-for="(image, index) in product.images" 
                :key="index"
                @click="selectedImage = image"
                class="aspect-square bg-gray-50 overflow-hidden border-2 transition-all"
                :class="selectedImage === image ? 'border-black' : 'border-transparent opacity-60 hover:opacity-100'"
              >
                <img :src="image" class="w-full h-full object-cover" />
              </button>
            </div>
          </div>

          <!-- Product Info -->
          <div class="lg:col-span-5 space-y-8">
            <div class="space-y-4">
              <div v-if="product.category" class="text-xs font-bold uppercase tracking-[0.2em] text-gray-400">
                {{ product.category.name }}
              </div>
              <h1 class="text-3xl md:text-4xl font-bold text-black tracking-tight leading-tight">
                {{ product.name }}
              </h1>
              <div class="flex items-center space-x-4">
                <template v-if="isSale">
                  <span class="text-2xl font-bold text-black">{{ product.sale_price }}</span>
                  <span class="text-lg text-gray-400 line-through">{{ product.price }}</span>
                  <span v-if="product.discount_percentage" class="bg-black text-white text-[10px] font-bold px-2 py-1 uppercase tracking-wider">{{ product.discount_percentage }}% OFF</span>
                </template>
                <span v-else class="text-2xl font-bold text-black">{{ displayPrice }}</span>
              </div>
            </div>

            <!-- Description Short -->
            <p class="text-gray-500 text-sm leading-relaxed">
              {{ product.description }}
            </p>

            <div class="space-y-6 pt-6 border-t border-gray-100">
              <!-- Variant Selection -->
              <div v-if="hasVariants" class="space-y-6">
                <div v-for="(options, name) in attributeGroups" :key="name" class="space-y-3">
                  <label class="text-xs font-bold uppercase tracking-widest text-black flex justify-between">
                    <span>Select {{ name }}</span>
                    <span v-if="selectedAttributes[name]" class="text-gray-400">{{ selectedAttributes[name] }}</span>
                  </label>
                  <div class="flex flex-wrap gap-2">
                    <button 
                      v-for="option in options" :key="option"
                      @click="selectAttribute(name, option)"
                      class="px-4 py-2 text-xs font-bold border transition-all uppercase tracking-wider"
                      :class="selectedAttributes[name] === option ? 'border-black bg-black text-white' : 'border-gray-200 text-gray-500 hover:border-black hover:text-black'"
                    >
                      {{ option }}
                    </button>
                  </div>
                </div>
              </div>

              <!-- Shipping & Weight Info -->
              <div class="grid grid-cols-2 gap-4 py-4 px-6 bg-gray-50">
                <div class="flex items-center space-x-3">
                  <Warehouse class="w-4 h-4 text-gray-400" />
                  <div class="space-y-0.5">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Ship From</p>
                    <p class="text-xs font-bold text-black uppercase">{{ product.warehouse?.name || 'Main Hub' }}</p>
                  </div>
                </div>
                <div class="flex items-center space-x-3 border-l border-gray-200 pl-4">
                  <Scale class="w-4 h-4 text-gray-400" />
                  <div class="space-y-0.5">
                    <p class="text-[10px] text-gray-400 uppercase font-bold tracking-widest">Weight</p>
                    <p class="text-xs font-bold text-black uppercase">{{ (product.weight / 1000).toFixed(2) }}kg</p>
                  </div>
                </div>
              </div>

              <!-- Type Badge -->
              <div class="flex items-center space-x-2 text-xs font-medium uppercase tracking-wider">
                <span class="text-gray-400">Type:</span>
                <span class="text-black">{{ product.digital ? 'Digital Product' : 'Physical Product' }}</span>
              </div>

              <!-- Stock Status -->
              <div class="flex items-center space-x-2 text-xs font-medium uppercase tracking-wider">
                <span class="text-gray-400">Availability:</span>
                <span :class="displayStock > 0 ? 'text-green-600' : 'text-red-600'">
                  {{ displayStock > 0 ? `In Stock (${displayStock} units)` : 'Out of Stock' }}
                </span>
              </div>

              <!-- Quantity Selector -->
              <div class="space-y-3">
                <label class="text-xs font-bold uppercase tracking-widest text-black">Quantity</label>
                <div class="flex items-center w-32 border border-gray-200">
                  <button @click="updateQuantity(-1)" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 transition-colors">-</button>
                  <input type="number" v-model="quantity" readonly class="w-12 h-10 text-center border-none focus:ring-0 text-sm font-medium" />
                  <button @click="updateQuantity(1)" class="w-10 h-10 flex items-center justify-center hover:bg-gray-50 transition-colors">+</button>
                </div>
              </div>

              <!-- Actions -->
              <div class="flex items-stretch gap-4 pt-4">
                <button 
                  @click="addToCart"
                  :disabled="displayStock <= 0 || (hasVariants && !selectedVariant)"
                  class="flex-grow bg-black text-white py-4 px-6 text-xs md:text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition-all flex items-center justify-center space-x-3 disabled:bg-gray-300 disabled:cursor-not-allowed"
                >
                  <ShoppingBag class="w-5 h-5" />
                  <span>{{ hasVariants && !selectedVariant ? 'Select Options' : 'Add to Bag' }}</span>
                </button>
                <button 
                  @click="toggleWishlist"
                  class="size-14 shrink-0 border border-gray-200 flex items-center justify-center hover:bg-gray-50 transition-all group"
                >
                  <Heart 
                    class="w-6 h-6 transition-colors" 
                    :class="isWishlisted ? 'fill-red-500 text-red-500' : 'text-gray-400 group-hover:text-red-500'"
                  />
                </button>
              </div>
            </div>

            <!-- Features -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-8 border-t border-gray-100">
              <div class="flex items-start space-x-3">
                <Truck class="w-5 h-5 text-black" />
                <div class="space-y-1">
                  <h4 class="text-xs font-bold uppercase tracking-widest">Free Shipping</h4>
                  <p class="text-[10px] text-gray-500">On all orders over $150</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <RefreshCw class="w-5 h-5 text-black" />
                <div class="space-y-1">
                  <h4 class="text-xs font-bold uppercase tracking-widest">Easy Returns</h4>
                  <p class="text-[10px] text-gray-500">30-day return policy</p>
                </div>
              </div>
              <div class="flex items-start space-x-3">
                <ShieldCheck class="w-5 h-5 text-black" />
                <div class="space-y-1">
                  <h4 class="text-xs font-bold uppercase tracking-widest">Secure Payment</h4>
                  <p class="text-[10px] text-gray-500">100% secure checkout</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- FAQ Section -->
        <div v-if="product.faqs && product.faqs.length > 0" class="mt-20 md:mt-32 max-w-4xl mx-auto">
          <div class="text-center space-y-4 mb-12">
            <h2 class="text-2xl md:text-3xl font-bold text-black tracking-tight uppercase">Frequently Asked Questions</h2>
            <p class="text-gray-500 text-sm">Everything you need to know about {{ product.name }}.</p>
          </div>

          <div class="space-y-4">
            <div 
              v-for="(faq, index) in product.faqs" 
              :key="faq.id"
              class="border border-gray-100 overflow-hidden"
            >
              <button 
                @click="toggleFaq(index)"
                class="w-full flex items-center justify-between p-6 text-left hover:bg-gray-50 transition-colors focus:outline-none"
              >
                <span class="text-sm font-bold text-black uppercase tracking-widest">{{ faq.question }}</span>
                <Plus v-if="activeFaq !== index" class="w-4 h-4 text-gray-400" />
                <Minus v-else class="w-4 h-4 text-black" />
              </button>
              
              <transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-2"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-2"
              >
                <div v-if="activeFaq === index" class="p-6 pt-0 bg-white">
                  <div class="text-sm text-gray-500 leading-relaxed prose prose-sm max-w-none" v-html="faq.answer"></div>
                </div>
              </transition>
            </div>
          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

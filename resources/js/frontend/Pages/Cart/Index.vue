<script setup>
import { computed, getCurrentInstance } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { Trash2, ShoppingBag, ArrowRight, Minus, Plus } from 'lucide-vue-next';

const props = defineProps({
  cart: Object
});

const { proxy } = getCurrentInstance();

const items = computed(() => props.cart?.items || []);
const subtotal = computed(() => {
  return items.value.reduce((total, item) => total + (item.price * item.quantity), 0);
});

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(amount);
};

const updateQuantity = (item, delta) => {
  const newQty = item.quantity + delta;
  if (newQty < 1) return;

  router.patch(route('frontend.cart.update', item.id), {
    quantity: newQty
  }, {
    preserveScroll: true
  });
};

const removeItem = (id) => {
  proxy.$confirm({
    title: 'Remove Item',
    message: 'Are you sure you want to remove this item from your bag?',
    button: {
      no: 'Cancel',
      yes: 'Remove'
    },
    callback: (confirm) => {
      if (confirm) {
        router.delete(route('frontend.cart.destroy', id), {
          preserveScroll: true
        });
      }
    }
  });
};
</script>


<template>
  <TemplateWrapper title="Your Shopping Bag">
    <div class="py-12 md:py-20">
      <div class="container mx-auto px-4 md:px-6">
        <h1 class="text-3xl md:text-4xl font-bold text-black tracking-tight mb-12">Shopping Bag</h1>

        <div v-if="items.length > 0" class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
          <!-- Cart Items List -->
          <div class="lg:col-span-8 space-y-8">
            <div v-for="item in items" :key="item.id" class="flex py-8 border-b border-gray-100 first:pt-0 group">
              <!-- Item Image -->
              <div class="w-24 md:w-32 aspect-[4/5] bg-gray-50 overflow-hidden flex-shrink-0">
                <img
                  :src="item.product.thumbnail || 'https://placehold.co/200x250?text=No+Image'"
                  :alt="item.product.name"
                  class="w-full h-full object-cover"
                />
              </div>

              <!-- Item Details -->
              <div class="flex-grow ml-6 md:ml-8 flex flex-col justify-between">
                <div class="flex justify-between items-start">
                  <div class="space-y-1">
                    <h3 class="text-sm md:text-base font-bold text-black hover:text-gray-600 transition-colors">
                      <Link :href="route('frontend.product-detail', item.product.slug)">{{ item.product.name }}</Link>
                    </h3>
                    <p v-if="item.product_variant" class="text-xs text-gray-400 uppercase tracking-widest">
                      Variant: {{ item.product_variant.variant_name }}
                    </p>
                  </div>
                  <p class="text-sm md:text-base font-bold text-black">
                    {{ formatCurrency(item.price * item.quantity) }}
                  </p>
                </div>

                <div class="flex items-center justify-between mt-4">
                  <!-- Quantity Control -->
                  <div class="flex items-center border border-gray-100 h-10">
                    <button @click="updateQuantity(item, -1)" class="w-8 h-full flex items-center justify-center hover:bg-gray-50 transition-colors">
                      <Minus class="w-3 h-3" />
                    </button>
                    <span class="w-10 text-center text-xs font-bold">{{ item.quantity }}</span>
                    <button @click="updateQuantity(item, 1)" class="w-8 h-full flex items-center justify-center hover:bg-gray-50 transition-colors">
                      <Plus class="w-3 h-3" />
                    </button>
                  </div>

                  <!-- Remove Button -->
                  <button @click="removeItem(item.id)" class="text-xs font-bold uppercase tracking-widest text-gray-400 hover:text-red-500 transition-colors flex items-center space-x-2">
                    <Trash2 class="w-4 h-4" />
                    <span class="hidden sm:inline">Remove</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Order Summary Sidebar -->
          <div class="lg:col-span-4">
            <div class="bg-gray-50 p-8 space-y-8 sticky top-32">
              <h2 class="text-sm font-bold uppercase tracking-widest text-black">Order Summary</h2>

              <div class="space-y-4">
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Subtotal</span>
                  <span class="text-black font-medium">{{ formatCurrency(subtotal) }}</span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-gray-500">Estimated Shipping</span>
                  <span class="text-black font-medium">Calculated at checkout</span>
                </div>
                <div class="pt-4 border-t border-gray-200 flex justify-between">
                  <span class="text-base font-bold text-black uppercase tracking-widest">Total</span>
                  <span class="text-xl font-bold text-black">{{ formatCurrency(subtotal) }}</span>
                </div>
              </div>

              <Link
                :href="route('frontend.checkout')"
                class="block w-full bg-black text-white py-4 text-center text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg hover:shadow-xl flex items-center justify-center space-x-3"
              >
                <span>Proceed to Checkout</span>
                <ArrowRight class="w-4 h-4" />
              </Link>

              <div class="pt-4 space-y-3">
                <p class="text-[10px] text-gray-400 text-center uppercase tracking-widest">Secure payments with SSL encryption</p>
                <div class="flex justify-center space-x-4 opacity-30 grayscale">
                   <img src="https://cdn-icons-png.flaticon.com/512/196/196578.png" alt="Visa" class="h-4 w-auto" />
                   <img src="https://cdn-icons-png.flaticon.com/512/196/196561.png" alt="Mastercard" class="h-4 w-auto" />
                   <img src="https://cdn-icons-png.flaticon.com/512/196/196566.png" alt="PayPal" class="h-4 w-auto" />
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="py-20 text-center space-y-8">
          <div class="w-24 h-24 bg-gray-50 flex items-center justify-center mx-auto rounded-full">
            <ShoppingBag class="w-10 h-10 text-gray-300" />
          </div>
          <div class="space-y-3">
            <h2 class="text-2xl font-bold text-black">Your bag is empty</h2>
            <p class="text-gray-500 text-sm max-w-sm mx-auto">Looks like you haven't added anything to your bag yet. Let's find something for you.</p>
          </div>
          <Link :href="route('frontend.products')" class="inline-block bg-black text-white px-10 py-4 text-sm font-bold uppercase tracking-widest hover:bg-gray-800 transition-all shadow-lg">
            Start Shopping
          </Link>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

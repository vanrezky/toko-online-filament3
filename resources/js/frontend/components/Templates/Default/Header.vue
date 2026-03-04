<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { Search, ShoppingCart, User, Menu, X, ArrowLeft, Heart } from 'lucide-vue-next';

const { props } = usePage();
const settings = computed(() => props.settings);
const isMobileMenuOpen = ref(false);
const isSearchOpen = ref(false);
const searchQuery = ref('');

const wishlistCount = computed(() => usePage().props.wishlist_product_ids?.length || 0);

const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
  if (isMobileMenuOpen.value) isSearchOpen.value = false;
};

const toggleSearch = () => {
  isSearchOpen.value = !isSearchOpen.value;
  if (isSearchOpen.value) {
    isMobileMenuOpen.value = false;
    // Focus input after transition
    setTimeout(() => {
      const input = document.getElementById('desktop-search-input') || document.getElementById('mobile-search-input');
      input?.focus();
    }, 100);
  }
};

const handleSearch = () => {
  if (searchQuery.value.trim()) {
    router.get(route('frontend.products'), { search: searchQuery.value });
    isSearchOpen.value = false;
  }
};

const navItems = computed(() => [
  { name: 'Home', href: route('frontend.home') },
  { name: 'Products', href: route('frontend.products') },
  { name: 'Blogs', href: route('frontend.blog.index') },
  ...(usePage().props.menu?.header || []),
]);

const cartItemCount = computed(() => usePage().props.cart_total || 0); // This should be dynamic based on cart state
</script>

<template>
  <header class="sticky top-0 z-50 bg-white border-b border-gray-100 shadow-sm">
    <div class="container mx-auto px-4 md:px-6">
      <div class="flex items-center justify-between h-16 md:h-20">
        <!-- Mobile Menu Button -->
        <div class="flex items-center md:hidden">
          <button @click="toggleMobileMenu" class="p-2 text-gray-600 focus:outline-none">
            <Menu v-if="!isMobileMenuOpen" class="w-6 h-6" />
            <X v-else class="w-6 h-6" />
          </button>
        </div>

        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <Link :href="route('frontend.home')" class="flex items-center">
            <img v-if="settings.logo" :src="settings.logo" alt="Logo" class="h-8 md:h-10 w-auto" />
            <span v-else class="text-xl font-bold tracking-tight text-black uppercase">{{ settings.site_name }}</span>
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex items-center space-x-8">
          <Link
            v-for="item in navItems"
            :key="item.name"
            :href="item.href"
            class="text-[11px] font-bold uppercase tracking-[0.2em] text-gray-500 hover:text-black transition-colors"
          >
            {{ item.name }}
          </Link>
        </nav>

        <!-- Action Icons -->
        <div class="flex items-center space-x-2 md:space-x-6">
          <!-- Desktop Search Toggle -->
          <div class="hidden md:flex items-center">
            <div v-if="isSearchOpen" class="relative flex items-center animate-in fade-in slide-in-from-right-4 duration-300">
              <input
                id="desktop-search-input"
                v-model="searchQuery"
                type="text"
                placeholder="Search products..."
                class="w-64 bg-gray-50 border-b border-black py-1 px-2 text-sm focus:outline-none"
                @keyup.enter="handleSearch"
              />
              <button @click="toggleSearch" class="ml-2 text-gray-400 hover:text-black">
                <X class="w-4 h-4" />
              </button>
            </div>
            <button v-else @click="toggleSearch" class="text-gray-700 hover:text-black p-1 transition-transform hover:scale-110">
              <Search class="w-6 h-6" />
            </button>
          </div>

          <!-- Mobile Search Toggle -->
          <button @click="toggleSearch" class="md:hidden text-gray-700 hover:text-black p-2">
            <Search class="w-6 h-6" />
          </button>

          <Link :href="route('frontend.wishlist')" class="relative text-gray-700 hover:text-black p-1 transition-transform hover:scale-110">
            <Heart class="w-6 h-6" />
            <span v-if="wishlistCount > 0" class="absolute -top-1 -right-1 bg-black text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
              {{ wishlistCount }}
            </span>
          </Link>

          <Link :href="route('frontend.account')" class="text-gray-700 hover:text-black p-1 transition-transform hover:scale-110">
            <User class="w-6 h-6" />
          </Link>

          <Link :href="route('frontend.cart')" class="relative text-gray-700 hover:text-black p-1 transition-transform hover:scale-110">
            <ShoppingCart class="w-6 h-6" />
            <span v-if="cartItemCount > 0" class="absolute -top-1 -right-1 bg-black text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center font-bold">
              {{ cartItemCount }}
            </span>
          </Link>
        </div>
      </div>
    </div>

    <!-- Mobile Search Overlay (Below Header) -->
    <transition
      enter-active-class="transition duration-300 ease-out"
      enter-from-class="opacity-0 -translate-y-4"
      enter-to-class="opacity-100 translate-y-0"
      leave-active-class="transition duration-200 ease-in"
      leave-from-class="opacity-100 translate-y-0"
      leave-to-class="opacity-0 -translate-y-4"
    >
      <div v-if="isSearchOpen" class="absolute top-full left-0 w-full bg-white border-b border-gray-100 p-4 shadow-xl z-40 md:hidden">
        <div class="relative flex items-center">
          <input
            id="mobile-search-input"
            v-model="searchQuery"
            type="text"
            placeholder="Search our collection..."
            class="w-full bg-gray-50 border border-gray-200 py-4 px-6 text-sm focus:outline-none focus:border-black rounded-none transition-all"
            @keyup.enter="handleSearch"
          />
          <button @click="handleSearch" class="absolute right-4 text-black">
            <Search class="w-5 h-5" />
          </button>
        </div>
      </div>
    </transition>

    <!-- Mobile Menu Sidebar -->
    <div
      v-if="isMobileMenuOpen"
      class="fixed inset-0 bg-black/50 z-[60] backdrop-blur-sm"
      @click="isMobileMenuOpen = false"
    ></div>

    <div
      class="fixed top-0 left-0 bottom-0 w-[80%] max-w-sm bg-white z-[70] shadow-2xl transition-transform duration-300 ease-in-out transform"
      :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
    >
      <div class="p-6 flex flex-col h-full">
        <div class="flex items-center justify-between mb-12">
          <span class="text-xl font-bold tracking-tight uppercase">{{ settings.site_name }}</span>
          <button @click="isMobileMenuOpen = false" class="p-2">
            <X class="w-6 h-6" />
          </button>
        </div>

        <nav class="flex flex-col space-y-8">
          <Link
            v-for="item in navItems"
            :key="item.name"
            :href="item.href"
            @click="isMobileMenuOpen = false"
            class="text-xs font-bold uppercase tracking-[0.3em] text-black border-b border-gray-50 pb-4"
          >
            {{ item.name }}
          </Link>
          <Link
            :href="route('frontend.wishlist')"
            @click="isMobileMenuOpen = false"
            class="text-xs font-bold uppercase tracking-[0.3em] text-black border-b border-gray-50 pb-4 flex justify-between items-center"
          >
            <span>Wishlist</span>
            <span v-if="wishlistCount > 0" class="bg-black text-white px-2 py-0.5 rounded-full text-[10px]">{{ wishlistCount }}</span>
          </Link>
        </nav>

        <div class="mt-auto pt-8 border-t border-gray-100 space-y-6">
          <Link :href="route('frontend.account')" class="flex items-center space-x-4 text-xs font-bold uppercase tracking-widest text-gray-500">
            <User class="w-5 h-5" />
            <span>My Account</span>
          </Link>
          <div class="flex space-x-6">
            <!-- Social icons placeholder -->
            <div class="w-8 h-8 rounded-full bg-gray-50"></div>
            <div class="w-8 h-8 rounded-full bg-gray-50"></div>
            <div class="w-8 h-8 rounded-full bg-gray-50"></div>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>

<style scoped>
.animate-in {
  animation: 0.3s ease-out forwards;
}
.slide-in-from-right-4 {
  animation-name: slideInFromRight;
}
@keyframes slideInFromRight {
  from { opacity: 0; transform: translateX(1rem); }
  to { opacity: 1; transform: translateX(0); }
}
</style>

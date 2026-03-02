<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { Instagram, Facebook, Twitter, Mail } from 'lucide-vue-next';

const { props } = usePage();
const settings = computed(() => props.settings);
const currentYear = new Date().getFullYear();

const footerLinks = {
  customerService: [
    { name: 'Contact Us', href: '#' },
    { name: 'FAQ', href: '#' },
    { name: 'Shipping Info', href: '#' },
    { name: 'Returns & Exchanges', href: '#' },
    { name: 'Track Order', href: '#' },
  ],
  company: [
    { name: 'About Us', href: '#' },
    { name: 'Terms & Conditions', href: '#' },
    { name: 'Privacy Policy', href: '#' },
    { name: 'Cookies Policy', href: '#' },
  ]
};
</script>

<template>
  <footer class="bg-white border-t border-gray-100 pt-16 pb-8">
    <div class="container mx-auto px-4 md:px-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 md:gap-8">
        <!-- Brand Section -->
        <div class="space-y-6">
          <Link :href="route('frontend.home')" class="block">
            <img v-if="settings.logo" :src="settings.logo" alt="Logo" class="h-10 w-auto" />
            <span v-else class="text-2xl font-bold tracking-tight text-black">{{ settings.site_name }}</span>
          </Link>
          <p class="text-gray-500 text-sm leading-relaxed max-w-xs">
            Experience the finest in fashion and lifestyle. We bring you curated collections that blend quality, comfort, and timeless style.
          </p>
          <div class="flex space-x-5">
            <a href="#" class="text-gray-400 hover:text-black transition-colors">
              <Instagram class="w-5 h-5" />
            </a>
            <a href="#" class="text-gray-400 hover:text-black transition-colors">
              <Facebook class="w-5 h-5" />
            </a>
            <a href="#" class="text-gray-400 hover:text-black transition-colors">
              <Twitter class="w-5 h-5" />
            </a>
          </div>
        </div>

        <!-- Customer Service -->
        <div>
          <h3 class="text-sm font-bold uppercase tracking-widest text-black mb-6">Customer Service</h3>
          <ul class="space-y-4">
            <li v-for="link in footerLinks.customerService" :key="link.name">
              <a :href="link.href" class="text-gray-500 hover:text-black text-sm transition-colors">{{ link.name }}</a>
            </li>
          </ul>
        </div>

        <!-- Company -->
        <div>
          <h3 class="text-sm font-bold uppercase tracking-widest text-black mb-6">Company</h3>
          <ul class="space-y-4">
            <li v-for="link in footerLinks.company" :key="link.name">
              <a :href="link.href" class="text-gray-500 hover:text-black text-sm transition-colors">{{ link.name }}</a>
            </li>
          </ul>
        </div>

        <!-- Newsletter -->
        <div>
          <h3 class="text-sm font-bold uppercase tracking-widest text-black mb-6">Newsletter</h3>
          <p class="text-gray-500 text-sm mb-6">Subscribe to receive updates, access to exclusive deals, and more.</p>
          <form class="space-y-3" @submit.prevent>
            <div class="relative">
              <input 
                type="email" 
                placeholder="Enter your email" 
                class="w-full bg-gray-50 border border-gray-200 py-3 px-4 rounded-none focus:outline-none focus:border-black text-sm"
              />
              <button class="absolute right-0 top-0 h-full px-4 text-black hover:text-gray-600">
                <Mail class="w-5 h-5" />
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Bottom Bar -->
      <div class="mt-16 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0 text-xs text-gray-400">
        <p>&copy; {{ currentYear }} {{ settings.site_name }}. All rights reserved.</p>
        <div class="flex space-x-6">
          <span>Secure Payment:</span>
          <div class="flex space-x-3 opacity-50 grayscale">
            <img src="https://cdn-icons-png.flaticon.com/512/196/196578.png" alt="Visa" class="h-4 w-auto" />
            <img src="https://cdn-icons-png.flaticon.com/512/196/196561.png" alt="Mastercard" class="h-4 w-auto" />
            <img src="https://cdn-icons-png.flaticon.com/512/196/196566.png" alt="PayPal" class="h-4 w-auto" />
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

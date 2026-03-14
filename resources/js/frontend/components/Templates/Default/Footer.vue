<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { Instagram, Facebook, Twitter, Mail } from "lucide-vue-next";
import PromotionBanner from "../../UI/PromotionBanner.vue";

const { props } = usePage();
const settings = computed(() => props.settings);
const currentYear = new Date().getFullYear();

const customerServiceLinks = [
  { name: 'Contact Us', href: '#' },
  { name: 'FAQ', href: route('frontend.faq') },
  { name: 'Shipping Info', href: '#' },
  { name: 'Returns & Exchanges', href: '#' },
  { name: 'Track Order', href: '#' },
];

const companyLinks = computed(() => [...(usePage().props.menu?.footer || [])]);
const footerPromos = computed(() => {
  const promos = usePage().props.promotions;
  return (Array.isArray(promos) ? promos : promos?.data || []).filter(p => p.position === 'footer');
});
</script>

<template>
    <footer class="border-t border-gray-100 bg-white pb-8 pt-16">
        <!-- Promotions: Footer Position (New Section) -->
        <div v-if="footerPromos.length > 0" class="container mx-auto px-4 md:px-6 mb-12">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <PromotionBanner v-for="promo in footerPromos" :key="promo.id" :promotion="promo" class="aspect-[16/7] md:aspect-[21/9]" />
          </div>
        </div>
        <div class="container mx-auto px-4 md:px-6">
            <div class="grid grid-cols-1 gap-12 md:grid-cols-2 md:gap-8 lg:grid-cols-4">
                <!-- Brand Section -->
                <div class="space-y-6">
                    <Link :href="route('frontend.home')" class="block">
                        <img v-if="settings.logo" :src="settings.logo" alt="Logo" class="h-10 w-auto" />
                        <span v-else class="text-2xl font-bold tracking-tight text-black">{{ settings.site_name }}</span>
                    </Link>
                    <p class="max-w-xs text-sm leading-relaxed text-gray-500">
                        Experience the finest in fashion and lifestyle. We bring you curated collections that blend quality, comfort, and timeless
                        style.
                    </p>
                    <div class="flex space-x-5">
                        <a href="#" class="text-gray-400 transition-colors hover:text-black">
                            <Instagram class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-gray-400 transition-colors hover:text-black">
                            <Facebook class="h-5 w-5" />
                        </a>
                        <a href="#" class="text-gray-400 transition-colors hover:text-black">
                            <Twitter class="h-5 w-5" />
                        </a>
                    </div>
                </div>

                <!-- Customer Service -->
                <div>
                    <h3 class="mb-6 text-sm font-bold uppercase tracking-widest text-black">Customer Service</h3>
                    <ul class="space-y-4">
                        <li v-for="link in customerServiceLinks" :key="link.name">
                            <a :href="link.href" class="text-sm text-gray-500 transition-colors hover:text-black">{{ link.name }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Pages -->
                <div>
                    <h3 class="mb-6 text-sm font-bold uppercase tracking-widest text-black">Pages</h3>
                    <ul class="space-y-4">
                        <li v-for="link in companyLinks" :key="link.name">
                            <Link :href="link.href" class="text-sm text-gray-500 transition-colors hover:text-black">{{ link.name }}</Link>
                        </li>
                    </ul>
                </div>

                <!-- Newsletter -->
                <div>
                    <h3 class="mb-6 text-sm font-bold uppercase tracking-widest text-black">Newsletter</h3>
                    <p class="mb-6 text-sm text-gray-500">Subscribe to receive updates, access to exclusive deals, and more.</p>
                    <form class="space-y-3" @submit.prevent>
                        <div class="relative">
                            <input
                                type="email"
                                placeholder="Enter your email"
                                class="w-full rounded-none border border-gray-200 bg-gray-50 px-4 py-3 text-sm focus:border-black focus:outline-none"
                            />
                            <button class="absolute right-0 top-0 h-full px-4 text-black hover:text-gray-600">
                                <Mail class="h-5 w-5" />
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div
                class="mt-16 flex flex-col items-center justify-between space-y-4 border-t border-gray-100 pt-8 text-xs text-gray-400 md:flex-row md:space-y-0"
            >
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

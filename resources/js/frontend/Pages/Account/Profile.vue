<script setup>
import { Link } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { User, Package, MapPin, Settings, LogOut, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  user: Object
});

const menuItems = [
  { name: 'My Orders', icon: Package, href: route('frontend.orders'), description: 'Track, return, or buy things again' },
  { name: 'Shipping Addresses', icon: MapPin, href: '#', description: 'Edit addresses for orders' },
  { name: 'Account Settings', icon: Settings, href: '#', description: 'Edit login, name, and mobile number' },
];
</script>

<template>
  <TemplateWrapper title="My Account">
    <div class="py-12 md:py-20 bg-gray-50 min-h-screen">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-4xl mx-auto">
          <!-- User Header -->
          <div class="bg-white p-8 md:p-12 shadow-sm flex flex-col md:flex-row items-center md:items-start space-y-6 md:space-y-0 md:space-x-8 mb-12">
            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center border-4 border-gray-50">
              <User class="w-12 h-12 text-gray-400" />
            </div>
            <div class="text-center md:text-left space-y-2 flex-grow">
              <h1 class="text-2xl md:text-3xl font-bold text-black tracking-tight">{{ user.first_name }} {{ user.last_name }}</h1>
              <p class="text-gray-500 text-sm">{{ user.email }}</p>
              <div class="pt-4">
                <Link :href="route('logout')" method="post" as="button" class="text-xs font-bold uppercase tracking-widest text-red-500 hover:text-red-600 flex items-center justify-center md:justify-start">
                  <LogOut class="w-4 h-4 mr-2" /> Sign Out
                </Link>
              </div>
            </div>
          </div>

          <!-- Menu Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <Link 
              v-for="item in menuItems" 
              :key="item.name" 
              :href="item.href"
              class="bg-white p-8 shadow-sm hover:shadow-md transition-all group border border-transparent hover:border-black"
            >
              <div class="flex items-start space-x-6">
                <div class="p-3 bg-gray-50 group-hover:bg-black group-hover:text-white transition-colors">
                  <component :is="item.icon" class="w-6 h-6" />
                </div>
                <div class="space-y-1 flex-grow">
                  <div class="flex items-center justify-between">
                    <h3 class="text-sm font-bold text-black uppercase tracking-widest">{{ item.name }}</h3>
                    <ChevronRight class="w-4 h-4 text-gray-300 group-hover:text-black transition-colors" />
                  </div>
                  <p class="text-xs text-gray-500 leading-relaxed">{{ item.description }}</p>
                </div>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

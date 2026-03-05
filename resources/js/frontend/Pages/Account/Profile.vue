<script setup>
import { ref, computed, watch, getCurrentInstance } from 'vue';
import { Link, useForm, router, usePage } from '@inertiajs/vue3';
import TemplateWrapper from '../../components/TemplateWrapper.vue';
import { User, Package, MapPin, Settings, LogOut, ChevronRight, Camera, Save, X, Plus, Trash2, CheckCircle2 } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
  user: Object,
  addresses: Object,
  provinces: Array,
});

const page = usePage();
const { proxy } = getCurrentInstance();
const getSectionFromUrl = () => {
  const params = new URLSearchParams(window.location.search);
  return params.get('section') || 'overview';
};

const activeSection = ref(getSectionFromUrl()); // 'overview', 'settings', 'addresses', 'address_form'

// Watch for URL changes (e.g. when clicking a link that only changes query params)
watch(() => page.url, () => {
  activeSection.value = getSectionFromUrl();
});

const imagePreview = ref(null);
const fileInput = ref(null);
const editingAddress = ref(null);

const districts = ref([]);
const subDistricts = ref([]);

// Profile Form
const profileForm = useForm({
  first_name: props.user.first_name,
  last_name: props.user.last_name,
  email: props.user.email,
  phone: props.user.phone || '',
  image: null,
});

// Address Form
const addressForm = useForm({
  id: null,
  name: '',
  phone: '',
  province_id: '',
  district_id: '',
  sub_district_id: '',
  address: '',
  postal_code: '',
  is_featured: false,
});

const menuItems = [
  { id: 'overview', name: 'Overview', icon: User, description: 'Account summary and balance' },
  { id: 'orders', name: 'My Orders', icon: Package, href: route('frontend.orders'), description: 'Track, return, or buy things again' },
  { id: 'addresses', name: 'Shipping Addresses', icon: MapPin, description: 'Manage your delivery locations' },
  { id: 'settings', name: 'Account Settings', icon: Settings, description: 'Edit your profile and preferences' },
];

const handleImageChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    profileForm.image = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      imagePreview.value = e.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const submitProfile = () => {
  profileForm.post(route('frontend.account.update'), {
    preserveScroll: true,
    onSuccess: () => {
      imagePreview.value = null;
      profileForm.image = null;
      activeSection.value = 'overview';
    },
  });
};

// Address Logic
const openAddressForm = (address = null) => {
  if (address) {
    editingAddress.value = address;
    addressForm.id = address.id;
    addressForm.name = address.name;
    addressForm.phone = address.phone;
    addressForm.province_id = address.province_id;
    addressForm.district_id = address.district_id;
    addressForm.sub_district_id = address.sub_district_id;
    addressForm.address = address.address;
    addressForm.postal_code = address.postal_code;
    addressForm.is_featured = !!address.is_featured;

    // Fetch dependent data
    fetchDistricts(address.province_id, address.district_id);
    fetchSubDistricts(address.district_id, address.sub_district_id);
  } else {
    editingAddress.value = null;
    addressForm.reset();
    districts.value = [];
    subDistricts.value = [];
  }
  activeSection.value = 'address_form';
};

const fetchDistricts = async (provinceId, selectId = null) => {
  if (!provinceId) return;
  const res = await axios.get(route('frontend.regions.districts', provinceId));
  districts.value = res.data;
  if (!selectId) {
    addressForm.district_id = '';
    addressForm.sub_district_id = '';
    subDistricts.value = [];
  }
};

const fetchSubDistricts = async (districtId, selectId = null) => {
  if (!districtId) return;
  const res = await axios.get(route('frontend.regions.sub-districts', districtId));
  subDistricts.value = res.data;
  if (!selectId) addressForm.sub_district_id = '';
};

const submitAddress = () => {
  if (addressForm.id) {
    addressForm.patch(route('frontend.account.address.update', addressForm.id), {
      onSuccess: () => {
        activeSection.value = 'addresses';
        addressForm.reset();
        districts.value = [];
        subDistricts.value = [];
      },
    });
  } else {
    addressForm.post(route('frontend.account.address.store'), {
      onSuccess: () => {
        activeSection.value = 'addresses';
        addressForm.reset();
        districts.value = [];
        subDistricts.value = [];
      },
    });
  }
};

const deleteAddress = (id) => {
  proxy.$confirm({
    title: 'Delete Address',
    message: 'Are you sure you want to delete this address?',
    button: {
      no: 'Cancel',
      yes: 'Delete'
    },
    callback: (confirm) => {
      if (confirm) {
        router.delete(route('frontend.account.address.delete', id), {
          preserveScroll: true,
        });
      }
    }
  });
};

const setFeaturedAddress = (address) => {
    router.patch(route('frontend.account.address.update', address.id), {
        ...address,
        is_featured: true
    }, {
        preserveScroll: true
    });
};

const featuredAddress = computed(() => props.addresses?.find(a => a.is_featured));
</script>

<template>
  <TemplateWrapper title="My Account">
    <div class="py-12 md:py-20 bg-gray-50 min-h-screen font-sans">
      <div class="container mx-auto px-4 md:px-6">
        <div class="max-w-6xl mx-auto flex flex-col lg:flex-row gap-12">

          <!-- Sidebar Navigation -->
          <aside class="w-full lg:w-72 shrink-0">
            <div class="bg-white p-8 shadow-sm space-y-8 sticky top-32">
              <div class="flex items-center space-x-4 pb-8 border-b border-gray-100">
                <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-100 flex items-center justify-center bg-gray-50">
                  <img v-if="user.image || user.profile_photo_url" :src="user.image || user.profile_photo_url" class="w-full h-full object-cover" />
                  <User v-else class="w-6 h-6 text-gray-400" />
                </div>
                <div class="min-w-0">
                  <p class="text-sm font-bold text-black truncate">{{ user.full_name }}</p>
                  <p class="text-[10px] text-gray-400 uppercase tracking-widest truncate">{{ user.email }}</p>
                </div>
              </div>

              <nav class="space-y-2">
                <template v-for="item in menuItems" :key="item.id">
                  <Link v-if="item.href" :href="item.href" class="flex items-center space-x-4 p-3 text-xs font-bold uppercase tracking-widest text-gray-500 hover:text-black hover:bg-gray-50 transition-all">
                    <component :is="item.icon" class="w-4 h-4" />
                    <span>{{ item.name }}</span>
                  </Link>
                  <button
                    v-else
                    @click="activeSection = item.id"
                    class="flex items-center justify-between w-full p-3 text-xs font-bold uppercase tracking-widest transition-all"
                    :class="activeSection === item.id || (activeSection === 'address_form' && item.id === 'addresses') ? 'text-black bg-gray-50' : 'text-gray-500 hover:text-black hover:bg-gray-50'"
                  >
                    <div class="flex items-center space-x-4">
                      <component :is="item.icon" class="w-4 h-4" />
                      <span>{{ item.name }}</span>
                    </div>
                    <ChevronRight class="w-3 h-3" />
                  </button>
                </template>

                <Link :href="route('frontend.logout')" method="post" as="button" class="flex items-center space-x-4 w-full p-3 text-xs font-bold uppercase tracking-widest text-red-500 hover:bg-red-50 transition-all">
                  <LogOut class="w-4 h-4" />
                  <span>Sign Out</span>
                </Link>
              </nav>
            </div>
          </aside>

          <!-- Main Content Area -->
          <div class="flex-grow">

            <!-- OVERVIEW SECTION -->
            <div v-if="activeSection === 'overview'" class="space-y-8 animate-in fade-in duration-500">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-black text-white p-10 space-y-4">
                  <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Total Balance</p>
                  <h3 class="text-4xl font-bold italic tracking-tight">Rp 0</h3>
                  <button class="text-[10px] font-bold uppercase tracking-widest border-b border-white pb-1 hover:text-gray-300 hover:border-gray-300 transition-all">Top Up Wallet</button>
                </div>
                <div class="bg-white p-10 shadow-sm space-y-4 border border-gray-100">
                  <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-gray-400">Default Shipping</p>
                  <div v-if="featuredAddress" class="space-y-1">
                    <h4 class="text-sm font-bold text-black">{{ featuredAddress.name }}</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">{{ featuredAddress.address }}, {{ featuredAddress.sub_district_name }}, {{ featuredAddress.district_name }}, {{ featuredAddress.province_name }}</p>
                  </div>
                  <p v-else class="text-xs text-gray-400 italic">No default address set</p>
                  <button @click="activeSection = 'addresses'" class="text-[10px] font-bold uppercase tracking-widest border-b border-black pb-1 hover:text-gray-500 hover:border-gray-500 transition-all">Manage Addresses</button>
                </div>
              </div>

              <div class="bg-white p-10 shadow-sm border border-gray-100 space-y-8">
                <h3 class="text-sm font-bold uppercase tracking-[0.2em]">Recent Activity</h3>
                <div class="py-20 text-center space-y-4">
                   <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto">
                     <Package class="w-6 h-6 text-gray-300" />
                   </div>
                   <p class="text-xs text-gray-400 uppercase tracking-widest font-bold">No recent activity to show</p>
                </div>
              </div>
            </div>

            <!-- SETTINGS SECTION -->
            <div v-if="activeSection === 'settings'" class="bg-white p-8 md:p-12 shadow-sm border border-gray-100 animate-in fade-in slide-in-from-right-4 duration-500">
              <h2 class="text-2xl font-bold uppercase tracking-widest mb-12 italic">Account Settings</h2>

              <form @submit.prevent="submitProfile" class="space-y-12">
                <div class="flex flex-col items-center md:items-start space-y-6">
                  <div class="relative group">
                    <div class="w-32 h-32 rounded-full overflow-hidden border-4 border-gray-50 bg-gray-100 flex items-center justify-center shadow-inner">
                      <img v-if="imagePreview || user.image || user.profile_photo_url"
                           :src="imagePreview || user.image || user.profile_photo_url"
                           class="w-full h-full object-cover" />
                      <User v-else class="w-16 h-16 text-gray-300" />
                    </div>
                    <button type="button" @click="$refs.fileInput.click()" class="absolute inset-0 bg-black/40 flex items-center justify-center rounded-full opacity-0 group-hover:opacity-100 transition-opacity">
                      <Camera class="w-8 h-8 text-white" />
                    </button>
                    <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleImageChange" />
                  </div>
                  <div class="text-center md:text-left space-y-1">
                    <p class="text-xs font-bold uppercase tracking-widest">Profile Picture</p>
                    <p class="text-[10px] text-gray-400 uppercase tracking-widest">JPEG, PNG or WEBP. Max 2MB.</p>
                  </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">First Name</label>
                    <input v-model="profileForm.first_name" type="text" class="input-elegant" />
                    <p v-if="profileForm.errors.first_name" class="text-xs text-red-500">{{ profileForm.errors.first_name }}</p>
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Last Name</label>
                    <input v-model="profileForm.last_name" type="text" class="input-elegant" />
                    <p v-if="profileForm.errors.last_name" class="text-xs text-red-500">{{ profileForm.errors.last_name }}</p>
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Email Address</label>
                    <input v-model="profileForm.email" type="email" class="input-elegant" />
                    <p v-if="profileForm.errors.email" class="text-xs text-red-500">{{ profileForm.errors.email }}</p>
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Phone Number</label>
                    <input v-model="profileForm.phone" type="text" class="input-elegant" placeholder="+62..." />
                    <p v-if="profileForm.errors.phone" class="text-xs text-red-500">{{ profileForm.errors.phone }}</p>
                  </div>
                </div>

                <div class="pt-8 border-t border-gray-100 flex gap-4">
                  <button type="submit" :disabled="profileForm.processing" class="btn-primary flex items-center space-x-3 px-12">
                    <Save class="w-4 h-4" />
                    <span>Save Changes</span>
                  </button>
                  <button type="button" @click="activeSection = 'overview'" class="px-12 py-3 text-sm font-bold uppercase tracking-widest text-gray-400 hover:text-black transition-all">Cancel</button>
                </div>
              </form>
            </div>

            <!-- ADDRESSES LIST SECTION -->
            <div v-if="activeSection === 'addresses'" class="space-y-8 animate-in fade-in slide-in-from-right-4 duration-500">
              <div class="flex justify-between items-end mb-4">
                <div>
                  <h2 class="text-2xl font-bold uppercase tracking-widest italic">Shipping Addresses</h2>
                  <p class="text-gray-500 text-sm">Manage where your orders are delivered.</p>
                </div>
                <button @click="openAddressForm()" class="btn-primary flex items-center space-x-2 py-3 px-6">
                  <Plus class="w-4 h-4" />
                  <span>Add New</span>
                </button>
              </div>

              <div class="grid grid-cols-1 gap-6">
                <div v-for="address in (addresses || [])" :key="address.id"
                     class="bg-white p-8 shadow-sm border transition-all flex flex-col md:flex-row justify-between gap-6"
                     :class="address.is_featured ? 'border-black' : 'border-gray-100 hover:border-gray-200'">

                  <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                      <h3 class="font-bold text-black uppercase tracking-widest">{{ address.name }}</h3>
                      <span v-if="address.is_featured" class="bg-black text-white text-[8px] font-bold px-2 py-0.5 uppercase tracking-tighter">Default</span>
                    </div>
                    <div class="space-y-1">
                      <p class="text-xs text-gray-500 font-bold uppercase">{{ address.phone }}</p>
                      <p class="text-sm text-gray-600 leading-relaxed max-w-md">
                        {{ address.address }}<br />
                        {{ address.sub_district_name }}, {{ address.district_name }}<br />
                        {{ address.province_name }} {{ address.postal_code }}
                      </p>
                    </div>
                  </div>

                  <div class="flex flex-row md:flex-col justify-end gap-4 shrink-0">
                    <button v-if="!address.is_featured" @click="setFeaturedAddress(address)" class="text-[10px] font-bold uppercase tracking-widest text-blue-600 hover:underline text-right">Set as Default</button>
                    <div class="flex items-center gap-6 mt-auto">
                      <button @click="openAddressForm(address)" class="text-[10px] font-bold uppercase tracking-widest text-black border-b border-black hover:text-gray-500 hover:border-gray-500 transition-all">Edit</button>
                      <button @click="deleteAddress(address.id)" class="text-[10px] font-bold uppercase tracking-widest text-red-500 hover:text-red-700 transition-all">Delete</button>
                    </div>
                  </div>
                </div>

                <!-- Empty State -->
                <div v-if="!addresses || addresses.length === 0" class="bg-white py-20 text-center space-y-6 shadow-sm border border-dashed border-gray-200">
                  <MapPin class="w-12 h-12 text-gray-200 mx-auto" />
                  <p class="text-sm text-gray-400 uppercase font-bold tracking-widest">No addresses saved yet</p>
                  <button @click="openAddressForm()" class="btn-primary">Add your first address</button>
                </div>
              </div>
            </div>

            <!-- ADDRESS FORM SECTION -->
            <div v-if="activeSection === 'address_form'" class="bg-white p-8 md:p-12 shadow-sm border border-gray-100 animate-in fade-in slide-in-from-right-4 duration-500">
              <div class="flex items-center justify-between mb-12">
                <h2 class="text-2xl font-bold uppercase tracking-widest italic">{{ editingAddress ? 'Edit Address' : 'New Shipping Address' }}</h2>
                <button @click="activeSection = 'addresses'" class="text-gray-400 hover:text-black transition-colors"><X class="w-6 h-6" /></button>
              </div>

              <form @submit.prevent="submitAddress" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Receiver Name</label>
                    <input v-model="addressForm.name" type="text" class="input-elegant" placeholder="Full Name" />
                    <p v-if="addressForm.errors.name" class="text-xs text-red-500">{{ addressForm.errors.name }}</p>
                  </div>
                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Phone Number</label>
                    <input v-model="addressForm.phone" type="text" class="input-elegant" placeholder="08..." />
                    <p v-if="addressForm.errors.phone" class="text-xs text-red-500">{{ addressForm.errors.phone }}</p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Province</label>
                    <select v-model="addressForm.province_id" @change="fetchDistricts(addressForm.province_id)" class="input-elegant appearance-none">
                      <option value="" disabled>Select Province</option>
                      <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                    </select>
                    <p v-if="addressForm.errors.province_id" class="text-xs text-red-500">{{ addressForm.errors.province_id }}</p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">City / District</label>
                    <select v-model="addressForm.district_id" @change="fetchSubDistricts(addressForm.district_id)" :disabled="!addressForm.province_id" class="input-elegant appearance-none disabled:bg-gray-50">
                      <option value="" disabled>Select District</option>
                      <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
                    </select>
                    <p v-if="addressForm.errors.district_id" class="text-xs text-red-500">{{ addressForm.errors.district_id }}</p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Sub-district</label>
                    <select v-model="addressForm.sub_district_id" :disabled="!addressForm.district_id" class="input-elegant appearance-none disabled:bg-gray-50">
                      <option value="" disabled>Select Sub-district</option>
                      <option v-for="sd in subDistricts" :key="sd.id" :value="sd.id">{{ sd.name }}</option>
                    </select>
                    <p v-if="addressForm.errors.sub_district_id" class="text-xs text-red-500">{{ addressForm.errors.sub_district_id }}</p>
                  </div>

                  <div class="space-y-2">
                    <label class="text-[10px] font-bold uppercase tracking-widest text-black">Postal Code</label>
                    <input v-model="addressForm.postal_code" type="text" class="input-elegant" />
                    <p v-if="addressForm.errors.postal_code" class="text-xs text-red-500">{{ addressForm.errors.postal_code }}</p>
                  </div>
                </div>

                <div class="space-y-2">
                  <label class="text-[10px] font-bold uppercase tracking-widest text-black">Full Address</label>
                  <textarea v-model="addressForm.address" rows="3" class="input-elegant" placeholder="Street name, house number..."></textarea>
                  <p v-if="addressForm.errors.address" class="text-xs text-red-500">{{ addressForm.errors.address }}</p>
                </div>

                <div class="flex items-center space-x-3 py-4">
                  <input id="is_featured" type="checkbox" v-model="addressForm.is_featured" class="w-4 h-4 border-gray-300 text-black focus:ring-black rounded-none" />
                  <label for="is_featured" class="text-xs font-bold uppercase tracking-widest text-gray-500 cursor-pointer">Set as default shipping address</label>
                </div>

                <div class="pt-8 border-t border-gray-100 flex gap-4">
                  <button type="submit" :disabled="addressForm.processing" class="btn-primary flex items-center justify-center space-x-3 px-12">
                    <Save class="w-4 h-4" />
                    <span>{{ addressForm.processing ? 'Saving...' : 'Save Address' }}</span>
                  </button>
                  <button type="button" @click="activeSection = 'addresses'" class="px-12 py-3 text-sm font-bold uppercase tracking-widest text-gray-400 hover:text-black transition-all">Cancel</button>
                </div>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </TemplateWrapper>
</template>

<style scoped>
.input-elegant {
  @apply w-full border border-gray-200 bg-white px-4 py-3 text-sm focus:border-black focus:outline-none transition-all duration-300 rounded-none;
}
</style>


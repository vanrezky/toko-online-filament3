<script setup>
import { ref, computed, watch, getCurrentInstance } from "vue";
import { Link, useForm, router, usePage } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import {
    User,
    Package,
    MapPin,
    Settings,
    LogOut,
    ChevronRight,
    Camera,
    Save,
    X,
    Plus,
    Trash2,
    CheckCircle2,
    Edit3,
    Heart,
    Clock,
} from "lucide-vue-next";
import axios from "axios";

const props = defineProps({
    user: Object,
    addresses: Object,
    provinces: Array,
});

const page = usePage();
const { proxy } = getCurrentInstance();

const getSectionFromUrl = () => {
    const params = new URLSearchParams(window.location.search);
    return params.get("section") || "overview";
};

const activeSection = ref(getSectionFromUrl());

watch(
    () => page.url,
    () => {
        activeSection.value = getSectionFromUrl();
    },
);

const imagePreview = ref(null);
const fileInput = ref(null);
const editingAddress = ref(null);

const districts = ref([]);
const subDistricts = ref([]);
const villages = ref([]);

const profileForm = useForm({
    first_name: props.user.first_name,
    last_name: props.user.last_name,
    email: props.user.email,
    phone: props.user.phone || "",
    image: null,
});

const addressForm = useForm({
    id: null,
    name: "",
    phone: "",
    province_id: "",
    district_id: "",
    sub_district_id: "",
    village_id: "",
    address: "",
    postal_code: "",
    is_featured: false,
});

const menuItems = [
    { id: "overview", name: "Ringkasan", icon: User },
    { id: "orders", name: "Pesanan Saya", icon: Package, href: route("frontend.orders") },
    { id: "addresses", name: "Alamat Pengiriman", icon: MapPin },
    { id: "settings", name: "Pengaturan", icon: Settings },
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
    profileForm.post(route("frontend.account.update"), {
        preserveScroll: true,
        onSuccess: () => {
            imagePreview.value = null;
            profileForm.image = null;
            activeSection.value = "overview";
        },
    });
};

const openAddressForm = (address = null) => {
    if (address) {
        editingAddress.value = address;
        addressForm.id = address.id;
        addressForm.name = address.name;
        addressForm.phone = address.phone;
        addressForm.province_id = address.province_id;
        addressForm.district_id = address.district_id;
        addressForm.sub_district_id = address.sub_district_id;
        addressForm.village_id = address.village_id;
        addressForm.address = address.address;
        addressForm.postal_code = address.postal_code;
        addressForm.is_featured = !!address.is_featured;

        fetchDistricts(address.province_id, address.district_id);
        fetchSubDistricts(address.district_id, address.sub_district_id);
        fetchVillages(address.sub_district_id, address.village_id);
    } else {
        editingAddress.value = null;
        addressForm.reset();
        districts.value = [];
        subDistricts.value = [];
        villages.value = [];
    }
    activeSection.value = "address_form";
};

const fetchDistricts = async (provinceId, selectId = null) => {
    if (!provinceId) return;
    const res = await axios.get(route("frontend.regions.districts", provinceId));
    districts.value = res.data;
    if (!selectId) {
        addressForm.district_id = "";
        addressForm.sub_district_id = "";
        addressForm.village_id = "";
        subDistricts.value = [];
        villages.value = [];
    }
};

const fetchSubDistricts = async (districtId, selectId = null) => {
    if (!districtId) return;
    const res = await axios.get(route("frontend.regions.sub-districts", districtId));
    subDistricts.value = res.data;
    if (!selectId) {
        addressForm.sub_district_id = "";
        addressForm.village_id = "";
        villages.value = [];
    }
};

const fetchVillages = async (subDistrictId, selectId = null) => {
    if (!subDistrictId) return;
    const res = await axios.get(route("frontend.regions.villages", subDistrictId));
    villages.value = res.data;
    if (!selectId) addressForm.village_id = "";
};

const onVillageChange = () => {
    const village = villages.value.find((v) => v.id === addressForm.village_id);
    if (village && village.postal_code) {
        addressForm.postal_code = village.postal_code;
    }
};

const submitAddress = () => {
    if (addressForm.id) {
        addressForm.patch(route("frontend.account.address.update", addressForm.id), {
            onSuccess: () => {
                activeSection.value = "addresses";
                addressForm.reset();
                districts.value = [];
                subDistricts.value = [];
            },
        });
    } else {
        addressForm.post(route("frontend.account.address.store"), {
            onSuccess: () => {
                activeSection.value = "addresses";
                addressForm.reset();
                districts.value = [];
                subDistricts.value = [];
            },
        });
    }
};

const deleteAddress = (id, addressName) => {
    proxy.$confirm({
        title: "Hapus Alamat",
        message: `Apakah Anda yakin ingin menghapus alamat "${addressName}"?`,
        button: {
            no: "Batal",
            yes: "Ya, Hapus",
        },
        callback: (confirm) => {
            if (confirm) {
                router.delete(route("frontend.account.address.delete", id), {
                    preserveScroll: true,
                });
            }
        },
    });
};

const setFeaturedAddress = (address) => {
    router.patch(
        route("frontend.account.address.update", address.id),
        {
            ...address,
            is_featured: true,
        },
        {
            preserveScroll: true,
        },
    );
};

const featuredAddress = computed(() => props.addresses?.find((a) => a.is_featured));
</script>

<template>
    <TemplateWrapper title="Akun Saya">
        <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-secondary/50 via-white to-secondary/30 py-8 md:py-12">
            <!-- Decorative -->
            <div class="absolute -left-20 -top-20 h-80 w-80 rounded-full bg-primary/5 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-20 h-96 w-96 rounded-full bg-primary/5 blur-3xl"></div>

            <div class="container mx-auto px-4">
                <!-- Breadcrumb -->
                <div class="relative z-10 mb-6 flex items-center gap-2 text-sm text-muted-foreground">
                    <span class="cursor-pointer transition-colors hover:text-foreground" @click="$inertia.get(route('frontend.home'))">Beranda</span>
                    <ChevronRight class="h-4 w-4" />
                    <span class="font-medium text-foreground">Akun Saya</span>
                </div>

                <div class="relative z-10 mx-auto grid max-w-6xl grid-cols-1 gap-8 lg:grid-cols-[300px_1fr]">
                    <!-- Sidebar Navigation -->
                    <aside>
                        <div class="sticky top-24 space-y-4">
                            <!-- Profile Card -->
                            <div class="overflow-hidden rounded-2xl bg-white shadow-xl">
                                <!-- Header with Gradient -->
                                <div class="bg-gradient-to-r from-primary to-primary/80 p-6 pb-8">
                                    <div class="-mt-12 flex flex-col items-center text-center">
                                        <div class="relative">
                                            <div
                                                class="flex h-24 w-24 items-center justify-center overflow-hidden rounded-full border-4 border-white bg-secondary shadow-lg"
                                            >
                                                <img
                                                    v-if="user.image || user.profile_photo_url"
                                                    :src="user.image || user.profile_photo_url"
                                                    class="h-full w-full object-cover"
                                                />
                                                <User v-else class="h-10 w-10 text-muted-foreground" />
                                            </div>
                                            <div
                                                class="absolute -bottom-1 -right-1 flex h-8 w-8 items-center justify-center rounded-full border-2 border-white bg-primary text-primary-foreground shadow"
                                            >
                                                <CheckCircle2 class="h-4 w-4" />
                                            </div>
                                        </div>
                                        <p class="mt-3 font-bold text-white">{{ user.full_name }}</p>
                                        <p class="text-sm text-white/70">{{ user.email }}</p>
                                    </div>
                                </div>

                                <!-- Menu Items -->
                                <div class="p-4">
                                    <nav class="space-y-1">
                                        <template v-for="item in menuItems" :key="item.id">
                                            <Link
                                                v-if="item.href"
                                                :href="item.href"
                                                class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-muted-foreground transition-all hover:bg-secondary hover:text-foreground"
                                            >
                                                <component :is="item.icon" class="h-5 w-5" />
                                                <span>{{ item.name }}</span>
                                            </Link>
                                            <button
                                                v-else
                                                @click="activeSection = item.id"
                                                class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium transition-all"
                                                :class="
                                                    activeSection === item.id || (activeSection === 'address_form' && item.id === 'addresses')
                                                        ? 'bg-gradient-to-r from-primary to-primary/80 text-primary-foreground shadow-lg shadow-primary/20'
                                                        : 'text-muted-foreground hover:bg-secondary hover:text-foreground'
                                                "
                                            >
                                                <component :is="item.icon" class="h-5 w-5" />
                                                <span>{{ item.name }}</span>
                                            </button>
                                        </template>

                                        <Link
                                            :href="route('frontend.logout')"
                                            method="post"
                                            as="button"
                                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-sm font-medium text-red-500 transition-all hover:bg-red-50"
                                        >
                                            <LogOut class="h-5 w-5" />
                                            <span>Keluar</span>
                                        </Link>
                                    </nav>
                                </div>
                            </div>

                            <!-- Quick Links -->
                            <div class="overflow-hidden rounded-2xl bg-white p-4 shadow-lg">
                                <h4 class="mb-3 text-sm font-semibold text-foreground">Akses Cepat</h4>
                                <div class="space-y-2">
                                    <Link
                                        :href="route('frontend.wishlist')"
                                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-muted-foreground transition-all hover:bg-secondary hover:text-foreground"
                                    >
                                        <Heart class="h-4 w-4" />
                                        <span>Daftar Keinginan</span>
                                    </Link>
                                    <Link
                                        :href="route('frontend.orders')"
                                        class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm text-muted-foreground transition-all hover:bg-secondary hover:text-foreground"
                                    >
                                        <Clock class="h-4 w-4" />
                                        <span>Riwayat Pesanan</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <!-- Main Content Area -->
                    <div class="space-y-6">
                        <!-- OVERVIEW SECTION -->
                        <div v-if="activeSection === 'overview'" class="space-y-6">
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div
                                    class="group overflow-hidden rounded-2xl bg-gradient-to-br from-primary to-primary/80 p-6 shadow-xl shadow-primary/20 transition-all hover:shadow-2xl hover:shadow-primary/30"
                                >
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <div class="mb-2 flex items-center gap-2">
                                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/20">
                                                    <Package class="h-5 w-5 text-white" />
                                                </div>
                                                <span class="text-xs font-semibold uppercase tracking-wider text-white/80">Total Pesanan</span>
                                            </div>
                                            <h3 class="text-4xl font-bold text-white">0</h3>
                                        </div>
                                        <div class="rounded-2xl bg-white/20 p-3 backdrop-blur-sm transition-transform group-hover:scale-110">
                                            <Package class="h-8 w-8 text-white" />
                                        </div>
                                    </div>
                                </div>

                                <div class="group overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all hover:shadow-xl">
                                    <div class="mb-2 flex items-center gap-2">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10">
                                            <MapPin class="h-5 w-5 text-primary" />
                                        </div>
                                        <span class="text-xs font-semibold uppercase tracking-wider text-muted-foreground">Alamat Default</span>
                                    </div>
                                    <div v-if="featuredAddress" class="space-y-2">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-foreground">{{ featuredAddress.name }}</h4>
                                            <span class="rounded-full bg-primary/10 px-2 py-0.5 text-[10px] font-bold text-primary">Default</span>
                                        </div>
                                        <p class="line-clamp-2 text-sm text-muted-foreground">
                                            {{ featuredAddress.address }}, {{ featuredAddress.village_name }}, {{ featuredAddress.sub_district_name }}
                                        </p>
                                    </div>
                                    <div v-else class="py-2">
                                        <p class="mb-3 text-sm text-muted-foreground">Belum ada alamat default</p>
                                    </div>
                                    <button
                                        @click="activeSection = 'addresses'"
                                        class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-primary transition-colors hover:text-primary/80"
                                    >
                                        <Edit3 class="h-3 w-3" />
                                        Kelola Alamat
                                    </button>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="overflow-hidden rounded-2xl bg-white p-6 shadow-lg">
                                <h3 class="mb-6 flex items-center gap-2 text-lg font-bold text-foreground">
                                    <Clock class="h-5 w-5 text-primary" />
                                    Aktivitas Terbaru
                                </h3>
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <div
                                        class="mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-br from-secondary to-secondary/50 shadow-inner"
                                    >
                                        <Package class="h-10 w-10 text-muted-foreground" />
                                    </div>
                                    <p class="mb-2 font-semibold text-foreground">Belum ada aktivitas terbaru</p>
                                    <p class="text-sm text-muted-foreground">Mulai belanja untuk melihat aktivitas Anda di sini</p>
                                    <Link
                                        :href="route('frontend.products')"
                                        class="mt-6 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-6 py-3 text-sm font-semibold text-primary-foreground shadow-lg shadow-primary/30 transition-all hover:shadow-xl hover:shadow-primary/40"
                                    >
                                        Mulai Belanja
                                        <ChevronRight class="h-4 w-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- SETTINGS SECTION -->
                        <div v-if="activeSection === 'settings'" class="overflow-hidden rounded-2xl bg-white p-6 shadow-xl md:p-8">
                            <div class="mb-8">
                                <h2 class="text-xl font-bold text-foreground">Pengaturan Akun</h2>
                                <p class="mt-1 text-sm text-muted-foreground">Kelola informasi profil dan preferensi akun Anda</p>
                            </div>

                            <form @submit.prevent="submitProfile" class="space-y-8">
                                <!-- Avatar Section -->
                                <div class="flex flex-col items-center gap-4 md:items-start">
                                    <div class="group relative">
                                        <div
                                            class="flex h-28 w-28 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-secondary to-secondary/50 shadow-inner"
                                        >
                                            <img
                                                v-if="imagePreview || user.image || user.profile_photo_url"
                                                :src="imagePreview || user.image || user.profile_photo_url"
                                                class="h-full w-full object-cover"
                                            />
                                            <User v-else class="h-12 w-12 text-muted-foreground" />
                                        </div>
                                        <button
                                            type="button"
                                            @click="$refs.fileInput.click()"
                                            class="absolute inset-0 flex items-center justify-center rounded-full bg-gradient-to-t from-black/60 to-transparent opacity-0 transition-opacity group-hover:opacity-100"
                                        >
                                            <Camera class="h-8 w-8 text-white" />
                                        </button>
                                        <input type="file" ref="fileInput" class="hidden" accept="image/*" @change="handleImageChange" />
                                    </div>
                                    <div class="text-center md:text-left">
                                        <p class="text-sm font-semibold text-foreground">Foto Profil</p>
                                        <p class="text-xs text-muted-foreground">JPEG, PNG atau WEBP. Maks 2MB.</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Nama Depan</label>
                                        <input v-model="profileForm.first_name" type="text" class="input-field" />
                                        <p v-if="profileForm.errors.first_name" class="text-xs text-red-500">{{ profileForm.errors.first_name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Nama Belakang</label>
                                        <input v-model="profileForm.last_name" type="text" class="input-field" />
                                        <p v-if="profileForm.errors.last_name" class="text-xs text-red-500">{{ profileForm.errors.last_name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Email</label>
                                        <input v-model="profileForm.email" type="email" class="input-field" />
                                        <p v-if="profileForm.errors.email" class="text-xs text-red-500">{{ profileForm.errors.email }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Nomor Telepon</label>
                                        <input v-model="profileForm.phone" type="text" class="input-field" placeholder="08..." />
                                        <p v-if="profileForm.errors.phone" class="text-xs text-red-500">{{ profileForm.errors.phone }}</p>
                                    </div>
                                </div>

                                <div class="flex flex-col gap-3 border-t border-border pt-6 sm:flex-row">
                                    <button
                                        type="submit"
                                        :disabled="profileForm.processing"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-8 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/30 transition-all hover:shadow-xl hover:shadow-primary/40 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:shadow-lg"
                                    >
                                        <Save class="h-4 w-4" />
                                        <span>{{ profileForm.processing ? "Menyimpan..." : "Simpan" }}</span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="activeSection = 'overview'"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-white px-8 py-3 text-sm font-semibold text-foreground shadow-sm transition-all hover:bg-secondary"
                                    >
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- ADDRESSES LIST SECTION -->
                        <div v-if="activeSection === 'addresses'" class="space-y-6">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-foreground">Alamat Pengiriman</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">Kelola alamat pengiriman pesanan Anda</p>
                                </div>
                                <button
                                    @click="openAddressForm()"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-6 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/30 transition-all hover:shadow-xl hover:shadow-primary/40"
                                >
                                    <Plus class="h-4 w-4" />
                                    <span>Tambah Baru</span>
                                </button>
                            </div>

                            <div class="grid gap-4">
                                <div
                                    v-for="address in addresses || []"
                                    :key="address.id"
                                    class="group overflow-hidden rounded-2xl bg-white p-6 shadow-lg transition-all hover:shadow-xl"
                                    :class="address.is_featured ? 'ring-2 ring-primary' : ''"
                                >
                                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                                        <div class="space-y-2">
                                            <div class="flex items-center gap-2">
                                                <h3 class="font-bold text-foreground">{{ address.name }}</h3>
                                                <span
                                                    v-if="address.is_featured"
                                                    class="rounded-full bg-gradient-to-r from-primary to-primary/80 px-3 py-1 text-xs font-bold text-primary-foreground shadow-sm"
                                                    >Default</span
                                                >
                                            </div>
                                            <p class="text-sm font-medium text-primary">{{ address.phone }}</p>
                                            <p class="text-sm text-muted-foreground">
                                                {{ address.address }}<br />
                                                {{ address.village_name }}, {{ address.sub_district_name }}, {{ address.district_name }}<br />
                                                {{ address.province_name }} {{ address.postal_code }}
                                            </p>
                                        </div>

                                        <div class="flex flex-row items-center gap-4 md:flex-col md:items-end">
                                            <button
                                                v-if="!address.is_featured"
                                                @click="setFeaturedAddress(address)"
                                                class="inline-flex items-center gap-1 text-xs font-semibold text-primary transition-colors hover:text-primary/80"
                                            >
                                                <CheckCircle2 class="h-3 w-3" />
                                                Jadikan Default
                                            </button>
                                            <div class="flex items-center gap-3">
                                                <button
                                                    @click="openAddressForm(address)"
                                                    class="inline-flex items-center gap-1 text-sm font-medium text-foreground underline transition-colors hover:text-primary"
                                                >
                                                    <Edit3 class="h-3 w-3" />
                                                    Edit
                                                </button>
                                                <button
                                                    @click="deleteAddress(address.id, address.name)"
                                                    class="inline-flex items-center gap-1 text-sm font-medium text-red-500 transition-colors hover:text-red-700"
                                                >
                                                    <Trash2 class="h-3 w-3" />
                                                    Hapus
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    v-if="!addresses || addresses.length === 0"
                                    class="relative overflow-hidden rounded-3xl border-2 border-dashed border-border bg-gradient-to-br from-slate-50 to-slate-100 py-16 text-center"
                                >
                                    <div class="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-primary/5"></div>
                                    <div class="absolute -bottom-10 -left-10 h-32 w-32 rounded-full bg-primary/5"></div>

                                    <div class="relative z-10">
                                        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-white shadow-lg">
                                            <MapPin class="h-10 w-10 text-muted-foreground" />
                                        </div>
                                        <p class="mb-6 text-sm font-medium text-muted-foreground">Belum ada alamat tersimpan</p>
                                        <button
                                            @click="openAddressForm()"
                                            class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-6 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/30 transition-all hover:shadow-xl hover:shadow-primary/40"
                                        >
                                            <Plus class="h-4 w-4" />
                                            Tambah Alamat Pertama
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ADDRESS FORM SECTION -->
                        <div v-if="activeSection === 'address_form'" class="overflow-hidden rounded-2xl bg-white p-6 shadow-xl md:p-8">
                            <div class="mb-8 flex items-center justify-between">
                                <div>
                                    <h2 class="text-xl font-bold text-foreground">{{ editingAddress ? "Edit Alamat" : "Alamat Pengiriman Baru" }}</h2>
                                    <p class="mt-1 text-sm text-muted-foreground">
                                        {{ editingAddress ? "Perbarui informasi alamat Anda" : "Tambahkan alamat baru untuk pengiriman" }}
                                    </p>
                                </div>
                                <button
                                    @click="activeSection = 'addresses'"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-secondary text-foreground shadow-sm transition-all hover:bg-primary hover:text-primary-foreground"
                                >
                                    <X class="h-5 w-5" />
                                </button>
                            </div>

                            <form @submit.prevent="submitAddress" class="space-y-6">
                                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Nama Penerima</label>
                                        <input v-model="addressForm.name" type="text" class="input-field" placeholder="Nama lengkap" />
                                        <p v-if="addressForm.errors.name" class="text-xs text-red-500">{{ addressForm.errors.name }}</p>
                                    </div>
                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Nomor Telepon</label>
                                        <input v-model="addressForm.phone" type="text" class="input-field" placeholder="08..." />
                                        <p v-if="addressForm.errors.phone" class="text-xs text-red-500">{{ addressForm.errors.phone }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Provinsi</label>
                                        <select
                                            v-model="addressForm.province_id"
                                            @change="fetchDistricts(addressForm.province_id)"
                                            class="select-field"
                                        >
                                            <option value="" disabled>Pilih Provinsi</option>
                                            <option v-for="p in provinces" :key="p.id" :value="p.id">{{ p.name }}</option>
                                        </select>
                                        <p v-if="addressForm.errors.province_id" class="text-xs text-red-500">{{ addressForm.errors.province_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Kabupaten/Kota</label>
                                        <select
                                            v-model="addressForm.district_id"
                                            @change="fetchSubDistricts(addressForm.district_id)"
                                            :disabled="!addressForm.province_id"
                                            class="select-field"
                                        >
                                            <option value="" disabled>Pilih Kabupaten/Kota</option>
                                            <option v-for="d in districts" :key="d.id" :value="d.id">{{ d.name }}</option>
                                        </select>
                                        <p v-if="addressForm.errors.district_id" class="text-xs text-red-500">{{ addressForm.errors.district_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Kecamatan</label>
                                        <select
                                            v-model="addressForm.sub_district_id"
                                            @change="fetchVillages(addressForm.sub_district_id)"
                                            :disabled="!addressForm.district_id"
                                            class="select-field"
                                        >
                                            <option value="" disabled>Pilih Kecamatan</option>
                                            <option v-for="sd in subDistricts" :key="sd.id" :value="sd.id">{{ sd.name }}</option>
                                        </select>
                                        <p v-if="addressForm.errors.sub_district_id" class="text-xs text-red-500">
                                            {{ addressForm.errors.sub_district_id }}
                                        </p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Kelurahan/Desa</label>
                                        <select
                                            v-model="addressForm.village_id"
                                            @change="onVillageChange"
                                            :disabled="!addressForm.sub_district_id"
                                            class="select-field"
                                        >
                                            <option value="" disabled>Pilih Kelurahan/Desa</option>
                                            <option v-for="v in villages" :key="v.id" :value="v.id">{{ v.name }}</option>
                                        </select>
                                        <p v-if="addressForm.errors.village_id" class="text-xs text-red-500">{{ addressForm.errors.village_id }}</p>
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-sm font-semibold text-foreground">Kode Pos</label>
                                        <input
                                            v-model="addressForm.postal_code"
                                            type="text"
                                            inputmode="numeric"
                                            @input="addressForm.postal_code = addressForm.postal_code.replace(/\D/g, '')"
                                            class="input-field"
                                            placeholder="12345"
                                        />
                                        <p v-if="addressForm.errors.postal_code" class="text-xs text-red-500">{{ addressForm.errors.postal_code }}</p>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-sm font-semibold text-foreground">Alamat Lengkap</label>
                                    <textarea
                                        v-model="addressForm.address"
                                        rows="3"
                                        class="input-field"
                                        placeholder="Nama jalan, nomor rumah..."
                                    ></textarea>
                                    <p v-if="addressForm.errors.address" class="text-xs text-red-500">{{ addressForm.errors.address }}</p>
                                </div>

                                <div class="flex items-center gap-3 rounded-xl bg-secondary/50 p-4">
                                    <input
                                        id="is_featured"
                                        type="checkbox"
                                        v-model="addressForm.is_featured"
                                        class="h-5 w-5 rounded border-border bg-white text-primary focus:ring-primary"
                                    />
                                    <label for="is_featured" class="cursor-pointer text-sm font-medium">Jadikan alamat default</label>
                                </div>

                                <div class="flex flex-col gap-3 border-t border-border pt-6 sm:flex-row">
                                    <button
                                        type="submit"
                                        :disabled="addressForm.processing"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-primary/90 px-8 py-3 text-sm font-bold text-primary-foreground shadow-lg shadow-primary/30 transition-all hover:shadow-xl hover:shadow-primary/40 active:scale-95 disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:shadow-lg"
                                    >
                                        <Save class="h-4 w-4" />
                                        <span>{{ addressForm.processing ? "Menyimpan..." : "Simpan Alamat" }}</span>
                                    </button>
                                    <button
                                        type="button"
                                        @click="activeSection = 'addresses'"
                                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-border bg-white px-8 py-3 text-sm font-semibold text-foreground shadow-sm transition-all hover:bg-secondary"
                                    >
                                        Batal
                                    </button>
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
.input-field {
    @apply w-full rounded-xl border border-border bg-gradient-to-r from-secondary/30 to-secondary/10 px-4 py-3.5 text-sm transition-all focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20;
}

.select-field {
    @apply w-full appearance-none rounded-xl border border-border bg-gradient-to-r from-secondary/30 to-secondary/10 px-4 py-3.5 text-sm transition-all focus:border-primary focus:bg-white focus:outline-none focus:ring-2 focus:ring-primary/20 disabled:cursor-not-allowed disabled:opacity-60;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}
</style>

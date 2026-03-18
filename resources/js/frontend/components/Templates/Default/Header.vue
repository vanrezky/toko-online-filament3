<script setup>
import { Link, usePage, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { Search, ShoppingCart, User, Menu, X } from "lucide-vue-next";

const { props } = usePage();
const settings = computed(() => props.settings);
const isMobileMenuOpen = ref(false);
const searchQuery = ref("");
const isSearchFocused = ref(false);

const cartItemCount = computed(() => usePage().props.cart_total || 0);
const auth = computed(() => usePage().props.auth);
const isLoggedIn = computed(() => !!auth.value?.user);

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

const handleSearch = () => {
    if (searchQuery.value.trim()) {
        router.get(
            route("frontend.home"),
            { search: searchQuery.value },
            {
                preserveState: true,
                preserveScroll: true,
            },
        );
    }
};

const clearSearch = () => {
    searchQuery.value = "";
    router.get(
        route("frontend.home"),
        {},
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <header class="sticky top-0 z-50 border-b border-border bg-white shadow-sm">
        <div class="container mx-auto px-4">
            <div class="flex h-14 items-center justify-between md:h-16">
                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="toggleMobileMenu" class="p-2 text-foreground focus:outline-none">
                        <Menu class="h-6 w-6" />
                    </button>
                </div>

                <!-- Logo -->
                <div class="flex flex-shrink-0 items-center">
                    <Link :href="route('frontend.home')" class="flex items-center gap-2">
                        <span v-if="settings.site_name" class="text-lg font-bold text-primary md:text-xl">
                            {{ settings.site_name }}
                        </span>
                        <span v-else class="text-lg font-bold text-primary md:text-xl">UMKM</span>
                    </Link>
                </div>

                <!-- Search Bar (Desktop) -->
                <div class="mx-8 hidden max-w-xl flex-grow md:flex">
                    <div class="relative w-full">
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Cari produk..."
                            class="w-full rounded-full border border-border bg-secondary py-2.5 pl-10 pr-10 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                            @keyup.enter="handleSearch"
                            @focus="isSearchFocused = true"
                            @blur="isSearchFocused = false"
                        />
                        <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                        <button
                            v-if="searchQuery"
                            @click="clearSearch"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                        >
                            <X class="h-4 w-4" />
                        </button>
                    </div>
                </div>

                <!-- Action Icons -->
                <div class="flex items-center space-x-2 md:space-x-4">
                    <!-- User Icon -->
                    <Link :href="route('frontend.account')" class="hidden p-2 text-foreground/70 transition-colors hover:text-primary md:block">
                        <User class="h-5 w-5" />
                    </Link>

                    <!-- Cart Icon -->
                    <Link :href="route('frontend.cart')" class="relative p-2 text-foreground/70 transition-colors hover:text-primary">
                        <ShoppingCart class="h-5 w-5" />
                        <span
                            v-if="cartItemCount > 0"
                            class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-destructive text-[10px] font-bold text-white"
                        >
                            {{ cartItemCount > 99 ? "99+" : cartItemCount }}
                        </span>
                    </Link>
                </div>
            </div>

            <!-- Mobile Search Bar -->
            <div class="pb-3 md:hidden">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        placeholder="Cari produk..."
                        class="w-full rounded-full border border-border bg-secondary py-2.5 pl-10 pr-10 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                        @keyup.enter="handleSearch"
                    />
                    <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                    <button
                        v-if="searchQuery"
                        @click="clearSearch"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                    >
                        <X class="h-4 w-4" />
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Sidebar -->
        <div v-if="isMobileMenuOpen" class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm" @click="isMobileMenuOpen = false"></div>

        <div
            class="fixed bottom-0 left-0 top-0 z-[70] w-[80%] max-w-xs transform bg-white shadow-2xl transition-transform duration-300 ease-in-out"
            :class="isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
        >
            <div class="flex h-full flex-col p-6">
                <div class="mb-8 flex items-center justify-between">
                    <span class="text-xl font-bold text-primary">{{ settings.site_name || "UMKM" }}</span>
                    <button @click="isMobileMenuOpen = false" class="p-2 text-foreground">
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <nav class="flex flex-col space-y-4">
                    <Link
                        :href="route('frontend.home')"
                        @click="isMobileMenuOpen = false"
                        class="border-b border-border/50 py-2 text-sm font-semibold text-foreground"
                    >
                        Beranda
                    </Link>
                    <Link
                        :href="route('frontend.products')"
                        @click="isMobileMenuOpen = false"
                        class="border-b border-border/50 py-2 text-sm font-semibold text-foreground"
                    >
                        Produk
                    </Link>
                    <Link
                        :href="route('frontend.cart')"
                        @click="isMobileMenuOpen = false"
                        class="flex items-center justify-between border-b border-border/50 py-2 text-sm font-semibold text-foreground"
                    >
                        <span>Keranjang</span>
                        <span v-if="cartItemCount > 0" class="rounded-full bg-primary px-2 py-0.5 text-xs text-primary-foreground">{{
                            cartItemCount
                        }}</span>
                    </Link>
                    <Link
                        :href="route('frontend.wishlist')"
                        @click="isMobileMenuOpen = false"
                        class="border-b border-border/50 py-2 text-sm font-semibold text-foreground"
                    >
                        Wishlist
                    </Link>
                </nav>

                <div class="mt-auto border-t border-border pt-8">
                    <Link
                        :href="route('frontend.account')"
                        class="flex items-center space-x-3 text-sm font-semibold text-foreground/70 hover:text-primary"
                    >
                        <User class="h-5 w-5" />
                        <span>{{ isLoggedIn ? "Akun Saya" : "Masuk / Daftar" }}</span>
                    </Link>
                </div>
            </div>
        </div>
    </header>
</template>

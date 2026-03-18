<script setup>
import { ref, watch, computed } from "vue";
import { router } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import ProductCard from "../../components/UI/ProductCard.vue";
import { Search, X, Loader2, SlidersHorizontal } from "lucide-vue-next";
import debounce from "lodash/debounce";

const props = defineProps({
    products: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search || "");
const selectedCategory = ref(props.filters.category || "");
const selectedSort = ref(props.filters.sort || "newest");
const priceMin = ref(props.filters.price_min || "");
const priceMax = ref(props.filters.price_max || "");
const isFilterOpen = ref(false);
const isLoadingMore = ref(false);
const allProducts = ref([...props.products.data]);

watch(
    () => props.products.data,
    (newData) => {
        if (!isLoadingMore.value) {
            allProducts.value = [...newData];
        }
    },
);

const applyFilters = debounce(() => {
    if (priceMin.value && parseFloat(priceMin.value) < 0) {
        priceMin.value = "";
    }
    if (priceMax.value && parseFloat(priceMax.value) < 0) {
        priceMax.value = "";
    }

    router.get(
        route("frontend.products"),
        {
            search: search.value || null,
            category: selectedCategory.value || null,
            sort: selectedSort.value,
            price_min: priceMin.value || null,
            price_max: priceMax.value || null,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
            onStart: () => {
                isLoadingMore.value = false;
            },
        },
    );
}, 300);

watch([search, selectedCategory, selectedSort, priceMin, priceMax], () => {
    applyFilters();
});

const loadMore = () => {
    if (props.products.links.next && !isLoadingMore.value) {
        isLoadingMore.value = true;
        router.get(
            props.products.links.next,
            {
                search: search.value,
                category: selectedCategory.value,
                sort: selectedSort.value,
                price_min: priceMin.value,
                price_max: priceMax.value,
            },
            {
                preserveState: true,
                preserveScroll: true,
                only: ["products"],
                onSuccess: (page) => {
                    allProducts.value = [...allProducts.value, ...page.props.products.data];
                    isLoadingMore.value = false;
                },
                onFinish: () => {
                    isLoadingMore.value = false;
                },
            },
        );
    }
};

const resetFilters = () => {
    search.value = "";
    selectedCategory.value = "";
    selectedSort.value = "newest";
    priceMin.value = "";
    priceMax.value = "";
};

const totalProducts = computed(() => props.products.total || allProducts.value.length);
const hasActiveFilters = computed(() => {
    return search.value || selectedCategory.value || priceMin.value || priceMax.value;
});
</script>

<template>
    <TemplateWrapper title="Semua Produk">
        <div class="bg-secondary/30 py-8 md:py-12">
            <div class="container mx-auto px-4">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-foreground md:text-3xl">Semua Produk</h1>
                        <p class="mt-1 text-sm text-muted-foreground">{{ totalProducts }} produk tersedia</p>
                    </div>

                    <button
                        @click="isFilterOpen = !isFilterOpen"
                        class="flex items-center justify-center gap-2 rounded-lg border border-border bg-white px-4 py-2.5 text-sm font-medium md:hidden"
                    >
                        <SlidersHorizontal class="h-4 w-4" />
                        Filter
                        <span
                            v-if="hasActiveFilters"
                            class="flex h-5 w-5 items-center justify-center rounded-full bg-primary text-[10px] text-primary-foreground"
                            >!</span
                        >
                    </button>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-[280px_1fr]">
                    <!-- Filters Sidebar -->
                    <aside :class="['rounded-xl bg-white p-6', isFilterOpen ? 'block' : 'hidden md:block']">
                        <div class="mb-6 flex items-center justify-between">
                            <h3 class="text-lg font-semibold">Filter & Urutan</h3>
                            <button v-if="hasActiveFilters" @click="resetFilters" class="text-xs font-medium text-primary hover:underline">
                                Reset
                            </button>
                        </div>

                        <!-- Search -->
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-medium">Pencarian</label>
                            <div class="relative">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari produk..."
                                    class="w-full rounded-lg border border-border bg-secondary py-2.5 pl-10 pr-4 text-sm transition-all focus:outline-none focus:ring-2 focus:ring-primary/20"
                                />
                                <Search class="absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-muted-foreground" />
                                <button
                                    v-if="search"
                                    @click="search = ''"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground"
                                >
                                    <X class="h-4 w-4" />
                                </button>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-6">
                            <label class="mb-2 block text-sm font-medium">Urutkan</label>
                            <select
                                v-model="selectedSort"
                                class="w-full cursor-pointer rounded-lg border border-border bg-secondary px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="newest">Terbaru</option>
                                <option value="price_low">Harga: Rendah ke Tinggi</option>
                                <option value="price_high">Harga: Tinggi ke Rendah</option>
                                <option value="name_asc">Nama: A-Z</option>
                                <option value="name_desc">Nama: Z-A</option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-6">
                            <h4 class="mb-3 text-sm font-semibold">Kategori</h4>
                            <div class="space-y-2">
                                <label class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" v-model="selectedCategory" value="" class="h-4 w-4 accent-primary" />
                                    <span class="text-sm">Semua Kategori</span>
                                </label>
                                <label v-for="category in categories" :key="category.id" class="flex cursor-pointer items-center gap-2">
                                    <input type="radio" v-model="selectedCategory" :value="category.slug" class="h-4 w-4 accent-primary" />
                                    <span class="text-sm">{{ category.name }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Price Filter -->
                        <div class="mb-6">
                            <h4 class="mb-3 text-sm font-semibold">Harga</h4>
                            <div class="flex items-center gap-2">
                                <input
                                    v-model="priceMin"
                                    type="number"
                                    min="0"
                                    placeholder="Min"
                                    class="w-full rounded-lg border border-border bg-secondary px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"
                                />
                                <span class="text-muted-foreground">-</span>
                                <input
                                    v-model="priceMax"
                                    type="number"
                                    min="0"
                                    placeholder="Max"
                                    class="w-full rounded-lg border border-border bg-secondary px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary/20"
                                />
                            </div>
                        </div>

                        <!-- Active Filters Tags -->
                        <div v-if="hasActiveFilters" class="border-t border-border pt-4">
                            <h4 class="mb-3 text-sm font-semibold">Filter Aktif</h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-if="search"
                                    class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                                >
                                    "{{ search }}"
                                    <button @click="search = ''" class="hover:text-primary/70">
                                        <X class="h-3 w-3" />
                                    </button>
                                </span>
                                <span
                                    v-if="selectedCategory"
                                    class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                                >
                                    {{ categories.find((c) => c.slug === selectedCategory)?.name }}
                                    <button @click="selectedCategory = ''" class="hover:text-primary/70">
                                        <X class="h-3 w-3" />
                                    </button>
                                </span>
                                <span
                                    v-if="priceMin || priceMax"
                                    class="inline-flex items-center gap-1 rounded-full bg-primary/10 px-3 py-1 text-xs font-medium text-primary"
                                >
                                    Rp{{ priceMin || "0" }} - Rp{{ priceMax || "∞" }}
                                    <button
                                        @click="
                                            priceMin = '';
                                            priceMax = '';
                                        "
                                        class="hover:text-primary/70"
                                    >
                                        <X class="h-3 w-3" />
                                    </button>
                                </span>
                            </div>
                        </div>
                    </aside>

                    <!-- Product Grid -->
                    <div>
                        <div v-if="allProducts.length > 0" class="grid grid-cols-2 gap-4 md:gap-6 lg:grid-cols-3 xl:grid-cols-4">
                            <ProductCard v-for="product in allProducts" :key="product.uuid || product.id" :product="product" />
                        </div>

                        <div v-else class="py-20 text-center">
                            <div class="mb-4 text-6xl">📭</div>
                            <h3 class="mb-2 text-xl font-bold text-foreground">Produk tidak ditemukan</h3>
                            <p class="mb-6 text-sm text-muted-foreground">Coba ubah filter atau kata kunci pencarian Anda.</p>
                            <button
                                @click="resetFilters"
                                class="inline-block rounded-full bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                Reset Filter
                            </button>
                        </div>

                        <!-- Load More -->
                        <div v-if="products.links.next" class="flex justify-center pt-12">
                            <button
                                @click="loadMore"
                                :disabled="isLoadingMore"
                                class="flex min-w-[200px] items-center justify-center gap-2 rounded-full bg-white px-8 py-3 text-sm font-semibold text-foreground shadow-sm transition-colors hover:bg-primary hover:text-primary-foreground disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                <Loader2 v-if="isLoadingMore" class="h-4 w-4 animate-spin" />
                                <span>{{ isLoadingMore ? "Memuat..." : "Lihat Lebih Banyak" }}</span>
                            </button>
                        </div>
                        <div v-else-if="allProducts.length > 0" class="pt-12 text-center">
                            <p class="text-sm text-muted-foreground">Anda telah melihat semua produk</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

<script setup>
import { computed, ref, watch } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { ShoppingBag, Heart, ShieldCheck, Truck, RefreshCw, ChevronRight, Plus, Minus, Warehouse, Scale } from "lucide-vue-next";

const props = defineProps({
    product: Object,
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
    router.post(
        route("frontend.wishlist.toggle"),
        {
            product_id: props.product.id,
        },
        {
            preserveScroll: true,
        },
    );
};

const hasVariants = computed(() => props.product.variants && props.product.variants.length > 0);

const attributeGroups = computed(() => {
    if (!hasVariants.value) return {};

    const groups = {};
    props.product.variants.forEach((variant) => {
        variant.attributes.forEach((attr) => {
            if (!groups[attr.name]) {
                groups[attr.name] = new Set();
            }
            groups[attr.name].add(attr.option);
        });
    });

    Object.keys(groups).forEach((key) => {
        groups[key] = Array.from(groups[key]);
    });

    return groups;
});

const selectedVariant = computed(() => {
    if (!hasVariants.value) return null;

    const selectedKeys = Object.keys(selectedAttributes.value);
    if (selectedKeys.length !== Object.keys(attributeGroups.value).length) return null;

    return props.product.variants.find((variant) => {
        return variant.attributes.every((attr) => selectedAttributes.value[attr.name] === attr.option);
    });
});

const displayPrice = computed(() => {
    let basePrice = selectedVariant.value ? selectedVariant.value.price : props.product.sale_price || props.product.price;

    if (props.product.wholesales && props.product.wholesales.length > 0) {
        const minOrder = props.product.min_order || 1;
        const applicableWholesale = [...props.product.wholesales]
            .filter((w) => w.min_qty > minOrder)
            .sort((a, b) => b.min_qty - a.min_qty)
            .find((w) => quantity.value >= w.min_qty);

        if (applicableWholesale) return applicableWholesale.price;
    }

    return basePrice;
});

const activeWholesale = computed(() => {
    if (!props.product.wholesales || props.product.wholesales.length === 0) return null;
    const minOrder = props.product.min_order || 1;
    return [...props.product.wholesales]
        .filter((w) => w.min_qty > minOrder)
        .sort((a, b) => b.min_qty - a.min_qty)
        .find((w) => quantity.value >= w.min_qty);
});

const nextWholesale = computed(() => {
    if (!props.product.wholesales || props.product.wholesales.length === 0) return null;
    return [...props.product.wholesales].sort((a, b) => a.min_qty - b.min_qty).find((w) => quantity.value < w.min_qty);
});

const displayStock = computed(() => {
    if (selectedVariant.value) return selectedVariant.value.stock;
    return props.product.stock;
});

const isSale = computed(() => !selectedVariant.value && !!props.product.sale_price);

const seoTitle = computed(() => props.product.meta?.title || props.product.name);
const seoDescription = computed(() => props.product.meta?.description || props.product.description?.substring(0, 160));
const seoKeywords = computed(() => props.product.meta?.keyword);

const updateQuantity = (val) => {
    const maxStock = selectedVariant.value ? selectedVariant.value.stock : props.product.stock;
    const newQty = quantity.value + val;
    if (newQty >= (props.product.min_order || 1) && newQty <= maxStock) {
        quantity.value = newQty;
    }
};

const selectAttribute = (name, option) => {
    selectedAttributes.value[name] = option;
    quantity.value = 1;
};

const toggleFaq = (index) => {
    activeFaq.value = activeFaq.value === index ? null : index;
};

const addToCart = () => {
    if (hasVariants.value && !selectedVariant.value) {
        alert("Silakan pilih semua opsi sebelum menambahkan ke keranjang.");
        return;
    }

    router.post(
        route("frontend.cart.store"),
        {
            product_id: props.product.id,
            product_variant_id: selectedVariant.value?.id,
            quantity: quantity.value,
        },
        {
            preserveScroll: true,
            preserveState: true,
        },
    );
};
</script>

<template>
    <TemplateWrapper :title="seoTitle" :description="seoDescription" :keywords="seoKeywords">
        <div class="bg-secondary/30 py-8 md:py-12">
            <div class="container mx-auto px-4">
                <nav class="mb-8 flex items-center gap-2 text-xs text-muted-foreground">
                    <Link :href="route('frontend.home')" class="transition-colors hover:text-foreground">Beranda</Link>
                    <ChevronRight class="h-3 w-3" />
                    <Link :href="route('frontend.products')" class="transition-colors hover:text-foreground">Produk</Link>
                    <ChevronRight v-if="product.category" class="h-3 w-3" />
                    <Link
                        v-if="product.category"
                        :href="route('frontend.products', { category: product.category.slug })"
                        class="transition-colors hover:text-foreground"
                    >
                        {{ product.category.name }}
                    </Link>
                </nav>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-2 lg:gap-12">
                    <div class="space-y-4">
                        <div class="aspect-[4/5] overflow-hidden rounded-2xl bg-white">
                            <img
                                :src="selectedImage || 'https://placehold.co/800x1000?text=No+Image'"
                                :alt="product.name"
                                class="h-full w-full object-cover"
                            />
                        </div>
                        <div v-if="product.images && product.images.length > 1" class="grid grid-cols-5 gap-3">
                            <button
                                v-for="(image, index) in product.images"
                                :key="index"
                                @click="selectedImage = image"
                                class="aspect-square overflow-hidden rounded-xl border-2 transition-all"
                                :class="selectedImage === image ? 'border-primary' : 'border-transparent opacity-60 hover:opacity-100'"
                            >
                                <img :src="image" class="h-full w-full object-cover" />
                            </button>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="space-y-4 rounded-2xl bg-white p-6">
                            <div
                                v-if="product.category"
                                class="inline-block rounded-full bg-secondary px-3 py-1 text-xs font-bold uppercase tracking-wider text-muted-foreground"
                            >
                                {{ product.category.name }}
                            </div>
                            <h1 class="text-2xl font-bold text-foreground md:text-3xl">
                                {{ product.name }}
                            </h1>

                            <div class="flex flex-wrap items-center gap-3">
                                <template v-if="isSale && !activeWholesale">
                                    <span class="text-3xl font-bold text-primary">Rp{{ product.sale_price?.toLocaleString("id-ID") }}</span>
                                    <span class="text-lg text-muted-foreground line-through">Rp{{ product.price?.toLocaleString("id-ID") }}</span>
                                    <span v-if="product.discount_percentage" class="rounded-full bg-red-500 px-3 py-1 text-xs font-bold text-white">
                                        {{ product.discount_percentage }}% OFF
                                    </span>
                                </template>
                                <template v-else>
                                    <span class="text-3xl font-bold text-primary">Rp{{ displayPrice?.toLocaleString("id-ID") }}</span>
                                </template>
                            </div>

                            <p v-if="activeWholesale" class="rounded-xl bg-green-50 p-3 text-sm font-medium text-green-700">
                                Harga Grosir! Min. {{ activeWholesale.min_qty }} unit @ Rp{{ activeWholesale.price?.toLocaleString("id-ID") }}
                            </p>
                            <p v-else-if="nextWholesale" class="rounded-xl bg-secondary p-3 text-sm text-muted-foreground">
                                Beli {{ nextWholesale.min_qty }}+ unit untuk harga grosir Rp{{ nextWholesale.price?.toLocaleString("id-ID") }}
                            </p>

                            <p class="text-sm leading-relaxed text-muted-foreground">
                                {{ product.description }}
                            </p>

                            <div v-if="hasVariants" class="space-y-4 border-t border-border pt-4">
                                <div v-for="(options, name) in attributeGroups" :key="name" class="space-y-3">
                                    <label class="flex items-center justify-between text-sm font-semibold">
                                        <span>{{ name }}</span>
                                        <span v-if="selectedAttributes[name]" class="font-normal text-muted-foreground">{{
                                            selectedAttributes[name]
                                        }}</span>
                                    </label>
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            v-for="option in options"
                                            :key="option"
                                            @click="selectAttribute(name, option)"
                                            class="rounded-lg border-2 px-4 py-2 text-sm font-semibold transition-all"
                                            :class="
                                                selectedAttributes[name] === option
                                                    ? 'border-primary bg-primary text-primary-foreground'
                                                    : 'border-border bg-white text-foreground hover:border-primary/50'
                                            "
                                        >
                                            {{ option }}
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 rounded-xl bg-secondary p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white">
                                        <Warehouse class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-muted-foreground">Dikirim Dari</p>
                                        <p class="text-sm font-semibold">{{ product.warehouse?.name || "Gudang Utama" }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 border-l border-border/50 pl-4">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white">
                                        <Scale class="h-5 w-5 text-muted-foreground" />
                                    </div>
                                    <div>
                                        <p class="text-[10px] uppercase tracking-wider text-muted-foreground">Berat</p>
                                        <p class="text-sm font-semibold">{{ ((product.weight || 0) / 1000).toFixed(2) }} kg</p>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-4">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm text-muted-foreground">Tersedia:</span>
                                    <span :class="displayStock > 0 ? 'text-green-600' : 'text-red-500'" class="text-sm font-semibold">
                                        {{ displayStock > 0 ? `${displayStock} unit` : "Stok Habis" }}
                                    </span>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="text-sm font-semibold">Jumlah</label>
                                <div class="flex w-fit items-center rounded-full border border-border bg-white">
                                    <button
                                        @click="updateQuantity(-1)"
                                        class="flex h-10 w-10 items-center justify-center rounded-l-full transition-colors hover:bg-secondary"
                                        :disabled="quantity <= (product.min_order || 1)"
                                    >
                                        <Minus class="h-4 w-4" />
                                    </button>
                                    <input
                                        type="number"
                                        v-model="quantity"
                                        readonly
                                        class="w-16 border-none bg-transparent text-center text-sm font-semibold focus:ring-0"
                                    />
                                    <button
                                        @click="updateQuantity(1)"
                                        class="flex h-10 w-10 items-center justify-center rounded-r-full transition-colors hover:bg-secondary"
                                        :disabled="quantity >= displayStock"
                                    >
                                        <Plus class="h-4 w-4" />
                                    </button>
                                </div>
                                <p v-if="product.min_order && product.min_order > 1" class="text-xs text-muted-foreground">
                                    Min. pemesanan: {{ product.min_order }} unit
                                </p>
                            </div>

                            <div class="flex items-stretch gap-3">
                                <button
                                    @click="addToCart"
                                    :disabled="displayStock <= 0 || (hasVariants && !selectedVariant)"
                                    class="flex flex-1 items-center justify-center gap-2 rounded-full bg-primary px-6 py-3.5 text-sm font-bold text-primary-foreground shadow-md transition-all hover:bg-primary/90 hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    <ShoppingBag class="h-5 w-5" />
                                    <span>{{ hasVariants && !selectedVariant ? "Pilih Opsi" : "Tambah ke Keranjang" }}</span>
                                </button>
                                <button
                                    @click="toggleWishlist"
                                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border-2 transition-all"
                                    :class="isWishlisted ? 'border-red-500 bg-red-50' : 'border-border hover:border-primary'"
                                >
                                    <Heart
                                        class="h-5 w-5 transition-colors"
                                        :class="isWishlisted ? 'fill-red-500 text-red-500' : 'text-muted-foreground'"
                                    />
                                </button>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="rounded-xl bg-white p-4 text-center">
                                <Truck class="mx-auto mb-2 h-6 w-6 text-primary" />
                                <h4 class="text-xs font-semibold">Gratis Ongkir</h4>
                                <p class="text-[10px] text-muted-foreground">Min. pembelanjaan tertentu</p>
                            </div>
                            <div class="rounded-xl bg-white p-4 text-center">
                                <ShieldCheck class="mx-auto mb-2 h-6 w-6 text-primary" />
                                <h4 class="text-xs font-semibold">Pembayaran Aman</h4>
                                <p class="text-[10px] text-muted-foreground">100% checkout aman</p>
                            </div>
                            <div class="rounded-xl bg-white p-4 text-center">
                                <RefreshCw class="mx-auto mb-2 h-6 w-6 text-primary" />
                                <h4 class="text-xs font-semibold">Easy Returns</h4>
                                <p class="text-[10px] text-muted-foreground">30 hari kebijakan pengembalian</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="product.faqs && product.faqs.length > 0" class="mt-16">
                    <div class="mb-8 text-center">
                        <h2 class="text-xl font-bold text-foreground md:text-2xl">Pertanyaan yang Sering Diajukan</h2>
                        <p class="mt-2 text-sm text-muted-foreground">Yang perlu Anda ketahui tentang {{ product.name }}</p>
                    </div>

                    <div class="mx-auto max-w-3xl space-y-3">
                        <div v-for="(faq, index) in product.faqs" :key="faq.id" class="overflow-hidden rounded-xl bg-white">
                            <button
                                @click="toggleFaq(index)"
                                class="flex w-full items-center justify-between p-5 text-left transition-colors hover:bg-secondary/50"
                            >
                                <span class="pr-4 text-sm font-semibold">{{ faq.question }}</span>
                                <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-secondary">
                                    <Plus v-if="activeFaq !== index" class="h-4 w-4 text-foreground" />
                                    <Minus v-else class="h-4 w-4 text-primary" />
                                </div>
                            </button>

                            <div v-show="activeFaq === index" class="px-5 pb-5">
                                <div class="prose prose-sm max-w-none text-sm leading-relaxed text-muted-foreground" v-html="faq.answer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

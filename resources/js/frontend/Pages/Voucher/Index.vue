<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import axios from "axios";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import VoucherCard from "../../components/UI/VoucherCard.vue";
import { Filter, Ticket, Truck, Tag } from "lucide-vue-next";

const props = defineProps({
    vouchers: Array,
    pendingShippingVoucher: Object,
    pendingProductVoucher: Object,
});

const activeFilter = ref("all");
const applyingCode = ref(null);

const filters = [
    { key: "all", label: "Semua", icon: Ticket },
    { key: "shipping", label: "Ongkir", icon: Truck },
    { key: "product", label: "Produk", icon: Tag },
];

const filteredVouchers = computed(() => {
    if (activeFilter.value === "all") {
        return props.vouchers || [];
    }
    return (props.vouchers || []).filter((v) => {
        if (activeFilter.value === "shipping") {
            return v.is_shipping;
        }
        if (activeFilter.value === "product") {
            return v.is_product;
        }
        return true;
    });
});

const isPending = (voucher) => {
    const shippingCode = props.pendingShippingVoucher?.code;
    const productCode = props.pendingProductVoucher?.code;
    return voucher.code === shippingCode || voucher.code === productCode;
};

const handleApply = async (voucher) => {
    applyingCode.value = voucher.code;

    try {
        await axios.post("/api/vouchers/apply", { code: voucher.code }, { withCredentials: true });
        router.visit(route("frontend.checkout"));
    } catch (error) {
        console.error("Failed to apply voucher:", error);
    } finally {
        applyingCode.value = null;
    }
};

const setFilter = (filter) => {
    activeFilter.value = filter;
};
</script>

<template>
    <TemplateWrapper title="Voucher & Promo">
        <div class="min-h-screen bg-secondary/30 py-8 md:py-12">
            <div class="container mx-auto px-4">
                <!-- Header -->
                <div class="mb-8 text-center">
                    <h1 class="mb-2 text-3xl font-bold text-foreground md:text-4xl">Voucher & Promo</h1>
                    <p class="text-muted-foreground">Temukan voucher menarik untuk hemat lebih banyak</p>
                </div>

                <!-- Filter Tabs -->
                <div class="mb-8 flex items-center justify-center gap-2">
                    <button
                        v-for="filter in filters"
                        :key="filter.key"
                        @click="setFilter(filter.key)"
                        class="flex items-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition-all duration-200"
                        :class="
                            activeFilter === filter.key
                                ? 'bg-primary text-primary-foreground shadow-lg shadow-primary/30'
                                : 'bg-white text-muted-foreground hover:bg-gray-50'
                        "
                    >
                        <component :is="filter.icon" class="h-4 w-4" />
                        <span>{{ filter.label }}</span>
                    </button>
                </div>

                <!-- Voucher Grid -->
                <div v-if="filteredVouchers.length > 0" class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <VoucherCard
                        v-for="voucher in filteredVouchers"
                        :key="voucher.id"
                        :voucher="voucher"
                        :is-applied="isPending(voucher)"
                        :is-applying="applyingCode === voucher.code"
                        @apply="handleApply"
                    />
                </div>

                <!-- Empty State -->
                <div v-else class="py-20 text-center">
                    <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-gray-100">
                        <Ticket class="h-12 w-12 text-gray-400" />
                    </div>
                    <h3 class="mb-2 text-xl font-bold text-foreground">Tidak Ada Voucher</h3>
                    <p class="text-muted-foreground">Voucher dengan filter "{{ activeFilter }}" belum tersedia. Coba filter lain.</p>
                </div>

                <!-- Info Box -->
                <div class="mt-12 rounded-2xl bg-white p-6 shadow-sm">
                    <h3 class="mb-4 flex items-center gap-2 text-lg font-bold text-foreground">
                        <span class="text-2xl">💡</span>
                        Cara Menggunakan Voucher
                    </h3>
                    <ul class="space-y-3 text-sm text-muted-foreground">
                        <li class="flex items-start gap-2">
                            <span
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"
                                >1</span
                            >
                            <span>Pilih voucher yang kamu inginkan dan klik "Gunakan Voucher"</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"
                                >2</span
                            >
                            <span>Voucher akan otomatis diterapkan ke checkout</span>
                        </li>
                        <li class="flex items-start gap-2">
                            <span
                                class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-primary/10 text-xs font-bold text-primary"
                                >3</span
                            >
                            <span>Selesaikan pembayaran dan nikmati hematnya!</span>
                        </li>
                    </ul>
                    <div class="mt-4 rounded-xl bg-amber-50 p-4">
                        <p class="text-sm text-amber-800">
                            <strong>💡 Tips:</strong> Kamu bisa menggunakan 1 voucher ongkir dan 1 voucher produk secara bersamaan untuk hemat lebih
                            maksimal!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

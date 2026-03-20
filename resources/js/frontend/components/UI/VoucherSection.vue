<script setup>
import { ref, onMounted } from "vue";
import { Link } from "@inertiajs/vue3";
import VoucherCard from "./VoucherCard.vue";
import voucherService from "../../services/voucherService";
import { ChevronRight } from "lucide-vue-next";

const props = defineProps({
    title: {
        type: String,
        default: "Voucher Tersedia",
    },
    subtitle: {
        type: String,
        default: "Simpan dan gunakan saat checkout",
    },
    limit: {
        type: Number,
        default: 4,
    },
});

const vouchers = ref([]);
const isLoading = ref(true);

onMounted(async () => {
    try {
        const response = await voucherService.getVouchers();
        vouchers.value = (response.data || []).slice(0, props.limit);
    } catch (error) {
        console.error("Failed to load vouchers:", error);
    } finally {
        isLoading.value = false;
    }
});
</script>

<template>
    <section v-if="vouchers.length > 0" class="py-8 md:py-12">
        <div class="container mx-auto px-4">
            <!-- Header -->
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-foreground md:text-2xl">{{ title }}</h2>
                    <p v-if="subtitle" class="mt-1 text-sm text-muted-foreground">{{ subtitle }}</p>
                </div>
                <Link
                    :href="route('frontend.vouchers')"
                    class="flex items-center gap-1 text-sm font-semibold text-primary transition-colors hover:text-primary/80"
                >
                    <span>Lihat Semua</span>
                    <ChevronRight class="h-4 w-4" />
                </Link>
            </div>

            <!-- Voucher Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <VoucherCard v-for="voucher in vouchers" :key="voucher.id" :voucher="voucher" :show-apply-button="true" :show-copy-button="true" />
            </div>
        </div>
    </section>
</template>

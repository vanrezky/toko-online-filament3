<script setup>
import { computed, ref } from "vue";
import { Copy, Check, Ticket, Truck, Tag, Clock, Users, Loader2 } from "lucide-vue-next";

const props = defineProps({
    voucher: {
        type: Object,
        required: true,
    },
    variant: {
        type: String,
        default: "default",
        validator: (v) => ["default", "compact", "horizontal"].includes(v),
    },
    showApplyButton: {
        type: Boolean,
        default: true,
    },
    showCopyButton: {
        type: Boolean,
        default: true,
    },
    isApplied: {
        type: Boolean,
        default: false,
    },
    isApplying: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["apply", "copy"]);

const copied = ref(false);

const hasBackgroundImage = computed(() => {
    return props.voucher.image && props.voucher.image.length > 0;
});

const typeIcon = computed(() => {
    return props.voucher.is_shipping ? Truck : Tag;
});

const typeLabel = computed(() => {
    return props.voucher.is_shipping ? "FREE ONGKIR" : "DISKON";
});

const isExpiringSoon = computed(() => {
    return props.voucher.is_expiring_soon;
});

const timeRemaining = computed(() => {
    if (props.voucher.remaining_days > 0) {
        return `${props.voucher.remaining_days} hari lagi`;
    }
    if (props.voucher.remaining_hours > 0) {
        return `${props.voucher.remaining_hours} jam lagi`;
    }
    return "Segera berakhir";
});

const copyCode = async () => {
    try {
        await navigator.clipboard.writeText(props.voucher.code);
        copied.value = true;
        emit("copy", props.voucher);
        setTimeout(() => {
            copied.value = false;
        }, 2000);
    } catch (err) {
        console.error("Failed to copy:", err);
    }
};

const applyVoucher = () => {
    if (props.isApplying) return;
    emit("apply", props.voucher);
};
</script>

<template>
    <div
        class="group relative overflow-hidden rounded-2xl transition-all duration-300"
        :class="[
            hasBackgroundImage ? 'h-full min-h-[280px]' : 'bg-white shadow-sm ring-1 ring-gray-200/50',
            isApplied ? 'ring-2 ring-primary' : '',
            variant === 'compact' ? 'p-4' : 'p-5',
        ]"
    >
        <!-- Background Image -->
        <div v-if="hasBackgroundImage" class="absolute inset-0 bg-cover bg-center" :style="{ backgroundImage: `url(${voucher.image})` }">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-black/30"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 flex h-full flex-col justify-between" :class="hasBackgroundImage ? 'text-white' : 'text-gray-900'">
            <!-- Header -->
            <div>
                <!-- Type Badge -->
                <div
                    class="mb-3 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold"
                    :class="hasBackgroundImage ? 'bg-white/20 text-white backdrop-blur-sm' : 'bg-primary/10 text-primary'"
                >
                    <component :is="typeIcon" class="h-3.5 w-3.5" />
                    <span>{{ typeLabel }}</span>
                </div>

                <!-- Expiring Soon Badge -->
                <div
                    v-if="isExpiringSoon"
                    class="mb-2 inline-flex items-center gap-1 rounded-full bg-amber-500 px-2 py-0.5 text-xs font-semibold text-white"
                >
                    <Clock class="h-3 w-3" />
                    <span>{{ timeRemaining }}</span>
                </div>

                <!-- Discount Value -->
                <h3 class="mb-1 text-2xl font-bold" :class="hasBackgroundImage ? '' : 'text-primary'">
                    {{ voucher.formatted_discount }}
                </h3>

                <!-- Name -->
                <p class="mb-2 font-semibold" :class="variant === 'compact' ? 'text-sm' : 'text-base'">
                    {{ voucher.name }}
                </p>

                <!-- Description -->
                <p v-if="voucher.description && variant !== 'compact'" class="mb-3 text-sm opacity-80">
                    {{ voucher.description }}
                </p>

                <!-- Min Purchase -->
                <p class="mb-3 text-xs opacity-70">Min. Belanja: {{ voucher.min_purchase_formatted }}</p>
            </div>

            <!-- Code & Actions -->
            <div>
                <!-- Code Input -->
                <div class="mb-3 flex items-center gap-2">
                    <div
                        class="flex flex-1 items-center justify-between rounded-xl border-2 px-4 py-2.5"
                        :class="hasBackgroundImage ? 'border-white/30 bg-white/10 text-white' : 'border-gray-200 bg-gray-50'"
                    >
                        <span class="font-mono font-bold tracking-wider" :class="hasBackgroundImage ? 'text-white' : 'text-gray-900'">
                            {{ voucher.code }}
                        </span>
                    </div>

                    <!-- Copy Button -->
                    <button
                        v-if="showCopyButton"
                        @click="copyCode"
                        class="flex h-11 w-11 items-center justify-center rounded-xl transition-all duration-200"
                        :class="
                            copied
                                ? 'bg-green-500 text-white'
                                : hasBackgroundImage
                                  ? 'bg-white/20 text-white hover:bg-white/30'
                                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                        "
                        :title="copied ? 'Copied!' : 'Salin kode'"
                    >
                        <Check v-if="copied" class="h-5 w-5" />
                        <Copy v-else class="h-5 w-5" />
                    </button>
                </div>

                <!-- Meta Info -->
                <div class="mb-3 flex items-center gap-4 text-xs opacity-70">
                    <div class="flex items-center gap-1">
                        <Clock class="h-3.5 w-3.5" />
                        <span>{{ timeRemaining }}</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <Users class="h-3.5 w-3.5" />
                        <span>{{ voucher.usage_count }}x digunakan</span>
                    </div>
                </div>

                <!-- Apply Button -->
                <button
                    v-if="showApplyButton"
                    @click="applyVoucher"
                    :disabled="voucher.is_fully_used || isApplying"
                    class="flex w-full items-center justify-center gap-2 rounded-xl py-3 text-sm font-bold transition-all duration-200"
                    :class="
                        isApplied
                            ? 'bg-green-500 text-white hover:bg-green-600'
                            : voucher.is_fully_used
                              ? 'cursor-not-allowed bg-gray-200 text-gray-500'
                              : isApplying
                                ? 'cursor-wait bg-gray-200 text-gray-500'
                                : hasBackgroundImage
                                  ? 'bg-white text-primary hover:bg-gray-100'
                                  : 'bg-primary text-white hover:bg-primary/90'
                    "
                >
                    <Loader2 v-if="isApplying" class="h-4 w-4 animate-spin" />
                    <Ticket v-else class="h-4 w-4" />
                    <span>{{
                        isApplied ? "Ganti Voucher" : voucher.is_fully_used ? "Habis Digunakan" : isApplying ? "Menerapkan..." : "Gunakan Voucher"
                    }}</span>
                </button>
            </div>
        </div>

        <!-- Applied Overlay -->
        <div v-if="isApplied" class="absolute -right-4 -top-4 rotate-12">
            <div class="rounded-lg bg-green-500 px-4 py-1 text-sm font-bold text-white shadow-lg">✓ AKTIF</div>
        </div>
    </div>
</template>

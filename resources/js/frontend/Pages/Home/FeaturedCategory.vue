<script setup>
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    featuredCategories: {
        required: true,
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <div class="scrollbar-hidden group overflow-x-auto pb-2 pt-5 lg:pt-10">
        <div class="grid min-w-full auto-cols-max grid-flow-col gap-4">
            <Link
                href="#"
                class="hover:scrollbar-visible flex min-w-[320px] transform items-start justify-between rounded-xl border bg-white p-4 transition duration-300 hover:scale-105 hover:shadow"
                v-for="(category, index) in featuredCategories"
                :key="index"
            >
                <div class="flex items-center gap-5">
                    <div class="rounded-xl bg-secondary p-3">
                        <img :src="category.image_url" :alt="category.name" class="w-7" />
                    </div>
                    <div>
                        <h4 class="font-semibold uppercase tracking-wider">{{ category.name }}</h4>
                        <p class="text-sm text-gray-500">Show All</p>
                    </div>
                </div>
                <p class="text-xs font-light text-gray-500">({{ category.products_count }})</p>
            </Link>
        </div>
    </div>
</template>

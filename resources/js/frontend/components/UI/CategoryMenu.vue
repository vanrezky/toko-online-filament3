<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    activeCategory: {
        type: String,
        default: "",
    },
});

const page = usePage();

const allCategories = computed(() => {
    if (props.categories.length > 0) return props.categories;
    return page.props.categories || [];
});

const emit = defineEmits(["select"]);
const selectCategory = (slug) => {
    emit("select", slug);
};
</script>

<template>
    <div class="border-b border-border bg-white">
        <div class="container mx-auto px-4">
            <div class="scrollbar-hidden -mx-4 flex items-center gap-3 overflow-x-auto px-4 py-3">
                <!-- Semua (All) -->
                <Link
                    :href="route('frontend.home')"
                    class="flex flex-shrink-0 flex-col items-center gap-1 rounded-lg px-3 py-2 text-center text-xs font-semibold transition-all duration-200"
                    :class="!activeCategory ? 'bg-primary text-primary-foreground shadow-md' : 'bg-secondary text-foreground hover:bg-primary/10'"
                >
                    <span class="text-base">🛍️</span>
                    <span>Semua</span>
                </Link>

                <!-- Categories -->
                <Link
                    v-for="category in allCategories"
                    :key="category.id"
                    :href="route('frontend.home', { category: category.slug })"
                    class="flex flex-shrink-0 flex-col items-center gap-1 rounded-lg px-3 py-2 text-center text-xs font-semibold transition-all duration-200"
                    :class="
                        activeCategory === category.slug
                            ? 'bg-primary text-primary-foreground shadow-md'
                            : 'bg-secondary text-foreground hover:bg-primary/10'
                    "
                >
                    <div class="h-8 w-8 overflow-hidden rounded-lg bg-muted">
                        <img v-if="category.image_url" :src="category.image_url" :alt="category.name" class="h-full w-full object-cover" />
                        <span v-else class="flex h-full w-full items-center justify-center text-base"> 📁 </span>
                    </div>
                    <span class="max-w-[60px] truncate">{{ category.name }}</span>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { Link, router } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Search, Calendar, User, ArrowRight, Tag } from "lucide-vue-next";
import debounce from "lodash/debounce";

const props = defineProps({
    posts: Object,
    categories: Array,
    filters: Object,
});

const search = ref(props.filters.search || "");
const selectedCategory = ref(props.filters.category || "");

const applyFilters = debounce(() => {
    router.get(
        route("frontend.blog.index"),
        {
            search: search.value,
            category: selectedCategory.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        },
    );
}, 500);

watch([search, selectedCategory], () => {
    applyFilters();
});
</script>

<template>
    <TemplateWrapper title="Blog & Artikel" description="Temukan berita terkini, tips fashion, dan artikel gaya hidup menarik.">
        <div class="bg-gradient-to-br from-secondary/50 via-white to-secondary/30 py-12 md:py-20">
            <div class="container mx-auto px-4">
                <div class="mx-auto max-w-6xl space-y-12">
                    <!-- Header -->
                    <div class="flex flex-col justify-between gap-8 border-b border-border/50 pb-12 md:flex-row md:items-end">
                        <div class="space-y-4">
                            <h1 class="text-4xl font-bold tracking-tight text-foreground md:text-5xl">Blog & Artikel</h1>
                            <p class="max-w-md text-lg text-muted-foreground">
                                Temukan berita terkini, tips, dan artikel menarik untuk gaya hidup Anda.
                            </p>
                        </div>

                        <div class="w-full md:w-80">
                            <div class="relative">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Cari artikel..."
                                    class="w-full rounded-xl border border-border bg-white py-4 pl-12 pr-6 text-sm shadow-sm transition-all focus:border-primary focus:ring-2 focus:ring-primary/20"
                                />
                                <Search class="absolute left-4 top-4 h-4 w-4 text-muted-foreground" />
                            </div>
                        </div>
                    </div>

                    <!-- Categories Filter -->
                    <div class="no-scrollbar flex flex-wrap gap-3 overflow-x-auto pb-4">
                        <button
                            @click="selectedCategory = ''"
                            class="whitespace-nowrap rounded-full px-6 py-2.5 text-sm font-semibold transition-all"
                            :class="
                                selectedCategory === ''
                                    ? 'bg-primary text-primary-foreground shadow-md'
                                    : 'bg-secondary text-foreground hover:bg-primary hover:text-primary-foreground'
                            "
                        >
                            Semua
                        </button>
                        <button
                            v-for="cat in categories"
                            :key="cat.slug"
                            @click="selectedCategory = cat.slug"
                            class="whitespace-nowrap rounded-full px-6 py-2.5 text-sm font-semibold transition-all"
                            :class="
                                selectedCategory === cat.slug
                                    ? 'bg-primary text-primary-foreground shadow-md'
                                    : 'bg-secondary text-foreground hover:bg-primary hover:text-primary-foreground'
                            "
                        >
                            {{ cat.name }}
                        </button>
                    </div>

                    <!-- Posts Grid -->
                    <div v-if="posts.data.length > 0" class="grid grid-cols-1 gap-8 md:grid-cols-2 lg:grid-cols-3">
                        <article
                            v-for="post in posts.data"
                            :key="post.id"
                            class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-md transition-all duration-300 hover:shadow-xl"
                        >
                            <Link :href="route('frontend.blog.show', post.slug)" class="block aspect-[16/10] overflow-hidden bg-secondary">
                                <img
                                    v-if="post.image_url"
                                    :src="post.image_url"
                                    :alt="post.title"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                                <div v-else class="flex h-full w-full items-center justify-center text-muted-foreground">
                                    <span class="text-4xl">📝</span>
                                </div>
                            </Link>

                            <div class="flex flex-grow flex-col space-y-4 p-6">
                                <div class="flex items-center gap-3 text-xs text-muted-foreground">
                                    <span class="rounded-full bg-primary/10 px-3 py-1 font-semibold text-primary">{{ post.category?.name }}</span>
                                    <span class="flex items-center gap-1"><Calendar class="h-3 w-3" /> {{ post.published_at }}</span>
                                </div>

                                <Link :href="route('frontend.blog.show', post.slug)" class="block flex-grow">
                                    <h3
                                        class="line-clamp-2 text-lg font-bold leading-tight text-foreground transition-colors group-hover:text-primary"
                                    >
                                        {{ post.title }}
                                    </h3>
                                </Link>

                                <p class="line-clamp-3 flex-grow text-sm leading-relaxed text-muted-foreground">
                                    {{ post.excerpt }}
                                </p>

                                <Link
                                    :href="route('frontend.blog.show', post.slug)"
                                    class="mt-auto inline-flex items-center text-sm font-semibold text-primary transition-all group-hover:gap-3"
                                >
                                    Baca Selengkapnya <ArrowRight class="ml-2 h-4 w-4" />
                                </Link>
                            </div>
                        </article>
                    </div>

                    <!-- Empty State -->
                    <div v-else class="space-y-6 rounded-3xl bg-white py-20 text-center shadow-sm">
                        <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-secondary">
                            <Search class="h-10 w-10 text-muted-foreground" />
                        </div>
                        <div class="space-y-2">
                            <h3 class="text-xl font-bold text-foreground">Artikel Tidak Ditemukan</h3>
                            <p class="text-sm text-muted-foreground">Kami tidak dapat menemukan artikel yang sesuai dengan pencarian Anda.</p>
                        </div>
                        <button
                            @click="
                                search = '';
                                selectedCategory = '';
                            "
                            class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-sm font-semibold text-primary-foreground shadow-md transition-colors hover:bg-primary/90"
                        >
                            Reset Filter
                        </button>
                    </div>

                    <!-- Pagination -->
                    <div v-if="posts.links.length > 3" class="flex justify-center border-t border-border/50 pt-8">
                        <div class="flex gap-2">
                            <Link
                                v-for="(link, k) in posts.links"
                                :key="k"
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="rounded-xl px-5 py-2.5 text-sm font-semibold transition-all"
                                :class="{
                                    'bg-primary text-primary-foreground shadow-md': link.active,
                                    'bg-secondary text-foreground hover:bg-primary hover:text-primary-foreground': !link.active && link.url,
                                    'cursor-not-allowed opacity-40': !link.url,
                                }"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </TemplateWrapper>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>

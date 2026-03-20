<script setup>
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import TemplateWrapper from "../../components/TemplateWrapper.vue";
import { Calendar, User, ArrowLeft, Tag, Facebook, Twitter, Link as LinkIcon, ArrowRight } from "lucide-vue-next";

const props = defineProps({
    post: Object,
    relatedPosts: Object,
    suggestedPosts: Object,
});

const share = (platform) => {
    const url = window.location.href;
    const title = props.post.title;

    if (platform === "facebook") {
        window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, "_blank");
    } else if (platform === "twitter") {
        window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, "_blank");
    } else if (platform === "copy") {
        navigator.clipboard.writeText(url);
    }
};

const allRelatedPosts = computed(() => {
    const posts = [];

    if (props.suggestedPosts && props.suggestedPosts.length > 0) {
        props.suggestedPosts.forEach((p) => posts.push({ ...p, type: "suggested" }));
    }

    if (props.relatedPosts?.data && props.relatedPosts.data.length > 0) {
        props.relatedPosts.data.forEach((p) => posts.push({ ...p, type: "related" }));
    }

    return posts.slice(0, 4);
});
</script>

<template>
    <TemplateWrapper :title="post.meta?.title || post.title" :description="post.meta?.description || post.excerpt" :keywords="post.meta?.keyword">
        <article class="min-h-screen bg-gradient-to-br from-secondary/30 via-white to-secondary/10">
            <!-- Hero Section -->
            <div class="relative h-[40vh] overflow-hidden bg-foreground md:h-[50vh]">
                <img v-if="post.image_url" :src="post.image_url" :alt="post.title" class="h-full w-full object-cover opacity-60" />
                <div class="absolute inset-0 bg-gradient-to-t from-foreground/95 via-foreground/40 to-transparent"></div>

                <div class="absolute inset-0 flex items-end">
                    <div class="container mx-auto w-full px-4 pb-8 md:pb-12">
                        <div class="max-w-4xl space-y-3">
                            <Link
                                :href="route('frontend.blog.index', { category: post.category?.slug })"
                                class="inline-block rounded-full bg-primary px-4 py-1.5 text-xs font-bold text-primary-foreground transition-colors hover:bg-primary/90"
                            >
                                {{ post.category?.name }}
                            </Link>
                            <h1 class="text-2xl font-bold leading-tight tracking-tight text-white md:text-4xl lg:text-5xl">
                                {{ post.title }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-white/60">
                                <span class="flex items-center gap-2"><User class="h-4 w-4" /> {{ post.author?.name }}</span>
                                <span class="flex items-center gap-2"><Calendar class="h-4 w-4" /> {{ post.published_at }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content Section -->
            <div class="container mx-auto px-4 py-12">
                <div class="mx-auto max-w-4xl">
                    <!-- Main Content Card -->
                    <div class="rounded-2xl bg-white p-6 shadow-sm md:p-10">
                        <!-- Content -->
                        <div
                            class="prose prose-lg prose-headings:text-foreground prose-headings:font-bold prose-p:text-muted-foreground prose-a:text-primary prose-img:rounded-xl max-w-none"
                            v-html="post.content"
                        ></div>

                        <!-- Tags & Share Row -->
                        <div class="mt-10 flex flex-col justify-between gap-6 border-t border-border/50 pt-8 md:flex-row md:items-center">
                            <!-- Tags -->
                            <div v-if="post.tags && post.tags.length > 0" class="flex flex-wrap items-center gap-2">
                                <span class="mr-2 text-sm font-semibold text-foreground">Tags:</span>
                                <Link
                                    v-for="tag in post.tags"
                                    :key="tag"
                                    :href="route('frontend.blog.index', { tag })"
                                    class="rounded-full bg-secondary/80 px-4 py-1.5 text-sm font-medium text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                                >
                                    #{{ tag }}
                                </Link>
                            </div>

                            <!-- Share Buttons -->
                            <div class="flex items-center gap-3">
                                <span class="text-sm font-semibold text-foreground">Bagikan:</span>
                                <button
                                    @click="share('facebook')"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                                >
                                    <Facebook class="h-4 w-4" />
                                </button>
                                <button
                                    @click="share('twitter')"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                                >
                                    <Twitter class="h-4 w-4" />
                                </button>
                                <button
                                    @click="share('copy')"
                                    class="flex h-9 w-9 items-center justify-center rounded-full bg-secondary text-foreground transition-colors hover:bg-primary hover:text-primary-foreground"
                                >
                                    <LinkIcon class="h-4 w-4" />
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Related Articles Section -->
                    <div v-if="allRelatedPosts.length > 0" class="mt-12">
                        <div class="mb-8 flex items-center justify-between">
                            <h2 class="text-2xl font-bold text-foreground">Baca Juga</h2>
                            <Link
                                v-if="post.category"
                                :href="route('frontend.blog.index', { category: post.category?.slug })"
                                class="flex items-center gap-2 text-sm font-semibold text-primary hover:underline"
                            >
                                Lihat semua
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <Link
                                v-for="item in allRelatedPosts"
                                :key="item.id"
                                :href="route('frontend.blog.show', item.slug)"
                                class="group block rounded-xl bg-white p-4 shadow-sm transition-all hover:-translate-y-1 hover:shadow-lg"
                            >
                                <div class="mb-4 aspect-[4/3] overflow-hidden rounded-lg bg-secondary">
                                    <img
                                        v-if="item.image_url"
                                        :src="item.image_url"
                                        :alt="item.title"
                                        class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105"
                                    />
                                    <div v-else class="flex h-full w-full items-center justify-center text-3xl">📝</div>
                                </div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-primary">{{ item.category?.name }}</span>
                                <h3 class="mt-2 line-clamp-2 text-sm font-bold text-foreground transition-colors group-hover:text-primary">
                                    {{ item.title }}
                                </h3>
                                <p class="mt-2 text-xs text-muted-foreground">{{ item.published_at }}</p>
                            </Link>
                        </div>
                    </div>

                    <!-- Back Button -->
                    <div class="mt-12 text-center">
                        <Link
                            :href="route('frontend.blog.index')"
                            class="inline-flex items-center gap-2 text-sm font-semibold text-foreground transition-colors hover:text-primary"
                        >
                            <ArrowLeft class="h-4 w-4" />
                            Kembali ke Blog
                        </Link>
                    </div>
                </div>
            </div>
        </article>
    </TemplateWrapper>
</template>

<style>
.prose blockquote {
    font-style: italic;
    border-left-width: 4px;
    border-left-color: hsl(var(--primary));
    padding-left: 1.5rem;
    margin-left: 0;
    margin-right: 0;
}
</style>

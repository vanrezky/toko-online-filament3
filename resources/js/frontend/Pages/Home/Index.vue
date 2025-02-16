<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import Layout from "../../Layouts/Layout.vue";
import AppHead from "../AppHead.vue";
import Topbar from "../../Layouts/Topbar.vue";
import { computed } from "vue";
import { PhEnvelopeSimple, PhMagnifyingGlass, PhPhone, PhSignOut, PhUser, PhUserCircle, PhUserPlus } from "@phosphor-icons/vue";
import { Button } from "@shadcn-vue/ui";
const props = defineProps({
    products: {
        type: Object,
        default: () => ({}),
    },
    categories: {
        type: Object,
        default: () => ({}),
    },
});

const settings = computed(() => usePage().props.settings);
</script>
<template>
    <Layout>
        <AppHead title="Products" />
        <div class="relative z-50 w-full">
            <header class="flex flex-wrap border-b border-gray-200 pb-2 text-sm sm:flex-col sm:pb-0">
                <Topbar />
                <!-- start primary nav -->
                <nav class="box-border flex h-[80px] w-full flex-wrap justify-between border-b border-solid">
                    <div class="mx-auto w-full max-w-[540px] text-black md:max-w-[960px] lg:max-w-[1140px]">
                        <div class="flex h-full items-center justify-between">
                            <div class="flex items-center justify-between">
                                <Link class="flex flex-none items-center text-2xl font-semibold text-black" :aria-label="settings.site_name" href="/">
                                    <img v-if="settings.logo" :src="settings.logo" :alt="settings.site_name" class="h-auto max-w-16 sm:max-w-16" />
                                    <span v-else>{{ settings.site_name }}</span>
                                </Link>
                            </div>
                            <form class="relative w-full max-w-md" action="#" method="GET">
                                <input
                                    type="text"
                                    name="search"
                                    placeholder="Search"
                                    class="w-full rounded-full bg-gray-100 py-2 pl-4 pr-10 text-gray-700 placeholder-gray-400 shadow-sm duration-300 focus:bg-white focus:shadow-lg focus:outline-none"
                                />
                                <button type="submit" class="absolute inset-y-0 right-3 flex items-center">
                                    <PhMagnifyingGlass class="h-5 w-5 text-black" />
                                </button>
                            </form>
                            <div class="flex items-center justify-between gap-x-5">
                                <a href="#" class="m-1 flex items-center gap-1 hover:text-primary">
                                    <PhPhone class="size-5" />
                                    Call
                                </a>
                                <div class="m-1 flex items-center gap-1 hover:text-primary">
                                    <PhEnvelopeSimple class="size-5" />
                                    Mail
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="flex min-h-screen items-center justify-center bg-gray-100">
                                <Button variant="default" size="lg">Hello from shadcn-vue!</Button>
                            </div>
                        </div>
                    </div>
                </nav>
                <!-- end primary nav -->
            </header>
            <!-- <x-navigation-menu :mobile="true" :categories="$categories" /> -->
        </div>
    </Layout>
</template>

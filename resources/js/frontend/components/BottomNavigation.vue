<script setup>
import { Link } from "@inertiajs/vue3";
import { Heart, House, Search, UserRound } from "lucide-vue-next";
import { route } from "ziggy-js";

const menus = [
    {
        url: "frontend.home",
        title: "Home",
        icon: House,
    },
    {
        url: "frontend.products",
        title: "Shop",
        icon: Search,
    },
    {
        url: "frontend.wishlist",
        title: "Wishlist",
        icon: Heart,
    },
    {
        url: "frontend.account",
        title: "Account",
        icon: UserRound,
    },
];
</script>

<template>
    <div class="fixed bottom-0 z-50 h-16 w-full max-w-[450px] rounded-xl rounded-t-xl shadow-lg">
        <div class="flex gap-2 border-t border-border bg-white px-4 pb-3 pt-2">
            <Link
                v-for="(menu, i) in menus"
                :key="i"
                class="just flex flex-1 flex-col items-center justify-end gap-1"
                :class="route().current(menu.url) ? 'rounded-full text-[#111418]' : 'text-[#60758a]'"
                :href="route(menu.url)"
            >
                <component :is="menu.icon" :class="route().current(menu.url) ? 'text-[#111418]' : 'text-[#60758a]'" />
                <p
                    class="text-xs font-medium leading-normal tracking-[0.015em]"
                    :class="route().current(menu.url) ? 'text-[#111418]' : 'text-[#60758a]'"
                >
                    {{ menu.title }}
                </p>
            </Link>
        </div>
        <div class="h-5 bg-white"></div>
    </div>
</template>

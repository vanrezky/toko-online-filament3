<script setup>
import { PhList, PhPhoneCall, PhSignIn, PhUserCircle, PhUserPlus } from "@phosphor-icons/vue";
import NavigationMenu from "./ui/navigation-menu/NavigationMenu.vue";
import NavigationMenuItem from "./ui/navigation-menu/NavigationMenuItem.vue";
import NavigationMenuList from "./ui/navigation-menu/NavigationMenuList.vue";
import NavigationMenuTrigger from "./ui/navigation-menu/NavigationMenuTrigger.vue";
import NavigationMenuContent from "./ui/navigation-menu/NavigationMenuContent.vue";
import NavigationMenuLink from "./ui/navigation-menu/NavigationMenuLink.vue";
import { navigationMenuTriggerStyle } from "./ui/navigation-menu";
import { Link } from "@inertiajs/vue3";
import Sheet from "./ui/sheet/Sheet.vue";
import SheetContent from "./ui/sheet/SheetContent.vue";
import { ref } from "vue";

const menus = [
    {
        url: "/account",
        name: "Account",
        icon: PhUserCircle,
    },
    {
        url: "/sign-up",
        name: "Sign Up",
        icon: PhUserPlus,
    },
    {
        url: "/sign-in",
        name: "Sign In",
        icon: PhSignIn,
    },
];

const isMenuOpen = ref();
const toggleMenu = () => (isMenuOpen.value = !isMenuOpen.value);
</script>

<template>
    <div class="">
        <button @click="toggleMenu" class="rounded-md bg-gray-200 p-2 lg:hidden">
            <PhList class="h-6 w-6" />
        </button>
        <NavigationMenu class="z-20 hidden lg:flex">
            <NavigationMenuList>
                <NavigationMenuItem>
                    <NavigationMenuTrigger> <PhUserCircle class="size-5" /> </NavigationMenuTrigger>
                    <NavigationMenuContent>
                        <ul class="grid p-2">
                            <li>
                                <NavigationMenuLink as-child v-for="(menu, index) in menus" :key="index">
                                    <Link
                                        href="/docs/introduction"
                                        class="block select-none space-y-1 rounded-md p-2 no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                    >
                                        <div class="flex items-center gap-2 text-xs">
                                            <component :is="menu.icon" class="size-5" />
                                            {{ menu.name }}
                                        </div>
                                    </Link>
                                </NavigationMenuLink>
                            </li>
                        </ul>
                    </NavigationMenuContent>
                </NavigationMenuItem>
                <NavigationMenuItem class="hidden lg:block">
                    <NavigationMenuLink href="/docs/introduction" :class="navigationMenuTriggerStyle()">
                        <PhPhoneCall class="size-5" />
                    </NavigationMenuLink>
                </NavigationMenuItem>
            </NavigationMenuList>
        </NavigationMenu>
        <!-- Sheet untuk Menu Mobile -->
        <Sheet :open="isMenuOpen" @close="toggleMenu" side="left">
            <SheetContent class="w-64 bg-white p-4">
                <h3 class="mb-4 text-lg font-semibold">Menu</h3>
                <ul class="space-y-3">
                    <li v-for="menu in menus" :key="menu.url">
                        <Link :to="menu.url" class="flex items-center gap-3 text-gray-700 hover:text-gray-900">
                            <component :is="menu.icon" class="size-5" />
                            {{ menu.name }}
                        </Link>
                    </li>
                </ul>
            </SheetContent>
        </Sheet>
    </div>
</template>
<style scoped>
.sheet-enter-active,
.sheet-leave-active {
    transition: transform 0.3s ease;
}
.sheet-enter,
.sheet-leave-to {
    transform: translateX(-100%);
}

/* Pastikan dropdown tidak keluar dari layar */
.sheet-content {
    max-width: 100vw;
}
</style>

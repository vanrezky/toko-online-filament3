<script setup>
import { PhHeart, PhList, PhShoppingCart, PhSignIn, PhTrash, PhUserCircle, PhUserPlus } from "@phosphor-icons/vue";
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
import { computed, ref } from "vue";
import Button from "./ui/button/Button.vue";

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

const showCart = ref(false);

const cartItems = [
    { id: 1, name: "Swim Suit", price: "$20", image: "https://placehold.co/40", qty: 2, category: "Electorics" },
    { id: 2, name: "Beach Hat", price: "$15", image: "https://placehold.co/40", qty: 1, category: "Bike" },
    { id: 3, name: "Sunglasses", price: "$10", image: "https://placehold.co/40", qty: 1, category: "Women Clothing" },
];

const hideCart = () => {
    setTimeout(() => {
        showCart.value = false;
    }, 300);
};

// const removeFromCart = (id) => {
//     cartItems = this.cartItems.filter((item) => item.id !== id);
// };
const cartCount = computed(() => cartItems.length);
</script>

<template>
    <div class="">
        <button @click="toggleMenu" class="rounded-md bg-gray-200 p-2 lg:hidden">
            <PhList class="h-6 w-6" />
        </button>
        <NavigationMenu class="z-20 hidden lg:flex">
            <NavigationMenuList>
                <NavigationMenuItem>
                    <NavigationMenuTrigger> <PhUserCircle class="size-6" /> </NavigationMenuTrigger>
                    <NavigationMenuContent>
                        <ul class="grid p-2">
                            <li>
                                <NavigationMenuLink as-child v-for="(menu, index) in menus" :key="index">
                                    <Link
                                        :href="menu.url"
                                        class="block select-none space-y-1 rounded-md p-2 no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                    >
                                        <div class="flex items-center gap-2 text-xs">
                                            <component :is="menu.icon" class="size-6" />
                                            {{ menu.name }}
                                        </div>
                                    </Link>
                                </NavigationMenuLink>
                            </li>
                        </ul>
                    </NavigationMenuContent>
                </NavigationMenuItem>
                <NavigationMenuItem>
                    <NavigationMenuLink href="/docs/introduction" :class="navigationMenuTriggerStyle()">
                        <PhHeart class="size-6" />
                    </NavigationMenuLink>
                </NavigationMenuItem>
                <NavigationMenuItem class="ml-auto">
                    <NavigationMenuTrigger class="relative">
                        <PhShoppingCart class="size-6" />
                        <span
                            v-if="cartCount > 0"
                            class="absolute -right-1 -top-1 flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white"
                        >
                            {{ cartCount }}
                        </span>
                    </NavigationMenuTrigger>
                    <NavigationMenuContent>
                        <ul class="grid w-[300px] space-y-2 overflow-y-auto p-2">
                            <li v-for="item in cartItems" :key="item.id" class="flex items-center justify-between rounded border p-2">
                                <Link href="/" class="flex items-center gap-2">
                                    <img :src="item.image" alt="product" class="h-10 w-10" />
                                    <div>
                                        <p class="text-sm hover:underline">{{ item.name }}</p>
                                        <p class="text-xs text-gray-500">
                                            <span>{{ item.qty }} x</span>
                                            {{ item.price }}
                                        </p>
                                    </div>
                                </Link>

                                <Button @click="removeFromCart(item.id)" variant="ghost">
                                    <PhTrash class="size-4 text-black hover:bg-primary" />
                                </Button>
                            </li>
                            <li class="mx-4 my-2">
                                <div class="flex flex-col gap-2">
                                    <Button class="w-full">Proceed To Checkout</Button>
                                    <Link href="/cart"> <Button class="w-full border" variant="ghost">View Cart</Button></Link>
                                </div>
                            </li>
                        </ul>
                    </NavigationMenuContent>
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
                            <component :is="menu.icon" class="size-6" />
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

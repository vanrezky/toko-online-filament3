<script setup>
import Container from "./Container.vue";
import { navigationMenuTriggerStyle } from "./ui/navigation-menu";
import NavigationMenu from "./ui/navigation-menu/NavigationMenu.vue";
import NavigationMenuContent from "./ui/navigation-menu/NavigationMenuContent.vue";
import NavigationMenuItem from "./ui/navigation-menu/NavigationMenuItem.vue";
import NavigationMenuLink from "./ui/navigation-menu/NavigationMenuLink.vue";
import NavigationMenuList from "./ui/navigation-menu/NavigationMenuList.vue";
import NavigationMenuTrigger from "./ui/navigation-menu/NavigationMenuTrigger.vue";
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const categories = computed(() => page.props.categories);

const components = [
    {
        url: "#",
        name: "New Arrivals",
    },
    {
        url: "#",
        name: "Menu 2",
        subs: [
            {
                url: "#",
                name: "Sub Menu 2 1",
            },
            {
                url: "#",
                name: "Sub Menu 2 2",
            },
        ],
    },
    {
        url: "#",
        name: "Menu 3",
    },
];
</script>

<template>
    <Container>
        <div class="flex items-center justify-between p-2">
            <div></div>
            <!-- start navigation -->
            <NavigationMenu class="hidden lg:flex">
                <NavigationMenuList>
                    <NavigationMenuItem>
                        <NavigationMenuTrigger class="font-semibold uppercase tracking-wider">Categories</NavigationMenuTrigger>
                        <NavigationMenuContent>
                            <ul class="grid gap-3 p-6 md:w-[400px] lg:w-[500px] lg:grid-cols-[minmax(0,.75fr)_minmax(0,1fr)]">
                                <li class="row-span-3">
                                    <NavigationMenuLink as-child>
                                        <a
                                            class="flex h-full w-full select-none flex-col justify-end rounded-md bg-gradient-to-b from-muted/50 to-muted p-6 no-underline outline-none focus:shadow-md"
                                            href="/"
                                        >
                                            <img src="https://www.radix-vue.com/logo.svg" class="h-6 w-6" />
                                            <div class="mb-2 mt-4 text-lg font-medium">shadcn/ui</div>
                                            <p class="text-sm leading-tight text-muted-foreground">
                                                Beautifully designed components built with Radix UI and Tailwind CSS.
                                            </p>
                                        </a>
                                    </NavigationMenuLink>
                                </li>
                                <li v-for="(category, index) in categories">
                                    <NavigationMenuLink as-child>
                                        <a
                                            href="/docs/introduction"
                                            class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                        >
                                            <div class="text-sm font-medium leading-none">{{ category.name }}</div>
                                            <p class="line-clamp-2 text-sm leading-snug text-muted-foreground">
                                                Re-usable components built using Radix UI and Tailwind CSS.
                                            </p>
                                        </a>
                                    </NavigationMenuLink>
                                </li>
                            </ul>
                        </NavigationMenuContent>
                    </NavigationMenuItem>
                    <NavigationMenuItem>
                        <NavigationMenuTrigger class="font-semibold uppercase tracking-wider">Components</NavigationMenuTrigger>
                        <NavigationMenuContent>
                            <ul class="grid w-[400px] gap-3 p-4 md:w-[500px] md:grid-cols-2 lg:w-[600px]">
                                <li v-for="component in components" :key="component.name">
                                    <NavigationMenuLink as-child>
                                        <a
                                            :href="component.url"
                                            class="block select-none space-y-1 rounded-md p-3 leading-none no-underline outline-none transition-colors hover:bg-accent hover:text-accent-foreground focus:bg-accent focus:text-accent-foreground"
                                        >
                                            <div class="text-sm font-medium leading-none">{{ component.name }}</div>
                                            <p class="line-clamp-2 text-sm leading-snug text-muted-foreground" v-if="component.description">
                                                {{ component.description }}
                                            </p>
                                        </a>
                                    </NavigationMenuLink>
                                </li>
                            </ul>
                        </NavigationMenuContent>
                    </NavigationMenuItem>
                    <NavigationMenuItem>
                        <NavigationMenuLink
                            href="/docs/introduction"
                            :class="navigationMenuTriggerStyle()"
                            class="font-semibold uppercase tracking-wider"
                        >
                            Documentation
                        </NavigationMenuLink>
                    </NavigationMenuItem>
                </NavigationMenuList>
            </NavigationMenu>
            <!-- end navigation -->
            <!-- start cart -->
            <NavigationMenu class="z-10">
                <NavigationMenuList> </NavigationMenuList>
            </NavigationMenu>
            <!-- end cart -->
        </div>
    </Container>
</template>

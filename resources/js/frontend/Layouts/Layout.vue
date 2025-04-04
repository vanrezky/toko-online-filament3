<script setup>
import Container from "@frontend/Components/Container.vue";
import Topbar from "./Topbar.vue";
import NavigationMenuLogo from "@frontend/Components/NavigationMenuLogo.vue";
import NavigationMenuSearch from "@frontend/Components/NavigationMenuSearch.vue";
import NavigationMenuContent from "@frontend/Components/NavigationMenuContent.vue";
import NavigationSecondary from "@frontend/Components/NavigationSecondary.vue";
import AppHead from "@frontend/Pages/AppHead.vue";

const props = defineProps({
    title: {
        type: String,
        default: "",
    },
});
</script>

<template>
    <AppHead :title="title" />
    <div class="flex min-h-screen flex-col bg-background font-sans">
        <Topbar />
        <!-- primary navigation-->
        <nav class="border-b border-b-secondary">
            <Container>
                <div class="flex items-center justify-between">
                    <NavigationMenuLogo />
                    <NavigationMenuSearch />
                    <NavigationMenuContent />
                </div>
            </Container>
        </nav>
        <!-- secondary navigation -->
        <nav class="border-none lg:border-b lg:border-b-secondary">
            <NavigationSecondary />
        </nav>
        <div>
            <!-- content -->
            <Container class="mt-1 md:mt-2">
                <slot />
            </Container>
            <!-- end content -->
        </div>
    </div>
</template>

<script setup>
import Container from "@frontend/Components/Container.vue";
import Topbar from "./Topbar.vue";
import NavigationMenuLogo from "@frontend/Components/NavigationMenuLogo.vue";
import NavigationMenuSearch from "@frontend/Components/NavigationMenuSearch.vue";
import NavigationMenuContent from "@frontend/Components/NavigationMenuContent.vue";
import NavigationSecondary from "@frontend/Components/NavigationSecondary.vue";
import AppHead from "@frontend/Pages/AppHead.vue";
</script>

<template>
    <AppHead />
    <div class="flex min-h-screen flex-col bg-white font-sans">
        <Topbar />
        <!-- primary navigation-->
        <nav class="border-b border-b-secondary p-2">
            <Container>
                <div class="flex items-center justify-between">
                    <NavigationMenuLogo />
                    <NavigationMenuSearch />
                    <NavigationMenuContent />
                </div>
            </Container>
        </nav>
        <!-- secondary navigation -->
        <nav class="mb-2 border-b border-b-secondary">
            <Container>
                <NavigationSecondary />
            </Container>
        </nav>
        <!-- content -->
        <Container>
            <slot />
        </Container>
        <!-- end content -->
    </div>
</template>

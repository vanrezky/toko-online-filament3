<script setup>
import Container from "./Container.vue";
import NavigationMenuContent from "./NavigationMenuContent.vue";
import NavigationMenuForm from "./NavigationMenuSearch.vue";
import NavigationMenuLogo from "./NavigationMenuLogo.vue";
</script>
<template>
    <Container>
        <div class="flex h-full items-center justify-between">
            <NavigationMenuLogo />
            <NavigationMenuForm />
            <NavigationMenuContent />
        </div>
    </Container>
</template>

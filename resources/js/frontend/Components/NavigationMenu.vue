<script setup>
import NavigationMenuContent from "./NavigationMenuContent.vue";
import NavigationMenuForm from "./NavigationMenuSearch.vue";
import NavigationMenuLogo from "./NavigationMenuLogo.vue";
</script>
<template>
    <div class="flex items-center justify-between">
        <NavigationMenuLogo />
        <NavigationMenuForm />
        <NavigationMenuContent />
    </div>
</template>

<script setup lang="ts">
import NavigationMenu from "@frontend/Components/NavigationMenu.vue";
import Topbar from "./Topbar.vue";
import NavigationSecond from "@frontend/Components/NavigationSecondary.vue";
</script>

<template>
    <div class="">
        <header class="flex flex-wrap border-b border-gray-200 pb-2 text-sm sm:flex-col sm:pb-0">
            <Topbar />
            <!-- start primary nav -->
            <nav class="box-border flex h-[80px] w-full flex-wrap justify-between border-b border-solid">
                <NavigationMenu />
            </nav>
            <!-- end primary nav -->
            <!-- start secondary nav -->
            <NavigationSecond />
            <!-- end secondary nav -->
        </header>
    </div>
</template>

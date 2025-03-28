<script setup>
import { PhMagnifyingGlass } from "@phosphor-icons/vue";
import Input from "./ui/input/Input.vue";
</script>
<template>
    <form class="relative w-full max-w-40 lg:max-w-md" action="#" method="GET">
        <Input
            type="text"
            name="search"
            placeholder="Search"
            class="without-ring w-full rounded-full bg-gray-100 py-2 pl-4 pr-10 text-sm shadow-sm duration-300 focus:bg-white focus:shadow-lg focus:outline-none"
        />
        <button type="submit" class="absolute inset-y-0 right-3 flex items-center">
            <PhMagnifyingGlass class="h-5 w-5 text-black" />
        </button>
    </form>
</template>

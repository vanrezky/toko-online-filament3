<script setup>
import { Search } from "lucide-vue-next";
</script>
<template>
    <div class="px-4 py-3">
        <label class="flex h-12 w-full min-w-40 flex-col">
            <div class="flex h-full w-full flex-1 items-stretch rounded-lg">
                <div
                    class="flex items-center justify-center rounded-l-lg border-r-0 border-none bg-[#f4f0f0] pl-4 text-[#886364]"
                    data-icon="MagnifyingGlass"
                    data-size="24px"
                    data-weight="regular"
                >
                    <Search />
                </div>
                <input
                    placeholder="Search Product.."
                    class="form-input flex h-full w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-l-none border-l-0 border-none bg-[#f4f0f0] px-4 pl-2 text-base font-normal leading-normal text-[#181111] placeholder:text-[#886364] focus:border-none focus:outline-0 focus:ring-0"
                    value=""
                />
            </div>
        </label>
    </div>
</template>

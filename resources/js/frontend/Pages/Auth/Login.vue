<script setup>
import HeaderNavigaton from "@frontend/components/HeaderNavigaton.vue";
import Layout from "@frontend/components/Layout.vue";
import { Link } from "@inertiajs/vue3";
import { route } from "ziggy-js";
</script>

<template>
    <Layout :bottom-navigation="false" title="Sign In" :backward-icon="null" backward-url="">
        <HeaderNavigaton title="Sign In" backward-icon="null" :backward-url="route('frontend.home')" />
        <h3 class="tracking-light px-4 pb-2 pt-5 text-left text-2xl font-bold leading-tight text-[#181111]">Welcome back</h3>
        <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
            <label class="flex min-w-40 flex-1 flex-col">
                <input
                    placeholder="Email"
                    class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border-none bg-[#f4f0f0] p-4 text-base font-normal leading-normal text-[#181111] placeholder:text-[#886364] focus:border-none focus:outline-0 focus:ring-0"
                    value=""
                />
            </label>
        </div>
        <div class="flex max-w-[480px] flex-wrap items-end gap-4 px-4 py-3">
            <label class="flex min-w-40 flex-1 flex-col">
                <input
                    placeholder="Password"
                    class="form-input flex h-14 w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg border-none bg-[#f4f0f0] p-4 text-base font-normal leading-normal text-[#181111] placeholder:text-[#886364] focus:border-none focus:outline-0 focus:ring-0"
                    value=""
                />
            </label>
        </div>
        <p class="px-4 pb-3 pt-1 text-sm font-normal leading-normal text-[#886364] underline">Forgot password?</p>
        <div class="flex px-4 py-3">
            <button
                class="flex h-12 min-w-[84px] max-w-[480px] flex-1 cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#e92932] px-5 text-base font-bold leading-normal tracking-[0.015em] text-white"
            >
                <span class="truncate">Sign in</span>
            </button>
        </div>
        <Link class="px-4 pb-3 pt-1 text-center text-sm font-normal leading-normal text-[#886364] underline" :href="route('frontend.signup')"
            >Don't have an account? Sign up</Link
        >
    </Layout>
</template>

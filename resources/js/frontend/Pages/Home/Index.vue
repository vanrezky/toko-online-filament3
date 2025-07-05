<script setup>
import CategoryCard from "@frontend/components/CategoryCard.vue";
import HorizontalScroller from "@frontend/components/HorizontalScroller.vue";
import Layout from "@frontend/components/Layout.vue";
import ProductCard from "@frontend/components/ProductCard.vue";
import SearchBar from "@frontend/components/SearchBar.vue";

const props = defineProps({
    title: {
        type: String,
        default: () => "",
    },
    products: {
        type: Object,
        default: () => ({}),
    },
    flashsales: {
        type: Object,
        default: () => ({}),
    },
    featuredCategories: {
        type: Object,
        default: () => ({}),
    },
    newArrivals: {
        type: Object,
        default: () => ({}),
    },
    sliders: {
        type: Object,
        default: () => ({}),
    },
});

const featured = [
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuDHm_RQpXTn1kI8Ma0pvtBLw-CKE1bkMRzjo-aC5KZ7qucVsC1RA3EcPYuyns8HG3VEkGjc_kaLdUkCkBGkMV_hU7tdgLwe0vU7lHpWB8Z-yJzTx299BX2BbuIbQMHrzV1kEJN0_w_oO8PSNnOMyTkfBrnF43ETnZNTgyt8y3JBWRndaNM8Am0-AV_4PTp610Y4Dyq02ZE8ZFL5VYt5ftVP9ZrhoWdTWUhvJgJtKBi6KcwaA90Fuqud4Tx6WqI7BMKMh7pDRSO8JiQ",
        title: "Modern Living Room",
    },
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuCh7TgXd06fabHaf3imM8O66o6dyY1pzGaMlLB6EZdMk4yVhmuJYeqjsrqQY_PLtI_3JBj2ZRMdrmB63ZgiHGGhqoz_fb84Kz_sYtAr31T9qnPKyJntJSbIng6ESOMbKW0OqtCemiAPzTfO05u9e2nMo3JsqgUYAsCk1OOsYPkCaXDWwfBPxZc4qgYxOyYYSeuq4vV-u1I_G5X1BiPXhu834A-vHflqkKDO4hhwOV7wQFfLFsm8-7U3QzfybT9nGt-z-2CVa6euV7U",
        title: "Cozy Bedroom",
    },
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuA_iVZ6UKu_5kIzOmBzAtHYBD-L-zMnyfvoVQhOnwR_k1fJLxveJ13ynQsO4u2iNjL0cl4PBGnpYffPEa8i6fDFvEiEUcPoJoGa2EvzNP_6BOUkjTN_7PGqFDGc4Vu5C3NMyY6GYpPUIU32_AnzeruhNlTMbBdEPPxUWuW1WXL2gmbXMsK-mx6CehQmBd1vMmYFlaW-ttIoqKqFvKKz_EY8OwIiZjLBck_ua5xJW2-C-J2s2cAwIloo9MXDkyFOPIpIf5Gg_etV1X4",
        title: "Minimalist Kitchen",
    },
];

const promotions = [
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuB76P1CxDXofB4fv1kuMRx8egOPPw7yvVVWWL2srvZg-9uYIYG7Ui0f1e7jy5xRJWKYpHfcqoTeeIlkt4wKp7FWNFVRGsJQYZO7q8_SZVfSNwWcOfxRilGOcEr2WhqkC4pUlPsL3iP2A_t1XyzHAsdfzmQvkR0UUKVjRKNVF5b2i4TCxtauve3U-KXXZEMeiX1Bw7xPhewoE4XmyJc8hXfbEiMyreEjuhLmLqVF6E8oPeZrOWLD6byrk2wDLjJhHrv7Y--4ILcwuiA",
        title: "Summer Sale",
    },
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuA6lUoa0YFmHrxvJZl7lpq-iYEQpZi_N28qNMvQQ4FScR0NxapJFTUtw1-L6VZyspfi21_KBZYxK3HKtwkRbteGJcr_0G1i6tJoCP_-dTfGVILcYWo0LsTyqEeaikhvr9lnlAFOdLPPQUlIAJzVETeXOd7frauy5Kbb5OGe3v_oF8y4rSIXIJy3dze0bhBYGFTEEMUkQHWEjWfm3Uaw10szS2FshYl6wuMuqZJuzGXqXPeAeTjMQBN1AaQFq_xL6YBnEWY4iy-tt14",
        title: "Back to School",
    },
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuDZyzIwNc4xVY54OTAJsVL7u-dvFctOJSQzZrNyxrlVjeuDx-FvbKgSAj22wk81L7fcaUyMpXdKAq8tCMrQvx9o31LdOdeV5qCeFPkL2UGG2q_HG4AcgqKJGA_TXMq-qYBqD5GYNcDSBzN5K-rzx8X0-LVupkE2YhOABE8Ah9DZKzQiFcruG8ctoSU8q6uWbgVL4_NGYdjxNgnLgyT1Vvu07TYim2FmLJtPcbwDw1iSD7D41BUSH8fSE17nbB-500uvez8iKimREeI",
        title: "Holiday Deals",
    },
];

const arrivals = [
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuBJjkruTfANg85AOvgczO7vOmYseHR-_uXNm-PhwTQXERO3ogqRZQcHEKDqKtoVEkBjmN1T2NMpsgVxMLbuOBpYuCoyDUM8uvda7pHus5Ti9K8lzwmi7CNaU9bygC04HJa3VVXvFXGl72iEejQEaJNt_admFW5Tnw4G7RvPL1bqmHT1soVdDRPKQbs48VYQ2XsOn43aDXQqN1zmO0lr19bvoRdQMOPCAIt7D8ZjL5AkKpa0q1-treFeER4ckjOD2l25XqWNIRlJ5gY",
        title: "Product a",
        price: "100.000",
    },
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuBJeKC6XqV285DgV_T8c71fVwpXzLoc-NhRr6AIxVa_3Rk-ydMUPCAbM2zEjzsjtD-fJCU48xo6dwuaC9ZTcKNA9nQzFK5-DFvH4XdPq0_fThx-b2bONdrYLD3cluMsWffUBeHBdkmS7Kf1VY6NmezfQv2Ux976osJa5E8omEX7x7F2iR-RIBl8YeeYjA7M4xuIVUx-6DwyWW_xxmLzFxktz6Irwe2h7-WtUVTY8ZFKkZ86NJOFFICSfZ7hx9Ug0UaUZwakfDJAXUY",
        title: "Product b",
        price: "155.100",
    },
    {
        image: "https://lh3.googleusercontent.com/aida-public/AB6AXuB03osy8fJF8YJy9wUwPqy7rDzXJZ41C-fN74xpwwx7TUFUWE8L3Zl5Q94OIcGKlsV2oWYrsHfD4tFgvCyJkhkIw4F9Uv5hXZGM4jKCTOQT4D4NFdF1zs-IALTUV-I15ND9poFEB61rg-1TJB2R47-rkfiAt_FG9HpD3htJ21UwARPBae2HqlvyG8KSgwvmPS5YpiEBSgGl2-9OVNkvLwbyoWxBqn5vIEk-2vjtMKvAcfRIZX2LBw8oukbeg7VGnw5Hi_ZXvw_udjk",
        title: "Product c",
        price: "92.000",
    },
];
</script>

<template>
    <Layout :title="title">
        <div class="relative flex size-full min-h-screen flex-col justify-between overflow-x-hidden bg-white">
            <div>
                <!-- search bar -->
                <SearchBar />
                <HorizontalScroller title="Featured">
                    <ProductCard v-for="(item, i) in featured" :key="i" v-bind="item" />
                </HorizontalScroller>

                <HorizontalScroller :title="flashsales.name" v-if="flashsales">
                    <ProductCard
                        v-for="(item, i) in flashsales.products"
                        :key="i"
                        :image="item.product.thumbnail"
                        :title="item.product.name"
                        :price="item.product.price"
                    />
                </HorizontalScroller>

                <div class="px-4 pb-3 pt-5">
                    <h2 class="text-[22px] font-bold text-[#181111]">Featured Categories</h2>
                    <div class="mt-3 grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3">
                        <CategoryCard v-for="(cat, i) in featuredCategories" :key="i" :image="cat.image_url" :title="cat.name" />
                    </div>
                </div>

                <HorizontalScroller title="New Arrivals">
                    <ProductCard
                        v-for="(item, i) in newArrivals"
                        :key="i"
                        :image="item.thumbnail"
                        :title="item.name"
                        :price="item.price"
                        type="rectacle"
                    />
                </HorizontalScroller>
            </div>
        </div>
    </Layout>
</template>

<script setup>
const props = defineProps({
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
    sliders: {
        type: Object,
        default: () => ({}),
    },
});
</script>

<template>
    <AppHead :title="title" />
    <div class="bg-gray-100">
        <div
            class="group/design-root relative mx-auto flex size-full min-h-screen w-full max-w-sm flex-col justify-between overflow-x-hidden bg-white pb-10"
        >
            <div>
                <div class="@container">
                    <div class="@[480px]:p-4">
                        <div
                            class="@[480px]:gap-8 @[480px]:rounded-lg @[480px]:px-10 flex min-h-[480px] flex-col items-start justify-end gap-6 bg-cover bg-center bg-no-repeat px-4 pb-10"
                            style="
                                background-image:
                                    linear-gradient(rgba(0, 0, 0, 0.1) 0%, rgba(0, 0, 0, 0.4) 100%),
                                    url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuARR3OT7t5EZ-XcO2Tt92lwKihbgl1XhhztirnBL-DtvpbARbKyeH6lWoBEwDzNcoVNKC6CFgZr08Xc17BgKOSQGoHw6ZUSXaNgO1DKwwEbHCuMcS1s0_Tw8UahB1Ev4vqLtfam279TdLZTTnk8nbvt6yfYq5yNumhhCkwRcXvLHLgBjb-xJL-Rf7xS4N_veSz5LUPyuaq08NwNvAudPoKF03J7Reivqex71ChKD4BrmtKpyY1LK2F9UW1BuMtklOEWPisoRMZbVZmT&quot;);
                            "
                        >
                            <div class="flex flex-col gap-2 text-left">
                                <h1
                                    class="@[480px]:text-5xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em] text-4xl font-black leading-tight tracking-[-0.033em] text-white"
                                >
                                    Elevate Your Style
                                </h1>
                                <h2
                                    class="@[480px]:text-base @[480px]:font-normal @[480px]:leading-normal text-sm font-normal leading-normal text-white"
                                >
                                    Discover the latest trends and timeless classics in fashion, electronics, and more.
                                </h2>
                            </div>
                            <button
                                class="@[480px]:h-12 @[480px]:px-5 @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em] flex h-10 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#0c7ff2] px-4 text-sm font-bold leading-normal tracking-[0.015em] text-white"
                            >
                                <span class="truncate">Shop Now</span>
                            </button>
                        </div>
                    </div>
                </div>
                <h2 class="px-4 pb-3 pt-5 text-[22px] font-bold leading-tight tracking-[-0.015em] text-[#111418]">Featured Collections</h2>
                <div class="[&amp;::-webkit-scrollbar]:hidden flex overflow-y-auto [-ms-scrollbar-style:none] [scrollbar-width:none]">
                    <div class="flex items-stretch gap-3 p-4">
                        <div class="flex h-full min-w-60 flex-1 flex-col gap-4 rounded-lg">
                            <div
                                class="flex aspect-square w-full flex-col rounded-lg bg-cover bg-center bg-no-repeat"
                                style="
                                    background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuATjppNOeLYRLxcx97dbodUdjNoNvSDZqH88gN-WYQJJwi0XS5SPxn3S2iHGcr3ClwHihAnR6p7sKvNfZusCYRg85F8A-Zk6uiRgaqFUTyae3bgsRmv49K3zq-GhMU9qbKwBGlOTY0e1uwrbSU7n7EPYmRB6NzgFREgUVfJLPM_2EMR2LsEcb59X1Oi-ivzuc24hoVZzjkdgYsDK-7BkyYbVb1MQxJ6K9hu0Bw2KVGRPoWliXSJB6Dl6VZwIWK5TbckCjUVfYSl9Hmt&quot;);
                                "
                            ></div>
                            <div>
                                <p class="text-base font-medium leading-normal text-[#111418]">New Arrivals</p>
                                <p class="text-sm font-normal leading-normal text-[#60758a]">Explore the freshest styles</p>
                            </div>
                        </div>
                        <div class="flex h-full min-w-60 flex-1 flex-col gap-4 rounded-lg">
                            <div
                                class="flex aspect-square w-full flex-col rounded-lg bg-cover bg-center bg-no-repeat"
                                style="
                                    background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuAiw6GiGQBrUCXwEbjkdqhCYFYaNOeP6vyIcSMJkTXO7QqvWc6Hl6Kx8yUQtJ4h3Br4bMHN-VTeb4xBgO5-7cey2ZpNTKzZ9bkC5iWrqPpOC145LOtWbT2GAhQ0sfOqF5idr881bS_49WDOZr4PFBXo1zmPY6UPKD1BEoLVTl7gUwSjQnQGO5agCxZxMn-TAMOofs9XIy4cCZnDFwkTJKH43wrbJXMzXMGibvKfqWHtli44liiHbcHh5-8UNHnGlvSypat1d23b-G1t&quot;);
                                "
                            ></div>
                            <div>
                                <p class="text-base font-medium leading-normal text-[#111418]">Tech Essentials</p>
                                <p class="text-sm font-normal leading-normal text-[#60758a]">Must-have gadgets for tech enthusiasts</p>
                            </div>
                        </div>
                        <div class="flex h-full min-w-60 flex-1 flex-col gap-4 rounded-lg">
                            <div
                                class="flex aspect-square w-full flex-col rounded-lg bg-cover bg-center bg-no-repeat"
                                style="
                                    background-image: url(&quot;https://lh3.googleusercontent.com/aida-public/AB6AXuDxwwOjr1ANIgciZ5jEQK_olcb3anKVEPXpQOnRpijUMtXpq28Qj58httBjd3OKP8eaO5-jhgrHE5xffFjtIgeFikVze9e2VBuf7E_j_zfo7Ey6dCZTVm_1UQ16CBphVHs56Hnb9dVQCKWAXUWDTNtwVwt_jldDjP0wxK9yESfuBbbltDMSewFHB8HpnFMylmRb5ANqc5QnymV3j2RyWwKk-6fqJTBK76OAFyPS042-yKgj_n3euDnPwnxwlzgzArYKsJnwtwDcbjK_&quot;);
                                "
                            ></div>
                            <div>
                                <p class="text-base font-medium leading-normal text-[#111418]">Digital Downloads</p>
                                <p class="text-sm font-normal leading-normal text-[#60758a]">Exclusive digital art and resources</p>
                            </div>
                        </div>
                    </div>
                </div>
                <h2 class="px-4 pb-3 pt-5 text-[22px] font-bold leading-tight tracking-[-0.015em] text-[#111418]">Shop by Category</h2>
                <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-3 p-4">
                    <div class="flex flex-1 items-center gap-3 rounded-lg border border-[#dbe0e6] bg-white p-4">
                        <div class="text-[#111418]" data-icon="TShirt" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M247.59,61.22,195.83,33A8,8,0,0,0,192,32H160a8,8,0,0,0-8,8,24,24,0,0,1-48,0,8,8,0,0,0-8-8H64a8,8,0,0,0-3.84,1L8.41,61.22A15.76,15.76,0,0,0,1.82,82.48l19.27,36.81A16.37,16.37,0,0,0,35.67,128H56v80a16,16,0,0,0,16,16H184a16,16,0,0,0,16-16V128h20.34a16.37,16.37,0,0,0,14.58-8.71l19.27-36.81A15.76,15.76,0,0,0,247.59,61.22ZM35.67,112a.62.62,0,0,1-.41-.13L16.09,75.26,56,53.48V112ZM184,208H72V48h16.8a40,40,0,0,0,78.38,0H184Zm36.75-96.14a.55.55,0,0,1-.41.14H200V53.48l39.92,21.78Z"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold leading-tight text-[#111418]">Clothing</h2>
                    </div>
                    <div class="flex flex-1 items-center gap-3 rounded-lg border border-[#dbe0e6] bg-white p-4">
                        <div class="text-[#111418]" data-icon="Sneaker" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M228.65,129.11l-60.73-20.24a24,24,0,0,1-14.32-13L130.39,41.6s0-.07,0-.1A16,16,0,0,0,110.25,33L34.53,60.49A16.05,16.05,0,0,0,24,75.53V192a16,16,0,0,0,16,16H240a16,16,0,0,0,16-16V167.06A40,40,0,0,0,228.65,129.11ZM115.72,48l7.11,16.63-21.56,7.85A8,8,0,0,0,104,88a7.91,7.91,0,0,0,2.73-.49l22.4-8.14,4.74,11.07-16.6,6A8,8,0,0,0,120,112a7.91,7.91,0,0,0,2.73-.49l17.6-6.4a40.24,40.24,0,0,0,7.68,10l-14.74,5.36A8,8,0,0,0,136,136a8.14,8.14,0,0,0,2.73-.48l28-10.18,56.87,18.95A24,24,0,0,1,238.93,160H40V75.53ZM40,192h0V176H240v16Z"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold leading-tight text-[#111418]">Shoes</h2>
                    </div>
                    <div class="flex flex-1 items-center gap-3 rounded-lg border border-[#dbe0e6] bg-white p-4">
                        <div class="text-[#111418]" data-icon="Laptop" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M232,168h-8V72a24,24,0,0,0-24-24H56A24,24,0,0,0,32,72v96H24a8,8,0,0,0-8,8v16a24,24,0,0,0,24,24H216a24,24,0,0,0,24-24V176A8,8,0,0,0,232,168ZM48,72a8,8,0,0,1,8-8H200a8,8,0,0,1,8,8v96H48ZM224,192a8,8,0,0,1-8,8H40a8,8,0,0,1-8-8v-8H224ZM152,88a8,8,0,0,1-8,8H112a8,8,0,0,1,0-16h32A8,8,0,0,1,152,88Z"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold leading-tight text-[#111418]">Electronics</h2>
                    </div>
                    <div class="flex flex-1 items-center gap-3 rounded-lg border border-[#dbe0e6] bg-white p-4">
                        <div class="text-[#111418]" data-icon="MusicNote" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M210.3,56.34l-80-24A8,8,0,0,0,120,40V148.26A48,48,0,1,0,136,184V98.75l69.7,20.91A8,8,0,0,0,216,112V64A8,8,0,0,0,210.3,56.34ZM88,216a32,32,0,1,1,32-32A32,32,0,0,1,88,216ZM200,101.25l-64-19.2V50.75L200,70Z"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold leading-tight text-[#111418]">Music</h2>
                    </div>
                    <div class="flex flex-1 items-center gap-3 rounded-lg border border-[#dbe0e6] bg-white p-4">
                        <div class="text-[#111418]" data-icon="Image" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M216,40H40A16,16,0,0,0,24,56V200a16,16,0,0,0,16,16H216a16,16,0,0,0,16-16V56A16,16,0,0,0,216,40Zm0,16V158.75l-26.07-26.06a16,16,0,0,0-22.63,0l-20,20-44-44a16,16,0,0,0-22.62,0L40,149.37V56ZM40,172l52-52,80,80H40Zm176,28H194.63l-36-36,20-20L216,181.38V200ZM144,100a12,12,0,1,1,12,12A12,12,0,0,1,144,100Z"
                                ></path>
                            </svg>
                        </div>
                        <h2 class="text-base font-bold leading-tight text-[#111418]">Art</h2>
                    </div>
                </div>
                <div class="@container">
                    <div class="@[480px]:gap-8 @[480px]:px-10 @[480px]:py-20 flex flex-col justify-end gap-6 px-4 py-10">
                        <div class="flex flex-col gap-2 text-center">
                            <h1
                                class="tracking-light @[480px]:text-4xl @[480px]:font-black @[480px]:leading-tight @[480px]:tracking-[-0.033em] max-w-[720px] text-[32px] font-bold leading-tight text-[#111418]"
                            >
                                Stay in the Loop
                            </h1>
                            <p class="max-w-[720px text-base font-normal leading-normal text-[#111418]">
                                Sign up for our newsletter to get exclusive offers and updates.
                            </p>
                        </div>
                        <div class="flex flex-1 justify-center">
                            <label class="@[480px]:h-16 flex h-14 min-w-40 max-w-[480px] flex-1 flex-col">
                                <div class="flex h-full w-full flex-1 items-stretch rounded-lg">
                                    <input
                                        placeholder="Your email"
                                        class="form-input @[480px]:text-base @[480px]:font-normal @[480px]:leading-normal flex h-full w-full min-w-0 flex-1 resize-none overflow-hidden rounded-lg rounded-r-none border-r-0 border-none bg-[#f0f2f5] px-4 pr-2 text-sm font-normal leading-normal text-[#111418] placeholder:text-[#60758a] focus:border-none focus:outline-0 focus:ring-0"
                                        value=""
                                    />
                                    <div class="flex items-center justify-center rounded-r-lg border-l-0 border-none bg-[#f0f2f5] pr-2">
                                        <button
                                            class="@[480px]:h-12 @[480px]:px-5 @[480px]:text-base @[480px]:font-bold @[480px]:leading-normal @[480px]:tracking-[0.015em] flex h-10 min-w-[84px] max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-lg bg-[#0c7ff2] px-4 text-sm font-bold leading-normal tracking-[0.015em] text-white"
                                        >
                                            <span class="truncate">Subscribe</span>
                                        </button>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fixed bottom-0 z-50 h-16 w-full max-w-sm shadow-lg rounded-xl">
                <div class="flex gap-2 border-t border-[#f0f2f5] bg-white px-4 pb-3 pt-2">
                    <a class="just flex flex-1 flex-col items-center justify-end gap-1 rounded-full text-[#111418]" href="#">
                        <div class="flex h-8 items-center justify-center text-[#111418]" data-icon="House" data-size="24px" data-weight="fill">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M224,115.55V208a16,16,0,0,1-16,16H168a16,16,0,0,1-16-16V168a8,8,0,0,0-8-8H112a8,8,0,0,0-8,8v40a16,16,0,0,1-16,16H48a16,16,0,0,1-16-16V115.55a16,16,0,0,1,5.17-11.78l80-75.48.11-.11a16,16,0,0,1,21.53,0,1.14,1.14,0,0,0,.11.11l80,75.48A16,16,0,0,1,224,115.55Z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-xs font-medium leading-normal tracking-[0.015em] text-[#111418]">Home</p>
                    </a>
                    <a class="just flex flex-1 flex-col items-center justify-end gap-1 text-[#60758a]" href="#">
                        <div
                            class="flex h-8 items-center justify-center text-[#60758a]"
                            data-icon="MagnifyingGlass"
                            data-size="24px"
                            data-weight="regular"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M229.66,218.34l-50.07-50.06a88.11,88.11,0,1,0-11.31,11.31l50.06,50.07a8,8,0,0,0,11.32-11.32ZM40,112a72,72,0,1,1,72,72A72.08,72.08,0,0,1,40,112Z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-xs font-medium leading-normal tracking-[0.015em] text-[#60758a]">Shop</p>
                    </a>
                    <a class="just flex flex-1 flex-col items-center justify-end gap-1 text-[#60758a]" href="#">
                        <div class="flex h-8 items-center justify-center text-[#60758a]" data-icon="Heart" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M178,32c-20.65,0-38.73,8.88-50,23.89C116.73,40.88,98.65,32,78,32A62.07,62.07,0,0,0,16,94c0,70,103.79,126.66,108.21,129a8,8,0,0,0,7.58,0C136.21,220.66,240,164,240,94A62.07,62.07,0,0,0,178,32ZM128,206.8C109.74,196.16,32,147.69,32,94A46.06,46.06,0,0,1,78,48c19.45,0,35.78,10.36,42.6,27a8,8,0,0,0,14.8,0c6.82-16.67,23.15-27,42.6-27a46.06,46.06,0,0,1,46,46C224,147.61,146.24,196.15,128,206.8Z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-xs font-medium leading-normal tracking-[0.015em] text-[#60758a]">Wishlist</p>
                    </a>
                    <a class="just flex flex-1 flex-col items-center justify-end gap-1 text-[#60758a]" href="#">
                        <div class="flex h-8 items-center justify-center text-[#60758a]" data-icon="User" data-size="24px" data-weight="regular">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24px" height="24px" fill="currentColor" viewBox="0 0 256 256">
                                <path
                                    d="M230.92,212c-15.23-26.33-38.7-45.21-66.09-54.16a72,72,0,1,0-73.66,0C63.78,166.78,40.31,185.66,25.08,212a8,8,0,1,0,13.85,8c18.84-32.56,52.14-52,89.07-52s70.23,19.44,89.07,52a8,8,0,1,0,13.85-8ZM72,96a56,56,0,1,1,56,56A56.06,56.06,0,0,1,72,96Z"
                                ></path>
                            </svg>
                        </div>
                        <p class="text-xs font-medium leading-normal tracking-[0.015em] text-[#60758a]">Account</p>
                    </a>
                </div>
                <div class="h-5 bg-white"></div>
            </div>
        </div>
    </div>
</template>

<footer class="w-full bg-primary mt-24">
    <div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 lg:pt-20 mx-auto">
        <!-- Grid -->
        <div class="grid grid-cols-2 gap-6 md:grid-cols-4 lg:grid-cols-5">
            <div class="col-span-full lg:col-span-1">
                <a class="flex-none text-4xl font-semibold text-white" href="/" wire:navigate aria-label="Brand">
                    @if (settings('logo'))
                        <img src="{{ settings('logo') }}" alt="{{ settings('site_name') }}" class="max-w-36 h-auto">
                    @else
                        {{ settings('site_name') }}
                    @endif
                </a>
            </div>
            <!-- End Col -->

            <div class="col-span-1">
                <h4 class="font-semibold text-white text-xl">Product</h4>
                <div class="grid mt-3 space-y-3">
                    <x-footer-link href="/categories" wire:navigate>Categories</x-footer-link>
                    <x-footer-link href="/products" wire:navigate>All Products</x-footer-link>
                    <x-footer-link href="/products?featured=true" wire:navigate>Featured</x-footer-link>

                </div>
            </div>
            <!-- End Col -->

            <div class="col-span-1">
                <h4 class="font-semibold text-white text-xl">Company</h4>

                <div class="grid mt-3 space-y-3">
                    <x-footer-link href="/about" wire:navigate>About Us</x-footer-link>
                    <x-footer-link href="/blog" wire:navigate>Blog</x-footer-link>
                    <x-footer-link href="/customer" wire:navigate>Customer</x-footer-link>
                </div>
            </div>
            <!-- End Col -->

            <div class="col-span-2">
                <h4 class="font-semibold text-gray-100">Stay up to date</h4>
                <form>
                    <div class="flex flex-col items-center gap-2 p-2 mt-4 bg-white rounded-lg sm:flex-row sm:gap-3 ">
                        <div class="w-full">
                            <input type="text" id="hero-input" name="hero-input" class="block w-full px-4 py-3 text-sm border-transparent rounded-lg disabled:opacity-50 disabled:pointer-events-none form-primary" placeholder="Enter your email">
                        </div>
                        <a class="inline-flex items-center justify-center w-full p-3 text-sm font-semibold text-white bg-primary border border-transparent rounded-lg sm:w-auto whitespace-nowrap gap-x-2 hover:bg-blue-500 disabled:opacity-50 disabled:pointer-events-none"
                            href="#">
                            Subscribe
                        </a>
                    </div>

                </form>
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->

        <div class="grid mt-5 sm:mt-12 gap-y-2 sm:gap-y-0 sm:flex sm:justify-between sm:items-center">
            <div class="flex items-center justify-between">
                <p class="text-sm text-white">© {{ date('Y') }} {{ settings('site_name') }}. All rights reserved.</p>
            </div>
            <!-- End Col -->

            <!-- Social Brands -->
            <div>
                <x-footer-social-link href="#">
                    <x-tabler-brand-facebook class="size-5" />
                </x-footer-social-link>
                <x-footer-social-link href="#">
                    <x-tabler-brand-instagram class="size-5" />
                </x-footer-social-link>
                <x-footer-social-link href="#">
                    <x-tabler-brand-x class="size-5" />
                </x-footer-social-link>
                <x-footer-social-link href="#">
                    <x-tabler-brand-github class="size-5" />
                </x-footer-social-link>
            </div>
            <!-- End Social Brands -->
        </div>
    </div>
</footer>

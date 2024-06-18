<footer class="w-full bg-black mt-24">
    <div class="w-full max-w-[85rem] py-10 px-4 sm:px-6 lg:px-8 lg:pt-20 mx-auto">
        <!-- Grid -->
        <div class="grid grid-cols-2 gap-6 md:grid-cols-4 lg:grid-cols-5">
            <div class="col-span-full lg:col-span-1">
                <a class="flex-none  font-semibold text-text" href="/" wire:navigate aria-label="Brand">
                    @if (settings('logo'))
                        <img src="{{ settings('logo') }}" alt="{{ settings('site_name') }}" class="max-w-36 h-auto">
                    @else
                        <span class="text-3xl">{{ settings('site_name') }}</span>
                    @endif
                </a>

                <p class="mt-3 text-sm text-text">{{ settings('site_tag_line') }}</p>
            </div>
            <!-- End Col -->

            <div class="col-span-1">
                <h4 class="font-semibold text-text text-xl">Support</h4>
                <div class="grid mt-3 space-y-3">
                    <x-footer-link href="#">{{ settings('address') }}</x-footer-link>
                    <x-footer-link href="#" target="_blank">{{ settings('phone') }}</x-footer-link>
                    <x-footer-link href="/products?featured=true" target="_blank">{{ settings('wa_phone') }}</x-footer-link>

                </div>
            </div>
            <!-- End Col -->

            <div class="col-span-1">
                <h4 class="font-semibold text-text text-xl">Quick Link</h4>

                <div class="grid mt-3 space-y-3">
                    <x-footer-link href="/about" wire:navigate>About Us</x-footer-link>
                    <x-footer-link href="/blog" wire:navigate>Blogs</x-footer-link>
                    <x-footer-link href="/customer" wire:navigate>Customer</x-footer-link>
                    <x-footer-link href="/privacy-policy" wire:navigate>Privacy Policy</x-footer-link>
                    <x-footer-link href="/tos" wire:navigate>Terms Of Use</x-footer-link>
                    <x-footer-link href="/faq" wire:navigate>FAQ</x-footer-link>
                    <x-footer-link href="/contact" wire:navigate>Contact</x-footer-link>
                </div>
            </div>
            <!-- End Col -->

            <div class="col-span-2">
                <h4 class="font-semibold text-gray-100">Stay up to date</h4>
                <form action="#" method="POST">
                    <div class="flex flex-col items-center gap-2 p-2 mt-4 bg-white rounded-lg sm:flex-row sm:gap-3 ">
                        <div class="w-full">
                            <x-form-input class="px-4 py-3" name="email" type="email" placeholder="Enter your email" />
                        </div>
                        <x-button type="submit">Subscribe</x-button>

                    </div>

                </form>
            </div>
            <!-- End Col -->
        </div>
        <!-- End Grid -->

        <div class="grid mt-5 sm:mt-12 gap-y-2 sm:gap-y-0 sm:flex sm:justify-between sm:items-center">
            <div class="flex items-center justify-between">
                <p class="text-sm text-text">© {{ date('Y') }} {{ settings('site_name') }}. All rights reserved.</p>
            </div>
            <!-- End Col -->

            <!-- Social Brands -->
            <div>
                <x-footer-social-link href="{{ settings('facebook') }}" target="_blank">
                    <x-tabler-brand-facebook class="size-5" />
                </x-footer-social-link>
                <x-footer-social-link href="{{ settings('instagram') }}" target="_blank">
                    <x-tabler-brand-instagram class="size-5" />
                </x-footer-social-link>
                <x-footer-social-link href="{{ settings('twitter') }}" target="_blank">
                    <x-tabler-brand-x class="size-5" />
                </x-footer-social-link>
            </div>
            <!-- End Social Brands -->
        </div>
    </div>
</footer>

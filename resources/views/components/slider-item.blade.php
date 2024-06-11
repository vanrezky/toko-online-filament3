@props(['slider' => ''])
<div class="hs-carousel-slide" wire:ignore>
    <div class="h-[8rem] md:h-[15rem] lg:h-[25rem] flex flex-col bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $slider }}');">
        {{-- <div class="w-2/3 pb-5 mt-auto md:max-w-lg ps-5 md:ps-10 md:pb-10">
            <span class="block text-white">Nike React</span>
            <span class="block text-xl text-white md:text-3xl">Rewriting sport's playbook for billions of athletes</span>
            <div class="mt-5">
                <a class="inline-flex items-center px-3 py-2 text-sm font-medium text-black bg-white border border-transparent rounded gap-x-2 hover:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none" href="#">
                    Read Case Studies
                </a>
            </div>
        </div> --}}
    </div>
</div>

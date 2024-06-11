<div class="app-container">

    <div class="container-content mt-2 sm:mt-16">
        <div class="flex flex-col md:flex-row bg-white rounded-lg border overflow-hidden max-w-4xl mx-auto">

            <!-- Login Form Section -->
            <div class="hidden md:block w-1/2" style="background-image: url('{{ asset('assets/frontends/images/login.png') }}')">
                <img src="{{ asset('assets/frontends/images/login.png') }}" class="w-full h-full" alt="Login Image">
            </div>
            <div class="w-full md:w-1/2 p-8">
                <h2 class="text-2xl font-bold text-center mb-8">{{ $title }}</h2>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

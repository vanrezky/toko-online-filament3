<div class="app-container">

    <div class="container-content mt-16">
        <div class="flex flex-col md:flex-row bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto">

            <!-- Login Form Section -->
            <div class="w-1/2" style="background-image: url('{{ asset('assets/frontends/images/login.png') }}')">
                <img src="{{ asset('assets/frontends/images/login.png') }}" class="w-full h-full" alt="Login Image">
            </div>
            <div class="w-1/2 p-8">
                <h2 class="text-2xl font-bold text-center mb-8">Sign In to {{ settings('site_name') }}</h2>
                <form action="#" method="POST">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email/Username</label>
                        <input type="text" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <input type="checkbox" id="remember" name="remember" class="mr-2">
                            <label for="remember" class="text-gray-700">Remember me</label>
                        </div>
                        <a href="/forgot" wire:navigate class="text-primary">Forgot Password?</a>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary-dark focus:ring-offset-2">Login</button>
                </form>
                <p class="text-center text-gray-700 mt-8">Don't have an account? <a href="/register" wire:navigate class="text-primary">Sign Up</a></p>
            </div>
        </div>
    </div>
</div>

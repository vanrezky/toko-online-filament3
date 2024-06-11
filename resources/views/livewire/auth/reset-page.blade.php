<div class="app-container">

    <div class="container-content mt-16">
        <div class="flex flex-col md:flex-row bg-white rounded-lg shadow-lg overflow-hidden max-w-4xl mx-auto">

            <!-- Login Form Section -->
            <div class="w-1/2" style="background-image: url('{{ asset('assets/frontends/images/login.png') }}')">
                <img src="{{ asset('assets/frontends/images/login.png') }}" class="w-full h-full" alt="Login Image">
            </div>
            <div class="w-1/2 p-8">
                <h2 class="text-2xl font-bold text-center mb-8">Reset Password</h2>
                <form action="#" method="POST">
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent" required>
                    </div>
                    <button type="submit" class="w-full bg-primary text-white py-2 rounded-lg hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary-dark focus:ring-offset-2">Reset Password</button>
                </form>
                <p class="text-center text-gray-700 mt-8">Already have account? <a href="/login" wire:navigate class="text-primary">Sign In</a></p>
            </div>
        </div>
    </div>
</div>

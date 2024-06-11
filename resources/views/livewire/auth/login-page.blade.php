<x-auth-container title="Sign In">
    <form wire:submit="login">
        <x-form-input label="Email/Username" name="email" type="text" wire:model="email" />
        <x-form-input label="Password" name="password" type="password" parent-class="mb-6" wire:model="password" />

        <div class="flex items-center justify-between mb-6">
            <div>
                <input type="checkbox" id="remember" name="remember" wire:model="remember" value="1" class="mr-2">
                <label for="remember" class="text-gray-700">Remember me</label>
            </div>
            <a href="/forgot" wire:navigate class="text-primary">Forgot Password?</a>
        </div>
        <x-button type="submit" class="btn-primary">Login</x-button>
    </form>
    <p class="text-center text-gray-700 mt-8">Don't have an account? <a href="/register" wire:navigate class="text-primary">Sign Up</a></p>
</x-auth-container>

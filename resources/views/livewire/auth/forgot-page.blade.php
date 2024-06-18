<x-auth-container title="Forgot Password">
    <form action="#" method="POST">
        <x-form-input label="Email" name="email" type="email" required placeholder="Enter your email" autofocus />
        <x-button class="w-full">Forgot Password</x-button>
    </form>
    <p class="text-center text-gray-700 mt-8">Already have account? <a href="/login" wire:navigate class="text-secondary2">Sign In</a></p>
</x-auth-container>

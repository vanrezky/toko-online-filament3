<x-auth-container title="Reset Password">
    <form action="#" method="POST">
        <x-form-input label="New Password" name="password" type="password" required autofocus />
        <x-form-input label="Confirm Password" name="confirm_password" type="password" required />
        <x-button type="submit" class="w-full">Set Password</x-button>
    </form>
    <p class="text-center text-gray-700 mt-8">Already have account? <a href="/login" wire:navigate class="text-secondary2">Sign In</a></p>
</x-auth-container>

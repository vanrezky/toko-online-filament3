<x-auth-container title="Sign Up">
    <form wire:submit="save">
        <x-form-input label="Name" name="name" type="text" required wire:model="first_name" autofocus />
        <x-form-input label="Email" name="email" type="email" required wire:model="email" />
        <x-form-input label="Password" name="password" type="password" required wire:model="password" />
        <x-form-input label="Confirm Password" name="password_confirmation" type="password" required wire:model="password_confirmation" />
        <x-button type="submit" class="btn-primary">Register</x-button>
    </form>
    <p class="text-center text-gray-700 mt-8">Already have account? <a href="/login" wire:navigate class="text-primary">Sign In</a></p>
</x-auth-container>

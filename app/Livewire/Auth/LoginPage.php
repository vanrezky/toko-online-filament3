<?php

namespace App\Livewire\Auth;

use App\Livewire\HomePage;
use Exception;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Masmerise\Toaster\Toast;

class LoginPage extends Component
{

    #[Validate('required')]
    public $email;

    #[Validate('required')]
    public $password;

    #[Validate('sometimes|bool')]
    public $remember = false;

    protected $maxAttempts = 5;
    protected $decayMinutes = 1; // 1 minutes

    public function login(): void
    {
        $this->validate();

        try {
            $this->check();
        } catch (\Exception $e) {
            Toaster::error($e->getMessage());
            return;
        }

        Toaster::success('Logged in successfully!');
        $this->redirect(HomePage::class, navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login-page');
    }

    protected function check(): bool
    {
        if (RateLimiter::tooManyAttempts($this->throttoleKey(), $this->maxAttempts)) {
            throw new Exception('Too many attempts. Please try again later.');
        }

        if (!auth()->guard('customer')->attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            // check using username
            if (!auth()->guard('customer')->attempt(['username' => $this->email, 'password' => $this->password], $this->remember)) {
                RateLimiter::hit($this->throttoleKey(), $this->decayMinutes * 60);
                throw new Exception('Invalid credentials.');
            }
        }
        RateLimiter::clear($this->throttoleKey());
        return true;
    }

    protected function throttoleKey(): string
    {
        return Str::lower($this->email)  . '|' . request()->ip();
    }
}

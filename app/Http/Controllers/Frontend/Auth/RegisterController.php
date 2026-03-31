<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class RegisterController extends Controller
{
    protected int $maxAttempts = 5;

    protected int $decayMinutes = 30;

    public function __invoke(Request $request)
    {
        return Inertia::render('Auth/Register', [
            'secure_password' => settings('secure_password'),
        ]);
    }

    public function register(Request $request)
    {
        if ($this->hasTooManyAttempts($request)) {
            $this->sendLockoutResponse($request);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => ['required', 'confirmed', securePassword(8)],
        ]);

        $customer = Customer::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        $this->clearAttempts($request);

        Auth::guard('customer')->login($customer);

        return redirect()->route('frontend.home');
    }

    protected function hasTooManyAttempts(Request $request): bool
    {
        return app(RateLimiter::class)->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts
        );
    }

    protected function incrementAttempts(Request $request): void
    {
        app(RateLimiter::class)->hit(
            $this->throttleKey($request),
            $this->decayMinutes * 60
        );
    }

    protected function clearAttempts(Request $request): void
    {
        app(RateLimiter::class)->clear($this->throttleKey($request));
    }

    protected function sendLockoutResponse(Request $request): void
    {
        $seconds = app(RateLimiter::class)->availableIn(
            $this->throttleKey($request)
        );

        throw ValidationException::withMessages([
            'email' => [
                __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ],
        ]);
    }

    protected function throttleKey(Request $request): string
    {
        return 'register|'.$request->ip();
    }
}

<?php

namespace App\Http\Controllers\Frontend\Auth;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

trait ThrottlesAuth
{
    protected function hasTooManyLoginAttempts(Request $request): bool
    {
        return app(RateLimiter::class)->tooManyAttempts(
            $this->throttleKey($request),
            $this->maxAttempts()
        );
    }

    protected function incrementLoginAttempts(Request $request): void
    {
        app(RateLimiter::class)->hit(
            $this->throttleKey($request),
            $this->decayMinutes() * 60
        );
    }

    protected function clearLoginAttempts(Request $request): void
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
        return mb_strtolower($request->input('email').'|'.$request->ip());
    }

    protected function maxAttempts(): int
    {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    protected function decayMinutes(): int
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }
}

<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
    protected int $maxAttempts = 3;

    protected int $decayMinutes = 5;

    public function __invoke(): Response
    {
        return Inertia::render('Auth/ForgotPassword');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'guard' => 'sometimes|string|in:customer,user',
        ]);

        $guard = $request->input('guard', 'customer');

        if ($this->hasTooManyAttempts($request)) {
            $this->sendLockoutResponse($request);
        }

        $model = $guard === 'customer' ? Customer::class : User::class;
        $user = $model::where('email', $request->email)->first();

        if (! $user) {
            $this->incrementAttempts($request);
            throw ValidationException::withMessages([
                'email' => [trans(Password::RESET_THROTTLED)],
            ]);
        }

        $token = Password::broker($guard === 'customer' ? 'customers' : 'users')->createToken($user);

        $user->notify(new CustomerResetPasswordNotification($token, $guard));

        $this->clearAttempts($request);

        return Inertia::render('Auth/ForgotPassword', [
            'status' => trans(Password::RESET_LINK_SENT),
        ]);
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
        return 'password_reset|'.mb_strtolower($request->email).'|'.$request->ip();
    }
}

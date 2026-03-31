<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    use ThrottlesAuth;

    protected int $maxAttempts = 5;

    protected int $decayMinutes = 1;

    public function __invoke(GeneralSettings $settings)
    {
        if (! $settings->registration) {
            return redirect()->back()->with('error', 'Registration is disabled.');
        }

        return Inertia::render('Auth/Login');
    }

    public function login(Request $request, GeneralSettings $settings)
    {
        if (! $settings->registration) {
            return redirect()->back()->with('error', 'Registration is disabled.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->sendLockoutResponse($request);
        }

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $this->clearLoginAttempts($request);
            $request->session()->regenerate();

            return redirect()->intended(route('frontend.home'));
        }

        $this->incrementLoginAttempts($request);

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}

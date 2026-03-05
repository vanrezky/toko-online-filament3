<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __invoke(GeneralSettings $settings)
    {
        if (!$settings->registration) {
            return redirect()->back()->with('error', 'Registration is disabled.');
        }

        return Inertia::render("Auth/Login");
    }

    public function login(Request $request, GeneralSettings $settings)
    {
        if (!$settings->registration) {
            return redirect()->back()->with('error', 'Registration is disabled.');
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::guard('customer')->attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('frontend.home'));
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }
}

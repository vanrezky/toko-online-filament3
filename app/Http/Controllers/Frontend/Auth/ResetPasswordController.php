<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        $guard = $request->input('guard', 'customer');
        $email = $request->input('email');

        return Inertia::render('Auth/ResetPassword', [
            'token' => $token,
            'email' => $email,
            'guard' => $guard,
        ]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => ['required', 'confirmed', securePassword(8)],
            'guard' => 'sometimes|string|in:customer,user',
        ]);

        $guard = $request->input('guard', 'customer');
        $broker = $guard === 'customer' ? 'customers' : 'users';

        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) use ($request, $guard) {
                $user->password = Hash::make($password);
                $user->save();

                if ($guard === 'customer') {
                    \Illuminate\Support\Facades\Auth::guard('customer')->login($user);
                } else {
                    \Illuminate\Support\Facades\Auth::guard('web')->login($user);
                }
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            $redirectRoute = $guard === 'customer' ? 'frontend.home' : 'home';
            return redirect()->route($redirectRoute)->with('status', trans($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
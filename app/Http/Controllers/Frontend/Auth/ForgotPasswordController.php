<?php

namespace App\Http\Controllers\Frontend\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\CustomerResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class ForgotPasswordController extends Controller
{
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

        $model = $guard === 'customer' ? Customer::class : User::class;
        $user = $model::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => [trans(Password::RESET_THROTTLED)],
            ]);
        }

        $token = Password::broker($guard === 'customer' ? 'customers' : 'users')->createToken($user);

        $user->notify(new CustomerResetPasswordNotification($token, $guard));

        return Inertia::render('Auth/ForgotPassword', [
            'status' => trans(Password::RESET_LINK_SENT),
        ]);
    }
}

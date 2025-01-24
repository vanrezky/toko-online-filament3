<?php

use Illuminate\Support\Facades\Log;

/**
 * Retrieve a value from the general settings with an optional default value.
 *
 * @param string $value The key of the setting to retrieve
 * @param mixed $default The default value to return if the setting does not exist
 * @return mixed value of the setting, or the default value if the setting does not exist
 */
function settings(string $key, $default = null)
{
    try {

        if ($key === 'favicon') {
            return app(App\Settings\GeneralSettings::class)->getFavicon();
        }
        if ($key === 'logo') {
            return app(App\Settings\GeneralSettings::class)->getLogo();
        }

        return app(App\Settings\GeneralSettings::class)->$key ?? $default;
    } catch (\Illuminate\Database\QueryException $e) {
        Log::error($e);
        return $default;
    }
}

/**
 * Generates a secure password based on settings.
 *
 * @param int $minLength the minimum length of the password (default is 8)
 * @return Illuminate\Validation\Rules\Password the generated secure password
 */
function secure_password(int $minLength = 8)
{
    return settings('secure_password') ? Illuminate\Validation\Rules\Password::min(8)->symbols()->numbers()->letters() : Illuminate\Validation\Rules\Password::min(8);
}


function isSuperUser(): bool
{
    return auth()->user()->is_super_user ?? false;
}


function getUrlImage($image): string
{
    if (filter_var($image, FILTER_VALIDATE_URL)) {
        // Jika $image adalah URL eksternal, kembalikan langsung
        return $image;
    }

    // Jika bukan URL eksternal, gunakan asset dari penyimpanan lokal
    return asset('/storage/' . $image);
}

function toMoney($price): string
{
    return \Akaunting\Money\Money::IDR($price);
}

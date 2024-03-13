<?php



/**
 * Retrieve a value from the general settings with an optional default value.
 *
 * @param string $value The key of the setting to retrieve
 * @param mixed $default The default value to return if the setting does not exist
 * @return mixed value of the setting, or the default value if the setting does not exist
 */
function settings(string $key, $default = null)
{
    return app(App\Settings\GeneralSettings::class)->$key ?? $default;
}

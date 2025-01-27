<?php

if (!function_exists('notification')) {
    function notification(string $message, $type = 'success'): \Filament\Notifications\Notification
    {
        return \Filament\Notifications\Notification::make()
            ->title($message)
            ->{$type}()
            ->send();
    }
}

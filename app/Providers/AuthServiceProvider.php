<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Policies\ActivityPolicy;
use App\Policies\ExceptionPolicy;
use BezhanSalleh\FilamentExceptions\Models\Exception;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Spatie\Activitylog\Models\Activity;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // Update `Activity::class` with the one defined in `config/activitylog.php`
        Activity::class => ActivityPolicy::class,
        Exception::class => ExceptionPolicy::class,
        // Exception::class => '',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
    }
}

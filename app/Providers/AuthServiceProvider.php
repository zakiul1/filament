<?php

namespace App\Providers;

use App\Models\ExportBundle;
use App\Models\User;
use App\Policies\ExportBundlePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ExportBundle::class => ExportBundlePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('access-admin', function (User $user) {
            return $user->hasRole('SUPER_ADMIN') || $user->hasRole('ADMIN');
        });
    }
}
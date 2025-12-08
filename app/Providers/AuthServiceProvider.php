<?php


namespace App\Providers;


use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;


class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];


    public function boot(): void
    {
        Gate::define('access-admin', function (User $user) {
            return $user->hasRole('SUPER_ADMIN') || $user->hasRole('ADMIN');
        });
    }
}
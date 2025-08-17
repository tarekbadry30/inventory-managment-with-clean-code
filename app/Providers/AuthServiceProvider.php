<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Enums\UserRoleEnum;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('create-transfer', fn($user) => in_array($user->role, [UserRoleEnum::ADMIN->value, UserRoleEnum::ACCOUNTANT->value]));
        Gate::define('update-stock', fn($user) => in_array($user->role, [UserRoleEnum::ADMIN->value, UserRoleEnum::ACCOUNTANT->value]));
    }
}

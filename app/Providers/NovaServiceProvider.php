<?php

namespace App\Providers;

use App\Models\User;
use App\Nova\Dashboards\Main;
use Illuminate\Support\Facades\Gate;
use Laravel\Nova\Nova;
use Laravel\Nova\NovaApplicationServiceProvider;

class NovaServiceProvider extends NovaApplicationServiceProvider
{
    protected function routes(): void
    {
        Nova::routes()
            ->withAuthenticationRoutes()
            ->withPasswordResetRoutes()
            ->withoutEmailVerificationRoutes()
            ->register();
    }

    protected function gate(): void
    {
        Gate::define('viewNova', function (User $user) {
            return in_array($user->email, [
                'kurusa03@gmail.com',
            ]);
        });
    }

    protected function dashboards(): array
    {
        return [
            new Main,
        ];
    }
}

<?php

declare(strict_types=1);

namespace App\Providers;

use Domain\Identity\Policies\BarbershopPolicy;
use Domain\Tenant\Models\Barbershop;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Barbershop::class, BarbershopPolicy::class);
    }
}

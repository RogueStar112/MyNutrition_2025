<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Filament\Notifications\Livewire\DatabaseNotifications;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        DatabaseNotifications::trigger('filament.notifications.database-notifications-trigger');
        DatabaseNotifications::pollingInterval('60s');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

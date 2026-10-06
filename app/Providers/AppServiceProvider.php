<?php

namespace App\Providers;

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
        // Schema and seeding commands run as the owner role; the web app keeps the restricted app role.
        if ($this->app->runningInConsole()) {
            $command = $_SERVER['argv'][1] ?? '';
            
            if (str_starts_with($command, 'migrate') || in_array($command, ['db:seed', 'db:wipe'], true)) {
                config(['database.default' => 'pgsql_owner']);
                }
            }
    }
}

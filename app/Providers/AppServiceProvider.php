<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        app()->singleton('site', fn () => new \App\Site);
    }

    public function boot(): void
    {
        URL::forceScheme('https');
    }
}

<?php

namespace Spatie\GoogleCalendar;

use Illuminate\Support\ServiceProvider;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/laravel-google-calendar.php' => config_path('laravel-google-calendar.php'),
        ], 'config');

    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/laravel-google-calendar.php', 'laravel-google-calendar');
    }
}

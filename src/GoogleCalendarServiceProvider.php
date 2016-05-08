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

        $this->app->bind(GoogleCalendar::class, function () {

            $calendarId = config('laravel-google-calendar.calendar_id');

            return GoogleCalendarFactory::createForCalendarId($calendarId);
        });

        $this->app->alias(GoogleCalendar::class, 'laravel-google-calendar');
    }
}

<?php

namespace Spatie\GoogleCalendar;

use Illuminate\Support\ServiceProvider;
use Spatie\GoogleCalendar\Exceptions\InvalidConfiguration;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-google-calendar.php' => config_path('laravel-google-calendar.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-google-calendar.php', 'laravel-google-calendar');

        $this->app->bind(GoogleCalendar::class, function () {
            $config = config('laravel-google-calendar');

            $this->guardAgainstInvalidConfiguration($config);

            return GoogleCalendarFactory::createForCalendarId($config['calendar_id']);
        });

        $this->app->alias(GoogleCalendar::class, 'laravel-google-calendar');
    }

    /**
     * @param array|null $config
     *
     * @throws \Spatie\GoogleCalendar\Exceptions\InvalidConfiguration
     */
    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($config['calendar_id'])) {
            throw InvalidConfiguration::calendarIdNotSpecified();
        }

        if (! file_exists($config['service_account_credentials_json'])) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($config['service_account_credentials_json']);
        }
    }
}

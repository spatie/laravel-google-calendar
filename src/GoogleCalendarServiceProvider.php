<?php

namespace Spatie\GoogleCalendar;

use Illuminate\Support\ServiceProvider;
use Spatie\GoogleCalendar\Exceptions\InvalidConfiguration;

class GoogleCalendarServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/google-calendar.php' => config_path('google-calendar.php'),
        ], 'config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/google-calendar.php', 'google-calendar');

        $this->app->bind(GoogleCalendar::class, function () {
            $config = config('google-calendar');

            $this->guardAgainstInvalidConfiguration($config);

            return GoogleCalendarFactory::createForCalendarId($config['calendar_id']);
        });

        $this->app->alias(GoogleCalendar::class, 'laravel-google-calendar');
    }

    protected function guardAgainstInvalidConfiguration(array $config = null)
    {
        if (empty($config['calendar_id'])) {
            throw InvalidConfiguration::calendarIdNotSpecified();
        }

        $authProfile = $config['default_auth_profile'];

        switch ($authProfile) {
            case 'service_account':
                $this->validateServiceAccountConfigSettings($config);
                break;
            case 'oauth':
                $this->validateOAuthConfigSettings($config);
                break;
            default:
                throw new \InvalidArgumentException("Unsupported authentication profile [{$authProfile}].");
        }
    }

    protected function validateServiceAccountConfigSettings(array $config = null)
    {
        $credentials = $config['auth_profiles']['service_account']['credentials_json'];

        $this->validateConfigSetting($credentials);
    }

    protected function validateOAuthConfigSettings(array $config = null)
    {
        $credentials = $config['auth_profiles']['oauth']['credentials_json'];

        $this->validateConfigSetting($credentials);

        $token = $config['auth_profiles']['oauth']['token_json'];

        $this->validateConfigSetting($token);
    }

    protected function validateConfigSetting(string $setting)
    {
        if (! is_array($setting) && ! is_string($setting)) {
            throw InvalidConfiguration::credentialsTypeWrong($setting);
        }

        if (is_string($setting) && ! file_exists($setting)) {
            throw InvalidConfiguration::credentialsJsonDoesNotExist($setting);
        }
    }
}

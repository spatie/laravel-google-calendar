<?php

namespace Spatie\GoogleCalendar;

use Google_Client;
use Google_Service_Calendar;
use Spatie\GoogleCalendar\Exceptions\InvalidConfiguration;

class GoogleCalendarFactory
{
    public static function createForCalendarId(string $calendarId,$apiSecretToken,$userToken): GoogleCalendar
    {
        $config = config('google-calendar');

        $client = self::createAuthenticatedGoogleClient($config,$apiSecretToken,$userToken);

        $service = new Google_Service_Calendar($client);

        return self::createCalendarClient($service, $calendarId,$apiSecretToken,$userToken);
    }

    public static function createAuthenticatedGoogleClient(array $config,string $apiSecretToken,string $userToken): Google_Client
    {
        $authProfile = $config['default_auth_profile'];

        if ($authProfile === 'service_account') {
            return self::createServiceAccountClient($config['auth_profiles']['service_account']);
        }
        if ($authProfile === 'oauth') {
            return self::createOAuthClient($apiSecretToken,$userToken);
        }

        throw InvalidConfiguration::invalidAuthenticationProfile($authProfile);
    }

    protected static function createServiceAccountClient(array $authProfile): Google_Client
    {
        $client = new Google_Client;

        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);

        $client->setAuthConfig($authProfile['credentials_json']);

        if (config('google-calendar')['user_to_impersonate']) {
            $client->setSubject(config('google-calendar')['user_to_impersonate']);
        }

        return $client;
    }

    protected static function createOAuthClient(string $apiSecretToken, string $userToken): Google_Client
    {
        $client = new Google_Client;

        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);

        $client->setAuthConfig($apiSecretToken);

        $client->setAccessToken(file_get_contents($userToken));

        return $client;
    }

    protected static function createCalendarClient(Google_Service_Calendar $service, string $calendarId,string $apiSecretToken,string $userToken): GoogleCalendar
    {
        return new GoogleCalendar($service, $calendarId,$apiSecretToken,$userToken);
    }
}

<?php

namespace Spatie\GoogleCalendar;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarFactory
{
    /**
     * @param string $calendarId
     *
     * @return \Spatie\GoogleCalendar\GoogleCalendar
     */
    public static function createForCalendarId(string $calendarId) : GoogleCalendar
    {
        $config = config('laravel-google-calendar');

        $client = self::createAuthenticatedGoogleClient($config);

        $service = new Google_Service_Calendar($client);

        return self::createCalendarClient($service, $calendarId);
    }

    /**
     * @param array $config
     *
     * @return \Google_Client
     */
    public static function createAuthenticatedGoogleClient(array $config) : Google_Client
    {
        $client = new Google_Client;

        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);

        $client->setAuthConfig($config['service_account_credentials_json']);

        return $client;
    }

    /**
     * @param \Google_Service_Calendar $service
     * @param string $calendarId
     *
     * @return \Spatie\GoogleCalendar\GoogleCalendar
     */
    protected static function createCalendarClient(Google_Service_Calendar $service, string $calendarId) : GoogleCalendar
    {
        return new GoogleCalendar($service, $calendarId);
    }
}

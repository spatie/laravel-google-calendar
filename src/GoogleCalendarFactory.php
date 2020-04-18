<?php

namespace Spatie\GoogleCalendar;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarFactory
{
    public static function createForCalendarId(string $calendarId, string $pathToCredentials): GoogleCalendar
    {
        $config = config('google-calendar');

        $client = self::createAuthenticatedGoogleClient($pathToCredentials);

        $service = new Google_Service_Calendar($client);

        return self::createCalendarClient($service, $calendarId);
    }

    public static function createAuthenticatedGoogleClient($pathToCredentials): Google_Client
    {
        $client = new Google_Client;

        $client->setScopes([
            Google_Service_Calendar::CALENDAR,
        ]);

        $client->setAuthConfig($pathToCredentials);

        return $client;
    }

    protected static function createCalendarClient(Google_Service_Calendar $service, string $calendarId): GoogleCalendar
    {
        return new GoogleCalendar($service, $calendarId);
    }
}

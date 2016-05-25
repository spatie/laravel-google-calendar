<?php

namespace Spatie\GoogleCalendar;

use Google_Client;
use Google_Service_Calendar;

class GoogleCalendarFactory
{
    public static function createForCalendarId($calendarId): GoogleCalendar
    {
        $config = config('laravel-google-calendar');

        $client = new Google_Client();

        $credentials = $client->loadServiceAccountJson(
            $config['client_secret_json'],
            'https://www.googleapis.com/auth/calendar'
        );

        $client->setAssertionCredentials($credentials);

        $service = new Google_Service_Calendar($client);

        return new GoogleCalendar($service, $calendarId);
    }
}

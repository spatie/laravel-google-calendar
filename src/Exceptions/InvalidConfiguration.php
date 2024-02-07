<?php

namespace Spatie\GoogleCalendar\Exceptions;

use Exception;

class InvalidConfiguration extends Exception
{
    public static function calendarIdNotSpecified()
    {
        return new static('There was no calendar id specified. You must provide a valid calendar id to fetch events for.');
    }

    public static function credentialsJsonDoesNotExist(string $path)
    {
        return new static("Could not find a credentials file at `{$path}`.");
    }

    public static function credentialsTypeWrong($credentials)
    {
        return new static(sprintf('Credentials should be an array or the path of json file. "%s was given.', gettype($credentials)));
    }

    public static function invalidAuthenticationProfile($authProfile)
    {
        return new static("Authentication profile [{$authProfile}] does not match any of the supported authentication types.");
    }
}

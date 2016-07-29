# Manage events on a Google Calendar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-google-calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-google-calendar)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-google-calendar/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-google-calendar)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/a966412b-091b-4407-b509-6f7472935b0e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/a966412b-091b-4407-b509-6f7472935b0e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-google-calendar.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-google-calendar)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-google-calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-google-calendar)

This package makes working with a Google Calendar a breeze. Once it has been set up you can do these things:

```php
use Spatie\GoogleCalendar\Event;

//create a new event
$event = new Event;

$event->name = 'A new event';
$event->startDateTime = Carbon\Carbon::now();
$event->endDateTime = Carbon\Carbon::now()->addHour();

$event->save();

// get all future events on a calendar
$events = Event::get(); 

$firstEvent = $events->first();
$firstEvent->name = 'updated name';
$firstEvent->save();

// create a new event
Event::create([
   'name' => 'A new event'
   'startDateTime' => Carbon\Carbon::now(),
   'endDateTime' => Carbon\Carbon::now()->addHour(),
]);

// delete an event
$event->delete();
```

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Postcardware

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment you are required to send us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

The best postcards will get published on the open source page on our website.

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-google-calendar
```

Next up the service provider must be registered:

```php
'providers' => [
    ...
    Spatie\GoogleCalendar\GoogleCalendarServiceProvider::class,
];
```

Optionally the  `Spatie\GoogleCalendar\GoogleCalendarFacade` must be registered:

```php
'aliases' => [
	...
    'GoogleCalendar' => Spatie\GoogleCalendar\GoogleCalendarFacade::class,
    ...
]
```

You must publish the configuration with this command:

```bash
php artisan vendor:publish --provider="Spatie\GoogleCalendar\GoogleCalendarServiceProvider"
```

This will publish file called `laravel-google-calendar.php` in your config-directory with this contents:
```
<?php

return [

    /**
     * Path to a json file containing the credentials of a Google Service account.
     */
    'client_secret_json' => storage_path('app/laravel-google-calendar/client_secret.json'),

    /**
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => '',
    
];
```

Read [this blogpost](https://murze.be/2016/05/how-to-setup-and-use-the-google-calendar-api/) to learn how to get the correct values for `client_secret_json` and `calendar_id`.

## Usage

### Getting events

You can fetch all events by simply calling `Event::get();` this will return all events of the coming year. An event comes in the form of a `Spatie\GoogleCalendar\Event` object.

The fill signature of the function is:

```php
/**
 * @param \Carbon\Carbon|null $startDateTime
 * @param \Carbon\Carbon|null $endDateTime
 * @param array $queryParameters
 * @param string|null $calendarId
 *
 * @return \Illuminate\Support\Collection
 */
public static function get(Carbon $startDateTime = null, Carbon $endDateTime = null, array $queryParameters = [], string $calendarId = null) : Collection
```

The parameters you can pass in `$querParameters` are listed [on the documentation on `list` at the Google Calendar API docs](https://developers.google.com/google-apps/calendar/v3/reference/events/list#request).

### Creating an event

You can just new up a `Spatie\GoogleCalendar\Event`-object

```php
$event = new Event;

$event->name = 'A new event';
$event->startDateTime = Carbon\Carbon::now();
$event->endDateTime = Carbon\Carbon::now()->addHour();

$event->save();
```

You can also call `create` statically:

```php
Event::create([
   'name' => 'A new event',
   'startDateTime' => Carbon\Carbon::now(),
   'endDateTime' => Carbon\Carbon::now()->addHour(),
]);
```

This will create an event with a specific start and end time. If you want to create a full day event you must use `startDate` and `endDate` instead of `startDateTime` and `endDateTime`.

```php
$event = new Event;

$event->name = 'A new full day event';
$event->startDate = Carbon\Carbon::now();
$event->endDate = Carbon\Carbon::now()->addDay();

$event->save();
```

### Getting a single event

Google assigns a unique id to every single event. You can get this id by getting events using the `get` method and getting the `id` property on a `Spatie\GoogleCalendar\Event`-object:
```php
// get the id of the first upcoming event in the calendar.
$calendarId = Event::get()->first()->id;
```

You can use this id to fetch a single event from Google:
```php
Event::find($calendarId)
```

### Updating an event

Easy, just change some properties and call `save()`:

```php
$event = Event::find($eventId)

$event->name = 'My updated title' 
$event->save();
```

### Deleting an event

Nothing to it!

```php
$event = Event::find($eventId)

$event->delete()
```

### Limitations

The Google Calendar API provides many options. This package doesn't support all of them. For instance recurring events cannot be managed properly with this package. If you stick to creating events with a name and a date you should be fine.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

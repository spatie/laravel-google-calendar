# Manage events on a Google Calendar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-google-calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-google-calendar)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-google-calendar/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-google-calendar)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/a966412b-091b-4407-b509-6f7472935b0e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/a966412b-091b-4407-b509-6f7472935b0e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-google-calendar.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-google-calendar)
[![StyleCI](https://styleci.io/repos/58305903/shield?branch=master)](https://styleci.io/repos/58305903)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-google-calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-google-calendar)

This package makes working with a Google Calendar a breeze. Once it has been set up you can do these things:

```php
use Spatie\GoogleCalendar\Event;

//create a new event
$event = new Event;

$event->name = 'A new event';
$event->startDateTime = Carbon\Carbon::now();
$event->endDateTime = Carbon\Carbon::now()->addHour();
$event->addAttendee(['email' => 'youremail@gmail.com']);
$event->addAttendee(['email' => 'anotherEmail@gmail.com']);

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

You're free to use this package (it's [MIT-licensed](LICENSE.md)), but if it makes it to your production environment when highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using.

Our address is: Spatie, Samberstraat 69D, 2060 Antwerp, Belgium.

All postcards are published [on our website](https://spatie.be/en/opensource/postcards).

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

This will publish file called `google-calendar.php` in your config-directory with this contents:
```
return [
    /*
     * Path to the json file containing the credentials.
     */
    'service_account_credentials_json' => storage_path('app/google-calendar/service-account-credentials.json'),

    /*
     *  The id of the Google Calendar that will be used by default.
     */
    'calendar_id' => env('GOOGLE_CALENDAR_ID'),
];

```

## How to obtain the credentials to communicate with Google Calendar

The first thing you’ll need to do is to get some credentials to use Google API’s. I’m assuming that you’ve already created a Google account and are signed in. Head over to [Google API console](https://console.developers.google.com/apis) and click "Select a project" in the header.

![1](https://spatie.github.io/laravel-google-calendar/v2/1.jpg)

Next up we must specify which API’s the project may consume. In the list of available API’s click "Google Analytics API". On the next screen click "Enable".

![2](https://spatie.github.io/laravel-google-calendar/v2/2.jpg)

Now that you’ve created a project that has access to the Analytics API it’s time to download a file with these credentials. Click "Credentials" in the sidebar. You’ll want to create a "Service account key".

![3](https://spatie.github.io/laravel-google-calendar/v2/3.jpg)

On the next screen you can give the service account a name. You can name it anything you’d like. In the service account id you’ll see an email address. We’ll use this email address later on in this guide. Select "JSON" as the key type and click "Create" to download the JSON file.

![4](https://spatie.github.io/laravel-google-calendar/v2/4.jpg)

Save the json inside your Laravel project at the location specified in the `service_account_credentials_json` key of the config file of this package. Because the json file contains potentially sensitive information I don't recommend committing it to your git repository.

Now that everything is set up on the API site, we’ll need to configure some things on the Google Calendar site. Head over Google Calendar and view the settings of the calendar you want to work with via PHP.  On the “Share this Calendar” tab add the service account id that was displayed when creating credentials on the API site. 

![5](https://spatie.github.io/laravel-google-calendar/v2/5.jpg)

Open up the “Calendar Details” tab to see the id of the calendar. You need to specify that id in the config file.

![6](https://spatie.github.io/laravel-google-calendar/v2/6.jpg)

## Usage

### Getting events

You can fetch all events by simply calling `Event::get();` this will return all events of the coming year. An event comes in the form of a `Spatie\GoogleCalendar\Event` object.

The full signature of the function is:

```php
public static function get(Carbon $startDateTime = null, Carbon $endDateTime = null, array $queryParameters = [], string $calendarId = null): Collection
```

The parameters you can pass in `$queryParameters` are listed [on the documentation on `list` at the Google Calendar API docs](https://developers.google.com/google-apps/calendar/v3/reference/events/list#request).

### Accessing start and end dates of an event

You can use these getters to retrieve start and end date as [Carbon](https://github.com/briannesbitt/Carbon) instances:

```php
$events = Event::get();

$event[0]->startDate;
$event[0]->startDateTime;
$event[0]->endDate;
$event[0]->endDateTime;
```

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
Event::find($calendarId);
```

### Updating an event

Easy, just change some properties and call `save()`:

```php
$event = Event::find($eventId);

$event->name = 'My updated title';
$event->save();
```

### Deleting an event

Nothing to it!

```php
$event = Event::find($eventId);

$event->delete();
```

### Limitations

The Google Calendar API provides many options. This package doesn't support all of them. For instance recurring events cannot be managed properly with this package. If you stick to creating events with a name and a date you should be fine.

## Upgrading from v1 to v2

The only major difference between `v1` and `v2` is that under the hood Google API v2 is used instead of v1. Here are the steps required to upgrade:
 - rename the config file from `laravel-google-calendar` to `google-calendar`
 - in the config file rename the `client_secret_json` key to `service_account_credentials_json`

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email freek@spatie.be instead of using the issue tracker.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

A big thank you to [Sebastiaan Luca](https://github.com/sebastiaanluca) for his big help creating v2 of this package.

## About Spatie
Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

# Manage a Google Calendar

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-google-calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-google-calendar)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/spatie/laravel-google-calendar/master.svg?style=flat-square)](https://travis-ci.org/spatie/laravel-google-calendar)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/a966412b-091b-4407-b509-6f7472935b0e.svg?style=flat-square)](https://insight.sensiolabs.com/projects/a966412b-091b-4407-b509-6f7472935b0e)
[![Quality Score](https://img.shields.io/scrutinizer/g/spatie/laravel-google-calendar.svg?style=flat-square)](https://scrutinizer-ci.com/g/spatie/laravel-google-calendar)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-google-calendar.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-google-calendar)

This package makes working with a Google Calendar a breeze. Once it has been setup you can do these things:

```php
use Spatie\GoogleCalendar\Event;

//create a new event
$event = new Event;

$event->name = 'my new event';
$event->startDateTime = Carbon\Carbon::now();
$event->endDateTime = Carbon\Carbon::now()->addHour();

$event->save();

// get all future events on a calendar
$events = Event::get(); 

$firstEvent = $event->first();
$firstEvent->name = 'updated name';
$firstEvent->save();

// create a new event
Event::create([
   'name' => 'my new event'
   'startDateTime' => Carbon\Carbon::now(),
   'endDateTime' => Carbon\Carbon::now()->addHour(),
]);

// delete and event
$event->delete();
```

Spatie is a webdesign agency based in Antwerp, Belgium. You'll find an overview of all our open source projects [on our website](https://spatie.be/opensource).

## Installation

You can install the package via composer:

``` bash
composer require spatie/laravel-google-calendar
```

## Usage

``` php
$skeleton = new Spatie\Skeleton();
echo $skeleton->echoPhrase('Hello, Spatie!');
```

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

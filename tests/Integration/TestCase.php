<?php

namespace Spatie\GoogleCalendar\Test\Integration;

use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\GoogleCalendar\GoogleCalendarServiceProvider;

abstract class TestCase extends Orchestra
{
    /** @var string */
    protected $calenderId;

    public function setUp()
    {
        $this->calendarId = 'abc123';

        parent::setUp();
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            GoogleCalendarServiceProvider::class,
        ];
    }

    /**
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('laravel-google-calendar.calendar_id', 'sqlite');
    }
}
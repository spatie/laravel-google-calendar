<?php

namespace Spatie\GoogleCalendar\Tests;

use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Spatie\GoogleCalendar\GoogleCalendarServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var string
     */
    protected $calendarId;

    /**
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app) : array
    {
        return [
            GoogleCalendarServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('laravel-google-calendar.calendar_id', $this->calendarId = 'personal');
    }
}

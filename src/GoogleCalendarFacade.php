<?php

namespace Spatie\GoogleCalendar;

use Illuminate\Support\Facades\Facade;

class GoogleCalendarFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-google-calendar';
    }
}

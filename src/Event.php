<?php

use Spatie\GoogleCalendar;

class Event
{
    /** @var string  */
    public $name = '';

    /** @var \Carbon\Carbon */
    public $startDateTime;

    /** @var \Carbon\Carbon */
    public $endDateTime;

    /** @var bool  */
    protected $allDayEvent = false;

    public static function createFromGoogleCalendarEvent(Google_Service_Calendar_Event $googleEvent)  {

        $event = new static;

        $event->name = $googleEvent->summary;
        
        $event->startDateTime = $googleEvent['start']['date'] ?? $googleEvent['start']['dateTime'];

        $event->endDateTime = $googleEvent['end']['date'] ?? $googleEvent['end']['dateTime'];

        $event->allDayEvent = is_null($googleEvent['start']['dateTime']);

    }
}
<?php

namespace Spatie\GoogleCalendar;

use Carbon\Carbon;
use DateTime;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;

class Event
{
    /** @var string */
    public $id;

    /** @var string */
    public $name;

    /** @var Carbon */
    public $startDateTime;

    /** @var Carbon */
    public $endDateTime;

    /** @var bool */
    public $allDayEvent;

    /** @var Google_Service_Calendar_Event*/
    public $googleEvent;
    
    /** @var int */
    protected $calendarId;

    public static function createFromGoogleCalendarEvent(Google_Service_Calendar_Event $googleEvent, $calendarId)  {

        $event = new static;

        $event->name = $googleEvent->summary;

        if (! is_null($googleEvent['start']['date'])) {
            $event->startDateTime = Carbon::createFromFormat('Y-m-d', $googleEvent['start']['date']);
        }

        if (! is_null($googleEvent['start']['dateTime'])) {
            $event->startDateTime = Carbon::createFromFormat(DateTime::RFC3339, $googleEvent['start']['dateTime']);
        }

        if (! is_null($googleEvent['end']['date'])) {
            $event->endDateTime = Carbon::createFromFormat('Y-m-d', $googleEvent['end']['date']);
        }

        if (! is_null($googleEvent['end']['dateTime'])) {
            $event->endDateTime = Carbon::createFromFormat(DateTime::RFC3339, $googleEvent['end']['dateTime']);
        }

        $event->allDayEvent = is_null($googleEvent['start']['dateTime']);

        $event->id = $googleEvent->id;

        $event->googleEvent = $googleEvent;
        
        $event->calendarId = $calendarId;

        return $event;
    }

    public function convertToGoogleEvent() : Google_Service_Calendar_Event
    {
        $googleEvent = new Google_Service_Calendar_Event();

        $googleEvent->summary = $this->name;
        
        $start = new Google_Service_Calendar_EventDateTime();
        $end = new Google_Service_Calendar_EventDateTime();
        
        if ($this->allDayEvent) {
            $start->setDateTime($this->startDateTime->format(DateTime::RFC3339));
            $end->setDateTime($this->startDateTime->format(DateTime::RFC3339));
        }
        else {
            $start->setDate($this->startDateTime->format('Y-m-d'));
            $end->setDate($this->endDateTime->format('Y-m-d'));
        }
        
        $googleEvent->setStart($start);
        $googleEvent->setEnd($end);

        return $googleEvent;
    }

    public static function get(Carbon $startDateTime = null, Carbon $endDateTime = null, $queryParameters = [], $calendarId = null)
    {
        $googleCalendar = self::getGoogleCalendar($calendarId);

        return $googleCalendar->listEvents($startDateTime, $endDateTime, $queryParameters);
    }
    
    public static function find($id, $calendarId = null)
    {
        $googleCalendar = self::getGoogleCalendar($calendarId);
        
        return $googleCalendar->getEvent($id);
    }

    public function save()
    {

    }

    public function delete($id = null)
    {
        return $this->getGoogleCalendar($this->calendarId)->deleteEvent($id ?? $this->id);
    }

    protected static function getGoogleCalendar($calendarId = null) : GoogleCalendar
    {
        $calendarId = $calendarId ?? config('laravel-google-calendar.calendar_id');

        return GoogleCalendarFactory::createForCalendarId($calendarId);
    }
}
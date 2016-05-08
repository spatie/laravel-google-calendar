<?php

namespace Spatie\GoogleCalendar;

use Carbon\Carbon;
use DateTime;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Collection;

class GoogleCalendar
{
    /** @var \Spatie\GoogleCalendar\Google_Service_Calendar */
    protected $calendarService;

    /** @var string */
    protected $calendarId;

    public function __construct(Google_Service_Calendar $calendarService, $calendarId)
    {
        $this->calendarService = $calendarService;

        $this->calendarId = $calendarId;
    }

    public function getCalendarId() : string
    {
        return $this->calendarId;
    }

    /**
     * List events.
     *
     * @param Carbon $startDateTime
     * @param Carbon $endDateTime
     * @param array $queryParameters
     *
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/list
     *
     * @return Collection
     */
    public function listEvents(Carbon $startDateTime = null, Carbon $endDateTime = null, $queryParameters = []) : Collection
    {
        $parameters = ['singleEvents' => true];

        if (is_null($startDateTime)) {
            $startDateTime = Carbon::now()->startOfDay();
        }
        $parameters['timeMin'] = $startDateTime->format(DateTime::RFC3339);

        if (is_null($endDateTime)) {
            $endDateTime = Carbon::now()->addYear()->endOfDay();
        }
        $parameters['timeMax'] = $endDateTime->format(DateTime::RFC3339);

        $parameters = array_merge($parameters, $queryParameters);

        $googleEvents = $this
            ->calendarService
            ->events
            ->listEvents($this->calendarId, $parameters)
            ->getItems();

        $events = collect($googleEvents)
            ->map(function (Google_Service_Calendar_Event $event) {
                return Event::createFromGoogleCalendarEvent($event, $this->calendarId);
            })
        ->sortBy(function (Event $event) {
           return $event->startDateTime->format(DATE_ISO8601);
        });

        /*
        $debug = $events->map(function (Event $event) {
            return $event->startDateTime->format('Y-m-d H:i:s') . ' - ' . $event->startDateTime->format('Y-m-d H:i:s') . $event->name . PHP_EOL;
        });
        */

        return $events;
    }

    /**
     * @param string $eventId
     * @return static
     */
    public function getEvent($eventId)
    {
        $googleEvent =  $this->calendarService->events->get($this->calendarId, $eventId);

        return Event::createFromGoogleCalendarEvent($googleEvent, $this->calendarId);
    }

    /**
     * Insert an event
     *
     * @param \Spatie\GoogleCalendar\Event|Google_Service_Calendar_Event $event
     *
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/insert
     *
     * @return mixed
     */
    public function insertEvent($event)
    {
        if ($event instanceof Event) {
            $event = $event->convertToGoogleEvent();
        }

        return $this->calendarService->events->insert($this->calendarId, $event);
    }

    /**
     * @param string|\Spatie\GoogleCalendar\Event $eventId
     */
    public function deleteEvent($eventId)
    {
        if ($eventId instanceof Event) {
            $eventId = $eventId->id;
        }

        return $this->calendarService->events->delete($this->calendarId, $eventId);
    }

    public function getService() : Google_Service_Calendar
    {
        return $this->calendarService;
    }
}

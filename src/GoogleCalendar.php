<?php

namespace Spatie\GoogleCalendar;

use Carbon\Carbon;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

class GoogleCalendar
{
    /** @var \Spatie\GoogleCalendar\Google_Service_Calendar */
    protected $calendarService;

    /** @var string */
    protected $calendarId;

    /** @var \Closure  */
    protected $eventTransformer;

    public function __construct(Google_Service_Calendar $calendarService, $calendarId)
    {
        $this->calendarService = $calendarService;

        $this->calendarId = $calendarId;

        $this->eventTransformer = function (Google_Service_Calendar_Event $event) {
            return [
                $event['start']['dateTime'],
                $event['end']['dateTime'],
                $event->summary,
            ];
        };
    }

    /**
     * @return string
     */
    public function getCalendarId()
    {
        return $this->calendarId;
    }

    public function setEventTransformer(callable $eventTransformer)
    {
        $this->eventTransformer = $eventTransformer;
    }

    /**
     * List all events.
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param array  $queryParameters
     *
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/list#parameters
     *
     * @return \Illuminate\Support\Collection
     */
    public function listEvents(Carbon $startTime = null, Carbon $endTime = null, $queryParameters = [])
    {
        $parameters = [];

        if ($startTime) {
            $parameters['timeMin'] = $startTime->toAtomString();
        }

        if ($endTime) {
            $parameters['timeMax'] = $startTime->toAtomString();
        }

        $parameters = array_merge($parameters, $queryParameters);

        $events = $this
            ->calendarService
            ->events
            ->listEvents($this->calendarId, $parameters)
            ->getItems();

        return collect($events)->map($this->eventTransformer);
    }

    /**
     * @return \Google_Service_Calendar|\Spatie\GoogleCalendar\Google_Service_Calendar
     */
    public function getService()
    {
        return $this->calendarService;
    }
}

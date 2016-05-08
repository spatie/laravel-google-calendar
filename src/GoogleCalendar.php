<?php

namespace Spatie\GoogleCalendar;

use Carbon\Carbon;
use Event;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Collection;

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
            return Event::createFromGoogleCalendarEvent($event);
        };
    }
    
    public function getCalendarId() : string
    {
        return $this->calendarId;
    }

    /**
     * @param callable $eventTransformer
     * @return $this
     */
    public function setEventTransformer(callable $eventTransformer)
    {
        $this->eventTransformer = $eventTransformer;
        
        return $this;
    }

    /**
     * List events.
     *
     * @param Carbon $startTime
     * @param Carbon $endTime
     * @param array  $queryParameters
     *
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/list#parameters
     *
     * @return Collection
     */
    public function listEvents(Carbon $startTime = null, Carbon $endTime = null, $queryParameters = []) : Collection
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
    
    public function getService() : Google_Service_Calendar
    {
        return $this->calendarService;
    }
}

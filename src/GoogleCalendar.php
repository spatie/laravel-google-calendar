<?php

namespace Spatie\GoogleCalendar;

use DateTime;
use Carbon\Carbon;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Psr\Http\Message\RequestInterface;

class GoogleCalendar
{
    /** @var \Google_Service_Calendar */
    protected $calendarService;

    /** @var string */
    protected $calendarId;

    /** @var iterable */
    protected $batchRequests;

    public function __construct(Google_Service_Calendar $calendarService, string $calendarId)
    {
        $this->calendarService = $calendarService;

        $this->calendarId = $calendarId;

        $this->batchRequests = $this->calendarService->createBatch();
    }

    public function getCalendarId(): string
    {
        return $this->calendarId;
    }

    /*
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/list
     */
    public function listEvents(Carbon $startDateTime = null, Carbon $endDateTime = null, array $queryParameters = []): array
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

        return $this
            ->calendarService
            ->events
            ->listEvents($this->calendarId, $parameters)
            ->getItems();
    }


    /**
     * @param string $eventId
     * @param array  $optParams
     *
     * @return Google_Service_Calendar_Event|RequestInterface
     */
    public function getEvent(string $eventId, $optParams = [])
    {
        return $this->calendarService->events->get($this->calendarId, $eventId, $optParams);
    }

    public function getEvents(iterable $eventIds, $optParams = [])
    {
        $this->enableBatch(true);

        collect($eventIds)
            ->each(
                function ($eventId, $batchIdentifier) use ($optParams) {
                    $this->batchRequests->add($this->getEvent($eventId, $optParams), $batchIdentifier);
                });

        return $this;
    }

    /**
     * @param       $event
     * @param array $optParams
     *
     * @link https://developers.google.com/google-apps/calendar/v3/reference/events/insert
     * @return Google_Service_Calendar_Event|RequestInterface
     */
    public function insertEvent($event, $optParams = [])
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendarService->events->insert($this->calendarId, $event, $optParams);
    }

    public function insertEvents(iterable $events, $optParams = [])
    {
        $this->enableBatch(true);

        collect($events)
            ->each(
                function ($event, $batchIdentifier) use ($optParams) {
                    $this->batchRequests->add($this->insertEvent($event, $optParams), $batchIdentifier);
                });

        return $this;
    }

    /**
     * @param       $event
     * @param array $optParams
     *
     * @return Google_Service_Calendar_Event|RequestInterface
     */
    public function updateEvent($event, $optParams = [])
    {
        if ($event instanceof Event) {
            $event = $event->googleEvent;
        }

        return $this->calendarService->events->update($this->calendarId, $event->id, $event, $optParams);
    }

    public function updateEvents(iterable $events, $optParams = [])
    {
        $this->enableBatch(true);

        collect($events)
            ->each(
                function ($event, $batchIdentifier) use ($optParams) {
                    $this->batchRequests->add($this->updateEvent($event, $optParams), $batchIdentifier);
                });

        return $this;
    }

    /**
     * @param       $eventId
     * @param array $optParams
     *
     * @return RequestInterface
     */
    public function deleteEvent($eventId, $optParams = [])
    {
        if ($eventId instanceof Event) {
            $eventId = $eventId->id;
        }

        return $this->calendarService->events->delete($this->calendarId, $eventId, $optParams);
    }

    /**
     * @param iterable $eventIds
     * @param array    $optParams
     *
     * @return $this
     */
    public function deleteEvents(iterable $eventIds, $optParams = [])
    {
        $this->enableBatch(true);

        collect($eventIds)
            ->each(
                function ($eventId, $batchIdentifier) use ($optParams) {
                    $this->batchRequests->add($this->deleteEvent($eventId, $optParams), $batchIdentifier);
                });

        return $this;
    }

    public function getService(): Google_Service_Calendar
    {
        return $this->calendarService;
    }

    protected function enableBatch(bool $bool)
    {
        $this->calendarService->getClient()->setUseBatch($bool);

        return $this;
    }

    public function sendBatch():array
    {
        return $this->batchRequests->execute();
    }
}

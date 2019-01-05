<?php

namespace Spatie\GoogleCalendar;

use DateTime;
use Carbon\Carbon;
use Google_Service_Calendar_Event;
use Illuminate\Support\Collection;
use Google_Service_Calendar_EventDateTime;

class Event
{
    /**
     * @var \Google_Service_Calendar_Event 
     */
    public $googleEvent;

    /**
     * @var string 
     */
    protected $calendarId;

    /**
     * @var array 
     */
    protected $attendees;

    public function __construct()
    {
        $this->attendees = [];
        $this->googleEvent = new Google_Service_Calendar_Event;
    }

    /**
     * @param \Google_Service_Calendar_Event $googleEvent
     * @param $calendarId
     *
     * @return static
     */
    public static function createFromGoogleCalendarEvent(Google_Service_Calendar_Event $googleEvent, $calendarId)
    {
        $event = new static;

        $event->googleEvent = $googleEvent;
        $event->calendarId = $calendarId;

        return $event;
    }

    /**
     * @param array       $properties
     * @param string|null $calendarId
     *
     * @return mixed
     */
    public static function create(array $properties, string $calendarId = null, $optParams = [])
    {
        $event = new static;

        $event->calendarId = static::getGoogleCalendar($calendarId)->getCalendarId();

        foreach ($properties as $name => $value) {
            $event->$name = $value;
        }

        return $event->save('insertEvent', $optParams);
    }

    public static function get(Carbon $startDateTime = null, Carbon $endDateTime = null, array $queryParameters = [], string $calendarId = null) : Collection
    {
        $googleCalendar = static::getGoogleCalendar($calendarId);

        $googleEvents = $googleCalendar->listEvents($startDateTime, $endDateTime, $queryParameters);

        $useUserOrder = isset($queryParameters['orderBy']);

        return collect($googleEvents)
            ->map(
                function (Google_Service_Calendar_Event $event) use ($calendarId) {
                    return static::createFromGoogleCalendarEvent($event, $calendarId);
                }
            )
            ->sortBy(
                function (Event $event, $index) use ($useUserOrder) {
                    if ($useUserOrder) {
                        return $index;
                    }

                    return $event->sortDate;
                }
            )
            ->values();
    }

    public static function find($eventId, string $calendarId = null): self
    {
        $googleCalendar = static::getGoogleCalendar($calendarId);

        $googleEvent = $googleCalendar->getEvent($eventId);

        return static::createFromGoogleCalendarEvent($googleEvent, $calendarId);
    }

    public function __get($name)
    {
        $name = $this->getFieldName($name);

        if ($name === 'sortDate') {
            return $this->getSortDate();
        }

        $value = array_get($this->googleEvent, $name);

        if (in_array($name, ['start.date', 'end.date']) && $value) {
            $value = Carbon::createFromFormat('Y-m-d', $value)->startOfDay();
        }

        if (in_array($name, ['start.dateTime', 'end.dateTime']) && $value) {
            $value = Carbon::createFromFormat(DateTime::RFC3339, $value);
        }

        return $value;
    }

    public function __set($name, $value)
    {
        $name = $this->getFieldName($name);

        if (in_array($name, ['start.date', 'end.date', 'start.dateTime', 'end.dateTime'])) {
            $this->setDateProperty($name, $value);

            return;
        }

        array_set($this->googleEvent, $name, $value);
    }

    public function exists(): bool
    {
        return $this->id != '';
    }

    public function isAllDayEvent(): bool
    {
        return is_null($this->googleEvent['start']['dateTime']);
    }

    public function save(string $method = null, $optParams = []): self
    {
        $method = $method ?? ($this->exists() ? 'updateEvent' : 'insertEvent');

        $googleCalendar = $this->getGoogleCalendar($this->calendarId);

        $this->googleEvent->setAttendees($this->attendees);

        $googleEvent = $googleCalendar->$method($this, $optParams);

        return static::createFromGoogleCalendarEvent($googleEvent, $googleCalendar->getCalendarId());
    }

    public function update(array $attributes, $optParams = []): self
    {
        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }

        return $this->save('updateEvent', $optParams);
    }

    public function delete(string $eventId = null)
    {
        $this->getGoogleCalendar($this->calendarId)->deleteEvent($eventId ?? $this->id);
    }

    public function addAttendee(array $attendees)
    {
        $this->attendees[] = $attendees;
    }

    public function getSortDate(): string
    {
        if ($this->startDate) {
            return $this->startDate;
        }

        if ($this->startDateTime) {
            return $this->startDateTime;
        }

        return '';
    }
    /**
     * Setup a notification channel to a resource
     *
     * @param array       $postBody   Channel request body
     * 
     * @postBody string address  Receiving URL
     * 
     * @postBody long expiration  Channel expiration time
     * 
     * @postBody string id  A UUID or similar unique string that identifies this channel
     * 
     * @postBody string kind Identifies this as a notification channel used to 
     * watch for changes to a resource;
     * 
     * @postBody array params Additional parameters controlling delivery channel behavior
     * 
     * @postBody string payload;
     * 
     * @postBody string resourceId An opaque ID that identifies the resource 
     * being watched on this channel
     * 
     * @postBody string resourceUri A version-specific identifier for the 
     * watched resource
     * 
     * @postBody string token An arbitrary string delivered to the target 
     * address with each notification delivered over this channel
     * 
     * @postBody string type The type of delivery mechanism used for this channel
     * 
     * @param  array       $optParams  Optional parameters
     * @param  string|null $calendarId Calendar ID
     * @return Google_Service_Calendar_Channel
     */
    public function watch(array $postBody, $optParams = [],string $calendarId = null)
    {

        $calendar = $calendarId ?? static::getGoogleCalendar();
        $calendarService = $calendar->getService();
        $calendarId = $calendar->getCalendarId();
        $calendarChannel = new Google_Service_Calendar_Channel();

        foreach($postBody as $key => $item){
            $method = "set".$key;
            $calendarChannel->$method($item);
        }

        return $calendarService->events->watch($calendarId, $calendarChannel, $optParams);
    }     

    protected static function getGoogleCalendar(string $calendarId = null): GoogleCalendar
    {
        $calendarId = $calendarId ?? config('google-calendar.calendar_id');

        return GoogleCalendarFactory::createForCalendarId($calendarId);
    }

    protected function setDateProperty(string $name, Carbon $date)
    {
        $eventDateTime = new Google_Service_Calendar_EventDateTime;

        if (in_array($name, ['start.date', 'end.date'])) {
            $eventDateTime->setDate($date->format('Y-m-d'));
            $eventDateTime->setTimezone($date->getTimezone());
        }

        if (in_array($name, ['start.dateTime', 'end.dateTime'])) {
            $eventDateTime->setDateTime($date->format(DateTime::RFC3339));
            $eventDateTime->setTimezone($date->getTimezone());
        }

        if (starts_with($name, 'start')) {
            $this->googleEvent->setStart($eventDateTime);
        }

        if (starts_with($name, 'end')) {
            $this->googleEvent->setEnd($eventDateTime);
        }
    }

    protected function getFieldName(string $name): string
    {
        return [
                   'name' => 'summary',
                   'description' => 'description',
                   'startDate' => 'start.date',
                   'endDate' => 'end.date',
                   'startDateTime' => 'start.dateTime',
                   'endDateTime' => 'end.dateTime',
               ][$name] ?? $name;
    }
}

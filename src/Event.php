<?php

namespace Spatie\GoogleCalendar;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventAttendee;
use Google_Service_Calendar_EventDateTime;
use Google_Service_Calendar_EventSource;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class Event
{
    /** @var \Google_Service_Calendar_Event */
    public $googleEvent;

    /** @var string */
    protected $calendarId;

    /** @var string */
    protected $apiSecretToken;

    /** @var string */
    protected $userToken;

    /** @var array */
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
    public static function createFromGoogleCalendarEvent(Google_Service_Calendar_Event $googleEvent, $calendarId,$apiSecretToken,$userToken)
    {
        $event = new static;

        $event->googleEvent = $googleEvent;
        $event->calendarId = $calendarId;
        $event->apiSecretToken = $apiSecretToken;
        $event->userToken = $userToken;

        return $event;
    }

    /**
     * @param array $properties
     * @param string|null $calendarId
     *
     * @return mixed
     */
    public static function create(array $properties, string $calendarId = null, $optParams = [],string $apiSecretToken=null,string $userToken=null)
    {
        $event = new static;

        $event->calendarId = static::getGoogleCalendar($calendarId,$apiSecretToken,$userToken)->getCalendarId();

        foreach ($properties as $name => $value) {
            $event->$name = $value;
        }

        return $event->save('insertEvent', $optParams);
    }

    public static function quickCreate(string $text)
    {
        $event = new static;

        $event->calendarId = static::getGoogleCalendar()->getCalendarId();

        return $event->quickSave($text);
    }

    public static function get(CarbonInterface $startDateTime = null, CarbonInterface $endDateTime = null, array $queryParameters = [], string $calendarId = null,string $apiSecretToken,string $userToken ): Collection
    {
        $googleCalendar = static::getGoogleCalendar($calendarId,$apiSecretToken,$userToken);

        $googleEvents = $googleCalendar->listEvents($startDateTime, $endDateTime, $queryParameters);

        $googleEventsList = $googleEvents->getItems();

        while ($googleEvents->getNextPageToken()) {
            $queryParameters['pageToken'] = $googleEvents->getNextPageToken();

            $googleEvents = $googleCalendar->listEvents($startDateTime, $endDateTime, $queryParameters);

            $googleEventsList = array_merge($googleEventsList, $googleEvents->getItems());
        }

        $useUserOrder = isset($queryParameters['orderBy']);

        return collect($googleEventsList)
            ->map(function (Google_Service_Calendar_Event $event) use ($calendarId,$apiSecretToken,$userToken) {
                return static::createFromGoogleCalendarEvent($event, $calendarId,$apiSecretToken,$userToken);
            })
            ->sortBy(function (self $event, $index) use ($useUserOrder) {
                if ($useUserOrder) {
                    return $index;
                }

                return $event->sortDate;
            })
            ->values();
    }

    public static function find($eventId, string $calendarId = null,string $apiSecretToken=null,string $userToken=null): self
    {
        $googleCalendar = static::getGoogleCalendar($calendarId,$apiSecretToken,$userToken);

        $googleEvent = $googleCalendar->getEvent($eventId);

        return static::createFromGoogleCalendarEvent($googleEvent, $calendarId,$apiSecretToken,$userToken);
    }

    public function __get($name)
    {
        $name = $this->getFieldName($name);

        if ($name === 'sortDate') {
            return $this->getSortDate();
        }

        if ($name === 'source') {
            return [
                'title' => $this->googleEvent->getSource()->title,
                'url' => $this->googleEvent->getSource()->url,
            ];
        }

        $value = Arr::get($this->googleEvent, $name);

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

        if ($name == 'source') {
            $this->setSourceProperty($value);

            return;
        }

        Arr::set($this->googleEvent, $name, $value);
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

        $googleCalendar = $this->getGoogleCalendar($this->calendarId,$this->apiSecretToken,$this->userToken);

        $googleEvent = $googleCalendar->$method($this, $optParams);

        return static::createFromGoogleCalendarEvent($googleEvent, $googleCalendar->getCalendarId(),$googleCalendar->getApiSecretToken(),$googleCalendar->getUserToken());
    }

    public function quickSave(string $text): self
    {
        $googleCalendar = $this->getGoogleCalendar($this->calendarId,$this->apiSecretToken,$this->userToken);

        $googleEvent = $googleCalendar->insertEventFromText($text);

        return static::createFromGoogleCalendarEvent($googleEvent, $googleCalendar->getCalendarId(),$googleCalendar->getApiSecretToken(),$googleCalendar->getUserToken());
    }

    public function update(array $attributes, $optParams = []): self
    {
        foreach ($attributes as $name => $value) {
            $this->$name = $value;
        }

        return $this->save('updateEvent', $optParams);
    }

    public function delete(string $eventId = null, $optParams = [])
    {
        $this->getGoogleCalendar($this->calendarId,$this->apiSecretToken,$this->userToken)->deleteEvent($eventId ?? $this->id, $optParams);
    }

    public function addAttendee(array $attendee)
    {
        $this->attendees[] = new Google_Service_Calendar_EventAttendee([
            'email' => $attendee['email'],
            'comment' => $attendee['comment'] ?? null,
            'displayName' => $attendee['name'] ?? null,
        ]);

        $this->googleEvent->setAttendees($this->attendees);
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

    public function getCalendarId(): string
    {
        return $this->calendarId;
    }

    public function getApiSecretToken(): string
    {
        return $this->apiSecretToken;
    }

    public function getUserToken(): string
    {
        return $this->userToken;
    }

    protected static function getGoogleCalendar(string $calendarId = null,$apiSecretToken,$userToken): GoogleCalendar
    {
        $calendarId = $calendarId ?? config('google-calendar.calendar_id');

        return GoogleCalendarFactory::createForCalendarId($calendarId,$apiSecretToken,$userToken);
    }

    protected function setDateProperty(string $name, CarbonInterface $date)
    {
        $eventDateTime = new Google_Service_Calendar_EventDateTime;

        if (in_array($name, ['start.date', 'end.date'])) {
            $eventDateTime->setDate($date->format('Y-m-d'));
            $eventDateTime->setTimezone((string) $date->getTimezone());
        }

        if (in_array($name, ['start.dateTime', 'end.dateTime'])) {
            $eventDateTime->setDateTime($date->format(DateTime::RFC3339));
            $eventDateTime->setTimezone((string) $date->getTimezone());
        }

        if (Str::startsWith($name, 'start')) {
            $this->googleEvent->setStart($eventDateTime);
        }

        if (Str::startsWith($name, 'end')) {
            $this->googleEvent->setEnd($eventDateTime);
        }
    }

    protected function setSourceProperty(array $value)
    {
        $source = new Google_Service_Calendar_EventSource([
            'title' => $value['title'],
            'url' => $value['url'],
        ]);

        $this->googleEvent->setSource($source);
    }

    public function setColorId(int $id)
    {
        $this->googleEvent->setColorId($id);
    }

    protected function getFieldName(string $name): string
    {
        return [
            'name' => 'summary',
            'startDate' => 'start.date',
            'endDate' => 'end.date',
            'startDateTime' => 'start.dateTime',
            'endDateTime' => 'end.dateTime',
        ][$name] ?? $name;
    }
}

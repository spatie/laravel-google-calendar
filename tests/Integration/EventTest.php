<?php

namespace Spatie\GoogleCalendar\Tests\Integration;

use DateTime;
use Carbon\Carbon;
use Spatie\GoogleCalendar\Event;
use Spatie\GoogleCalendar\Tests\TestCase;

class EventTest extends TestCase
{
    /** @var \Spatie\GoogleCalendar\Event */
    protected $event;

    public function setUp()
    {
        parent::setUp();

        $this->event = new Event;
    }

    /** @test */
    public function it_can_set_a_start_date()
    {
        $now = Carbon::now();

        $this->event->startDate = $now;

        $this->assertEquals($now->startOfDay()->format('Y-m-d'), $this->event->googleEvent['start']['date']);

        $this->assertEquals($now, $this->event->startDate);
    }

    /** @test */
    public function it_can_set_a_end_date()
    {
        $now = Carbon::now();

        $this->event->endDate = $now;

        $this->assertEquals($now->format('Y-m-d'), $this->event->googleEvent['end']['date']);
    }

    /** @test */
    public function it_can_set_a_start_date_time()
    {
        $now = Carbon::now();

        $this->event->startDateTime = $now;

        $this->assertEquals($now->format(DateTime::RFC3339), $this->event->googleEvent['start']['dateTime']);
    }

    /** @test */
    public function it_can_set_an_end_date_time()
    {
        $now = Carbon::now();

        $this->event->endDateTime = $now;

        $this->assertEquals($now->format(DateTime::RFC3339), $this->event->googleEvent['end']['dateTime']);
    }

    /** @test */
    public function it_can_determine_a_sort_date()
    {
        $now = Carbon::now();

        $event = new Event;

        $this->assertEmpty($event->getSortDate());

        $event->startDateTime = $now;

        $this->assertEquals($now, $event->getSortDate());
    }

    /** @test */
    public function it_can_set_a_name()
    {
        $this->event->name = 'testname';

        $this->assertEquals('testname', $this->event->googleEvent['summary']);
    }

    /** @test */
    public function it_can_set_a_location()
    {
        $this->event->location = 'Test Location';

        $this->assertEquals('Test Location', $this->event->googleEvent->getLocation());
    }

    /** @test */
    public function it_can_determine_if_an_event_is_an_all_day_event()
    {
        $event = new Event;

        $event->startDate = Carbon::now();

        $this->assertTrue($event->isAllDayEvent());

        $event->startDateTime = Carbon::now();

        $this->assertFalse($event->isAllDayEvent());
    }
}

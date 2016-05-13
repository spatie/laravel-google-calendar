<?php

namespace Spatie\GoogleCalendar\Test;

use DateTime;
use Mockery;
use Spatie\GoogleCalendar\Test\Integration\TestCase;
use Spatie\GoogleCalendar\Event;

class EventTest extends TestCase
{
    /** @var \Spatie\GoogleCalendar\Event|Mockery\Mock */
    protected $event;

    public function setUp()
    {
        parent::setUp();

        $this->event = new Event();
    }

    
    /** @test */
    public function it_will_use_the_calendar_id_from_the_config_file_by_default()
    {
        $this->assertEquals($this->calenderId, $this->event->calendarId);
    }

    /** @test */
    public function it_can_set_a_start_date()
    {
        $now = \Carbon\Carbon::now();

        $this->event->startDate = $now;

        $this->assertEquals($now->format('Y-m-d'), $this->event->googleEvent['start']['date']);

        $this->assertEquals($now, $this->event->startDate);
    }

    /** @test */
    public function it_can_set_a_end_date()
    {
        $now = \Carbon\Carbon::now();

        $this->event->endDate = $now;

        $this->assertEquals($now->format('Y-m-d'), $this->event->googleEvent['end']['date']);
    }

    /** @test */
    public function it_can_set_a_start_date_time()
    {
        $now = \Carbon\Carbon::now();

        $this->event->startDateTime = $now;

        $this->assertEquals($now->format(DateTime::RFC3339), $this->event->googleEvent['start']['dateTime']);
    }

    /** @test */
    public function it_can_set_an_end_date_time()
    {
        $now = \Carbon\Carbon::now();

        $this->event->endDateTime = $now;

        $this->assertEquals($now->format(DateTime::RFC3339), $this->event->googleEvent['end']['dateTime']);
    }

    /** @test */
    public function it_can_determine_a_sort_date()
    {
        $now = \Carbon\Carbon::now();

        $event = new Event;

        $this->assertNull($event->getSortDate());

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
    public function it_can_determine_if_an_event_is_an_all_day_event()
    {
        $event = new Event();

        $event->startDate = \Carbon\Carbon::now();

        $this->assertTrue($event->isAllDayEvent());

        $event->startDateTime = \Carbon\Carbon::now();

        $this->assertFalse($event->isAllDayEvent());
    }


}

<?php

namespace Spatie\GoogleCalendar;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use DateTime;

use Google_Service_Directory;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;


class Resource {
    /** @var \Google_Service_Directory */
    protected $resourceService;


    public function __construct(Google_Service_Directory $resourceService)
    {
        $this->resourceService = $resourceService;
        $this->resources = [];
    }


    public static function get(): Array
    {
        $adminService = GoogleCalendarFactory::createForResources();
       
        $googleResources = $adminService->resourceService->resources_calendars->listResourcesCalendars('my_customer'); //the whole resource object

        $googleResourcesList = $googleResources->getItems();

        while ($googleResources->getNextPageToken()) {
            $queryParameters['pageToken'] = $googleResources->getNextPageToken();

            $googleResources = $adminService->resources_calendars->listResourcesCalendars('my_customer');

            $googleResourcesList = array_merge($googleResourcesList, $googleResources->getItems());
        }

        $resources=$googleResourcesList;

        foreach($resources as $key => $resource) {
            $resourceList[] = array(
                'name' => $resource->getResourceName() ,
                'id' => $resource->getResourceId() ,
                'generatedname' => $resource->getGeneratedResourceName() ,
                'capacity' => $resource->getCapacity() ,
                'floorname' => $resource->getFloorName() ,
                'floorsection' => $resource->getFloorSection() ,
                'features'=> $resource->getFeatureInstances(),
                'email' => $resource->getResourceEmail()
            );
        }

        return $resourceList;
    }
}
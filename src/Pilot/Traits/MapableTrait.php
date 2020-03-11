<?php

namespace Flex360\Pilot\Pilot\Traits;

trait MapableTrait
{

    public function getStreetAddress()
    {
        return $this->address . ', ' . $this->city . ', ' . $this->state . ' ' . $this->zip;
    }

    public function getMapImage()
    {
        $params = [
            'key' => config('app.google_maps_api_key'),
            'center' => $this->lat . ',' . $this->lng,
            'zoom' => 12,
            'size' => '600x300',
            'markers' => 'color:red|' . $this->lat . ',' . $this->lng,
        ];

        return 'https://maps.googleapis.com/maps/api/staticmap?' . http_build_query($params);
    }

    public function getDirectionsLink()
    {
        $params = [
            'daddr' => $this->getStreetAddress()
        ];

        return 'https://maps.google.com?' . http_build_query($params);
    }
}

<?php

namespace Flex360\Pilot\Pilot\Helpers;

class Geocoder
{
    public static function fromAddress($address)
    {
        $params = array(
            'key' => env('GOOGLE_API_MAPS_KEY'),
            'sensor' => 'false',
            'address' => urlencode($address),
        );

        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);

        $result = json_decode(file_get_contents($url));

        $status = null;

        if (isset($result->results) && isset($result->results[0])) {
            $status = 'success';
            $location = $result->results[0]->geometry->location;
        } else {
            $status = 'failed';
            $location = null;

            return false;
        }

        return $location;
    }
}

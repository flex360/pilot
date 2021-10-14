<?php

namespace Flex360\Pilot\Pilot\Helpers;

class Geocoder
{
    public static function fromAddress($address)
    {
        $params = array(
            'key' => env('GOOGLE_API_GEOCODING_KEY'),
            'sensor' => 'false',
            'address' => urlencode($address),
        );


        $url = 'https://maps.googleapis.com/maps/api/geocode/json?' . http_build_query($params);

        $result = json_decode(file_get_contents($url));

        if (isset($result->results) && !empty($result->results)) {
            $location = $result->results[0]->geometry->location;
        } else {
            $message = $result->error_message ?? 'Gecoding failed.';

            if (isset($result->status) && $result->status == 'ZERO_RESULTS') {
                $message = 'Address not found. Latitude and longitude not updated.';
            }
            
            // throw(new \Exception($message));

            return [
                'status' => 'failed',
                'message' => $message,
            ];
        }

        return $location;
    }
}

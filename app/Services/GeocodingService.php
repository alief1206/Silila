<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeocodingService
{
    protected $client;
    protected $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = env('GOOGLE_MAPS_API_KEY');
    }

    public function getLocation($latitude, $longitude)
    {
        $url = 'https://maps.googleapis.com/maps/api/geocode/json';
        $response = $this->client->get($url, [
            'query' => [
                'latlng' => "{$latitude},{$longitude}",
                'key' => $this->apiKey
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        if (isset($data['results'][0])) {
            return $data['results'][0];
        }

        return null;
    }

    public function getSubDistrictAndVillage($latitude, $longitude)
    {
        $locationData = $this->getLocation($latitude, $longitude);

        if ($locationData) {
            $addressComponents = $locationData['address_components'];
            $subDistrict = null;
            $village = null;

            foreach ($addressComponents as $component) {
                if (in_array('administrative_area_level_3', $component['types'])) {
                    $subDistrict = $component['long_name'];
                }

                if (in_array('administrative_area_level_4', $component['types'])) {
                    $village = $component['long_name'];
                }
            }

            return [
                'kecamatan' => str_replace("Kecamatan ", "", $subDistrict),
                'desa' => str_replace("Desa ", "", $village)
            ];
        }

        return null;
    }
}

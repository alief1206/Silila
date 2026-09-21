<?php

namespace App\Services;

use GuzzleHttp\Client;

class GeocodingService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getLocation($latitude, $longitude)
    {
        $url = 'https://nominatim.openstreetmap.org/reverse';
        $response = $this->client->get($url, [
            'headers' => [
                'User-Agent' => 'SILILA-Banyuwangi-App/1.0'
            ],
            'query' => [
                'lat' => $latitude,
                'lon' => $longitude,
                'format' => 'json',
                'addressdetails' => 1
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        if (isset($data['address'])) {
            return $data['address'];
        }

        return null;
    }

    public function getSubDistrictAndVillage($latitude, $longitude)
    {
        $address = $this->getLocation($latitude, $longitude);

        if ($address) {
            // Coba ambil nama kecamatan
            $subDistrict = $address['county'] ?? $address['city_district'] ?? $address['suburb'] ?? $address['municipality'] ?? null;
            
            // Coba ambil nama desa/kelurahan
            $village = $address['village'] ?? $address['hamlet'] ?? $address['suburb'] ?? $address['neighbourhood'] ?? null;

            return [
                'kecamatan' => str_replace("Kecamatan ", "", $subDistrict),
                'desa' => str_replace(["Desa ", "Kelurahan "], "", $village)
            ];
        }

        return null;
    }
}

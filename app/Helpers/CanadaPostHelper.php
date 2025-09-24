<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class CanadaPostHelper
{
    protected $client;
    protected $username;
    protected $password;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('CANADA_POST_API_URL', 'https://ct.soa-gw.canadapost.ca/'),
            // production url : soa-gw.canadapost.ca
        ]);

        // $this->username = env('CANADA_POST_USERNAME');
        // $this->password = env('CANADA_POST_PASSWORD');
        $this->username = '295db334d7ebaeda' ;
        $this->password = '72ed2e2e0a39d49fb9ad18';
        
    }

    public function createShipment(array $shipmentData)
    {
        try { 
            $response = $this->client->post("rs/0006156622/0006156622/shipment", [
                'auth' => [$this->username, $this->password],
                'json' => $shipmentData,
                'headers' => [
                    'Accept' => 'application/vnd.cpc.shipment-v8+json',
                    'Content-Type' => 'application/vnd.cpc.shipment-v8+json',
                ],
            ]);

            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}

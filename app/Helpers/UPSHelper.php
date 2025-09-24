<?php

namespace App\Helpers;

use GuzzleHttp\Client;

class UPSHelper
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => env('UPS_API_BASE_URI', 'https://wwwcie.ups.com/'), // Default to test environment
        ]);
    }

    public function createShipment(array $shipmentData)
    {
        try {
            $response = $this->client->post('api/shipments/v2409/ship', [
                'json' => $shipmentData,
                'headers' => [
                    'Content-Type' => 'application/json',
                    'AccessLicenseNumber' => env('UPS_ACCESS_LICENSE'),
                    'Username' => env('UPS_USERNAME'),
                    'Password' => env('UPS_PASSWORD'),
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);

            $trackingNumber = $responseData['ShipmentResponse']['ShipmentResults']['ShipmentIdentificationNumber'] ?? null;
            $trackingUrl = $trackingNumber
                ? "https://wwwapps.ups.com/WebTracking/track?track=yes&trackNums={$trackingNumber}"
                : null;

            return [
                'tracking_number' => $trackingNumber,
                'tracking_url' => $trackingUrl,
                'response' => $responseData,
            ];
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $errorResponse = $e->getResponse();
            $errorMessage = $errorResponse
                ? $errorResponse->getBody()->getContents()
                : $e->getMessage();

            return ['error' => $errorMessage];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
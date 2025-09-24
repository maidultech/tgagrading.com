<?php

namespace App\Http\Controllers;

use App\Mail\OrderShipmentMail;
use App\Models\Order;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Mitrik\Shipping\ServiceProviders\Address\Address;
use Mitrik\Shipping\ServiceProviders\Box\BoxCollection;
use Mitrik\Shipping\ServiceProviders\Box\BoxImperial;
use Mitrik\Shipping\ServiceProviders\Box\BoxInterface;
use Mitrik\Shipping\ServiceProviders\Box\BoxMetric;
use Mitrik\Shipping\ServiceProviders\Phone\Phone;
use Mitrik\Shipping\ServiceProviders\ServiceProviderService\ServiceProviderService;
use Mitrik\Shipping\ServiceProviders\ServiceUPS\ServiceUPS;
use Mitrik\Shipping\ServiceProviders\ServiceUPS\ServiceUPSCredentials;
use Mitrik\Shipping\ServiceProviders\ShipFrom\ShipFrom;
use Mitrik\Shipping\ServiceProviders\ShipTo\ShipTo;
use Toastr;

class UPSController extends Controller
{
    function unitOfMeasurementWeight(): string
    {
        return 'KG';
    }

    public function createShipment(Request $request, $id)
    {
        // dd($this->track('1ZXXXXXXXXXXXXXXXX'));
        $test = '';

        $setting = getSetting();
        $order = Order::where('id', $id)->first();


        $ups_client_id = $setting->ups_client_id;
        $ups_client_secret = $setting->ups_client_secret;
        $ups_user_id = $setting->ups_user_id;
        $mode = $setting->ups_mode;

        if ($mode == 'live') {
            $test = False;
        } else {
            $test = True;
        }

        $credentials = new ServiceUPSCredentials($ups_client_id, $ups_client_secret, $ups_user_id, $ups_user_id, $test);
        $serviceUPS = new ServiceUPS($credentials);
        // dd($serviceUPS->token());
        $height = 5;
        $width = 5;
        $legnth = 5;
        $weight = $this->kgToLbs($order->cards->count() * 0.08);

        $fromPhone = shippingPhoneNumberFormat($setting->phone_no);
        $toPhone = shippingPhoneNumberFormat($request->phone);
        if (!$order->shipping_method_service_code) {
            toastr()->error('Please select a shipping method first', 'Error');
            return back();
        }
        try {
            
            $result = $serviceUPS->ship(
                new ShipFrom(
                    $request->name, // 'Full Name'
                    $request->name, // 'Full Name'
                    new Address(
                        $setting->site_name,
                        '',                // Last Name
                        $setting->site_name,
                        // 'Tyler',               // First Name
                        // 'stilborn',                // Last Name
                        // '1511297 BC LTD',          // Company Name (optional)
                        '9-16228 16TH AVE SURREY BC V4A 1S7',        // Address Line 1
                        '',          // Address Line 2 (optional)
                        'SURREY',            // City
                        'V4A 1S7',            // Postal Code (Canadian format)
                        'BC',                 // Province Code (ISO 2)
                        'CA'                  // Country Code (ISO 2 for Canada)
                    ),
                    new Phone($fromPhone[0], $fromPhone[1], str($setting->phone_no)->after($fromPhone[1])->remove(['-', ' '])),  // format : '+1', '555', '1231234'
                    $setting->site_name //'Company Name' 
                ),
                new ShipTo(
                    $request->name, // 'Full Name'
                    $request->name, // 'Full Name'
                    new Address(
                        $order->transaction->shipping_data['shippingName'], // 'First Name'
                        '', // 'Last Name'
                        '',
                        "{$order->transaction->shipping_data['shippingAddress']}", // 'line1'
                        '', // 'line2'
                        $order->transaction->shipping_data['shippingCity'], // 'city'
                        $order->transaction->shipping_data['shippingZip'], // 'postalCode'
                        stateToIso2($order->transaction->shipping_data['shippingState'] ?? $order->rUser->defaultAddress->state, true), // 'stateCodeIso2'
                        str($order->transaction->shipping_data['shippingCountry'] ?? $order->rUser->defaultAddress->country)->lower()->contains(['united', 'states']) ? 'US' : 'CA', // 'countryCodeIso2'
                    ),
                    new Phone($toPhone[0], $toPhone[1], str($request->phone)->after($toPhone[1])->remove(['-', ' '])),  // format : '+1', '555', '1231234'
                ),
                new BoxCollection([
                    new BoxImperial($legnth, $width, $height, $weight)
                ]),
                new ServiceProviderService(array_key_first($order->shipping_method_service_code), array_values($order->shipping_method_service_code)[0])
            );
            $order->shipping_api_response = $result->first()->metaData();
            $order->admin_tracking_id = "" . $result->first()->trackingNumber();
            $order->admin_tracking_note = "Shipped via UPS, Tracking Number: " . $result->first()->trackingNumber() . ". You can track your order at https://www.ups.com/track?loc=en_US&tracknum=" . $result->first()->trackingNumber();
            $order->save();
            Mail::to($request->email ?? $order->rUser->email)->send(new OrderShipmentMail($order));
            Toastr::success(('Shipped successfully #' . $result->first()->trackingNumber()), 'Success');
        } catch (GuzzleException $e) {
            Toastr::error(exceptionMsgConverter($e), 'Error');
        } catch (\Throwable $th) {
            // dd($th);
            \Log::info($th);
            Toastr::error('Failed to make shipment: ' . $th->getMessage(), 'Error');
        }
        return back();
        // dd($result);
    }

    function track(Order $order)
    {
        $test = '';

        $setting = getSetting();
        $ups_client_id = $setting->ups_client_id;
        $ups_client_secret = $setting->ups_client_secret;
        $ups_user_id = $setting->ups_user_id;
        $mode = $setting->ups_mode;

        if ($mode == 'live') {
            $test = False;
        } else {
            $test = True;
        }

        $tracking_number = $order->admin_tracking_id;
        $shipping_api_response = ($order->shipping_api_response);
        // if(!isset($shipping_api_response['ShipmentResponse']['Response']['TransactionReference']['TransactionIdentifier'])){
        //     return false;
        // }
        $credentials = new ServiceUPSCredentials($ups_client_id, $ups_client_secret, $ups_user_id, $ups_user_id, $test);
        $serviceUPS = new ServiceUPS($credentials);
        $token = $serviceUPS->token();

        try {
            $trackingResponse = Http::
                withHeaders([
                    'transactionSrc' => 'testing',
                    // 'transId' => $shipping_api_response['ShipmentResponse']['Response']['TransactionReference']['TransactionIdentifier']
                    'transId' => $order->order_number
                ])->
                withToken($token)->
                get("https://" . ($test ? 'wwwcie.ups.com' : 'onlinetools.ups.com') . "/api/track/v1/details/{$tracking_number}")
                ->throw()
                ->json();
            return $trackingResponse;
        } catch (GuzzleException $th) {
            return false;
            // dd($th);
        } catch (\Throwable $th) {
            return false;
            // dd($th);
        }
    }

    // public function rate($id, $setRate = false)
    // {
    //     $test = '';

    //     $setting = getSetting();
    //     $order = Order::where('id', $id)->first();


    //     $ups_client_id = $setting->ups_client_id;
    //     $ups_client_secret = $setting->ups_client_secret;
    //     $ups_user_id = $setting->ups_user_id;
    //     $mode = $setting->ups_mode;

    //     if ($mode == 'live') {
    //         $test = False;
    //     } else {
    //         $test = True;
    //     }

    //     $credentials = new ServiceUPSCredentials($ups_client_id, $ups_client_secret, $ups_user_id, $ups_user_id, $test);
    //     $serviceUPS = new ServiceUPS($credentials);
    //     $height = 5;
    //     $width = 5;
    //     $legnth = 5;
    //     $weight = $this->kgToLbs($order->cards->count() * 0.08);
    //     $rateArray = collect();
        
    //     try {

    //         $upsRates = $serviceUPS->rate(
    //             new Address(
    //                 $setting->site_name,               // First Name
    //                 '',                // Last Name
    //                 $setting->site_name,          // Company Name (optional)
    //                 'PO BOX 38131 SURREY BC V3Z 6R3',        // Address Line 1
    //                 '',          // Address Line 2 (optional)
    //                 'Surrey',            // City
    //                 'V3Z 6R3',            // Postal Code (Canadian format)
    //                 'BC',                 // Province Code (ISO 2)
    //                 'CA'                  // Country Code (ISO 2 for Canada)
    //             ),
    //             new Address(
    //                 $order->transaction->shipping_data['shippingName'], // 'First Name'
    //                 '', // 'Last Name'
    //                 '',
    //                 "{$order->transaction->shipping_data['shippingAddress']}", // 'line1'
    //                 '', // 'line2'
    //                 $order->transaction->shipping_data['shippingCity'], // 'city'
    //                 strtoupper(preg_replace('/\s+/u', '', $order->transaction->shipping_data['shippingZip'])), // 'postalCode'
    //                 stateToIso2($order->transaction->shipping_data['shippingState'] ?? $order->rUser->defaultAddress->state, true), // 'stateCodeIso2'
    //                 str($order->transaction->shipping_data['shippingCountry'] ?? $order->rUser->defaultAddress->country)->lower()->contains(['united', 'states']) ? 'US' : 'CA', // 'countryCodeIso2'
    //             ),
    //             new BoxCollection([
    //                 new BoxMetric($legnth, $width, $height, $weight)
    //             ])
    //         );

    //         foreach ($upsRates as $key => $value) {

    //             if (in_array($value->serviceProviderService()->serviceCode(), ["8", "11"])) {

    //                 $tmpArr = [
    //                     'serviceCode' => $value->serviceProviderService()->serviceCode(),
    //                     'serviceName' => $value->serviceProviderService()->serviceName(),
    //                     'price' => $value->price(),
    //                     'finalCharge' => (float) number_format($value->price() * getSetting()->shipping_cost_maximization, 2),
    //                     'metaData' => $value->metaData()
    //                 ];
    //                 $rateArray->push($tmpArr);
    //                 if ($setRate && $setRate == $value->serviceProviderService()->serviceCode()) {
    //                     $order->shipping_method_service_code = [
    //                         $value->serviceProviderService()->serviceCode() => $value->serviceProviderService()->serviceName()
    //                     ];
    //                     $order->save();
    //                     session(['checkout.delivery_charge_' . $order->id => $tmpArr]);
    //                 }
    //             }

    //         }

    //         return [
    //             'success' => true,
    //             'data' => $rateArray
    //         ];
    //     } catch (\Throwable $th) {
    //         return [
    //             'success' => false,
    //             'message' => exceptionMsgConverter($th),
    //             'data' => []
    //         ];
    //     }
    // }

    function kgToLbs($kg)
    {
        return $kg * 2.20462;
    }


    public function rate($id, $setRate = false)
    {
        $setting = getSetting();
        $order = Order::where('id', $id)->firstOrFail();
        $accessToken = $this->getUpsAccessToken();

        $rateArray = collect();

        try {
            $shipToData = $order->transaction->shipping_data;
            $toCountry = strtolower($shipToData['shippingCountry'] ?? $order->rUser->defaultAddress->country);
            $toCountryCode = str($toCountry)->contains(['united', 'states']) ? 'US' : 'CA';

            $payload = [
                "RateRequest" => [
                    "Request" => [
                        "TransactionReference" => [
                            "CustomerContext" => "Rate Check"
                        ]
                    ],
                    "Shipment" => [
                        "Shipper" => [
                            "Name" => $setting->site_name,
                            "ShipperNumber" => $setting->ups_user_id, 
                            "Address" => [
                                "AddressLine" => [
                                    "9-16228 16th Ave",
                                    "SURREY",
                                    "BC"
                                ],
                                "City" => "Surrey",
                                "StateProvinceCode" => "BC",
                                "PostalCode" => "V4A 1S7",
                                "CountryCode" => "CA"
                            ]
                        ],
                        "ShipTo" => [
                            "Name" => $shipToData['shippingName'],
                            "Address" => [
                                "AddressLine" => [
                                    $shipToData['shippingAddress'],
                                    $shipToData['shippingAddress'],
                                    $shipToData['shippingAddress']
                                ],
                                "City" => $shipToData['shippingCity'],
                                "StateProvinceCode" => stateToIso2($shipToData['shippingState'] ?? $order->rUser->defaultAddress->state, true),
                                "PostalCode" => preg_replace('/\s+/u', '', $shipToData['shippingZip']),
                                "CountryCode" => $toCountryCode
                            ]
                        ],
                        "ShipFrom" => [
                            "Name" => $setting->site_name,
                            "Address" => [
                                "AddressLine" => [
                                    "9-16228 16th Ave V4A 1S7",
                                    "SURREY",
                                    "BC"
                                ],
                                "City" => "Surrey",
                                "StateProvinceCode" => "BC",
                                "PostalCode" => "V4A 1S7",
                                "CountryCode" => "CA"
                            ]
                        ],
                        "ShipmentRatingOptions" => [
                            "TPFCNegotiatedRatesIndicator" => "Y",
                            "NegotiatedRatesIndicator" => "Y"
                        ],
                        "Service" => [
                            "Code" => "11",
                            "Description" => "UPS Standard"
                        ],
                        "NumOfPieces" => "1",
                        "Package" => [
                            "PackagingType" => [
                                "Code" => "02",
                                "Description" => "Package"
                            ],
                            "Dimensions" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "CM",
                                    "Description" => "Centimeters"
                                ],
                                "Length" => "5",
                                "Width" => "5",
                                "Height" => "5"
                            ],
                            "PackageWeight" => [
                                "UnitOfMeasurement" => [
                                    "Code" => "KGS",
                                    "Description" => "Kilograms"
                                ],
                                "Weight" => (string) ($order->cards->count() * 0.08)
                            ]
                        ]
                    ]
                ]
            ];

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => "https://onlinetools.ups.com/api/rating/v2409/Shop?", // 'Shop' or other option
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    "Authorization: Bearer {$accessToken}",
                    "Content-Type: application/json",
                    "transId: string",
                    "transactionSrc: testing"
                ],
            ]);

            $response = curl_exec($curl);

            $error = curl_error($curl);
            curl_close($curl);

            if ($error) {
                throw new \Exception("cURL Error: " . $error);
            }

            $decoded = json_decode($response, true);
            $ratedShipments = $decoded['RateResponse']['RatedShipment'] ?? [];

            foreach ($ratedShipments as $shipment) {
                $serviceCode = $shipment['Service']['Code'];
                $serviceName = $shipment['Service']['Description'];
                $totalPrice = (float) $shipment['NegotiatedRateCharges']['TotalCharge']['MonetaryValue'];

                if (in_array($serviceCode, ['8', '11'])) {
                    $finalPrice = number_format($totalPrice * getSetting()->shipping_cost_maximization, 2);
                    $tmpArr = [
                        'serviceCode' => $serviceCode,
                        'serviceName' => 'UPS Standard',
                        'price' => $totalPrice,
                        'finalCharge' => (float) $finalPrice,
                        'metaData' => $shipment
                    ];

                    $rateArray->push($tmpArr);

                    if ($setRate && $setRate == $serviceCode) {
                        $order->shipping_method_service_code = [$serviceCode => $serviceName];
                        $order->save();
                        session(['checkout.delivery_charge_' . $order->id => $tmpArr]);
                    }
                }
            }

            return [
                'success' => true,
                'data' => $rateArray,
                'shipping_address' => [
                    'name' => $shipToData['shippingName'],
                    'city' => $shipToData['shippingCity'],
                    'state' => stateToIso2($shipToData['shippingState'], true),
                    'postal_code' => $shipToData['shippingZip'],
                    'country' => $toCountryCode,
                ]
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => exceptionMsgConverter($th),
                'data' => []
            ];
        }
    }

    public function getUpsAccessToken()
    {
        $setting = getSetting();

        $clientId = $setting->ups_client_id;
        $clientSecret = $setting->ups_client_secret;
        $isTestMode = $setting->ups_mode !== 'live';

        $url = $isTestMode
            ? 'https://wwwcie.ups.com/security/v1/oauth/token'
            : 'https://onlinetools.ups.com/security/v1/oauth/token';

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => "grant_type=client_credentials",
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode("$clientId:$clientSecret"),
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        curl_close($curl);

        if ($error) {
            throw new \Exception("UPS OAuth Token Error: " . $error);
        }

        $data = json_decode($response, true);

        if (isset($data['access_token'])) {
            return $data['access_token'];
        }

        throw new \Exception("Unable to retrieve UPS access token: " . json_encode($data));
    }

}

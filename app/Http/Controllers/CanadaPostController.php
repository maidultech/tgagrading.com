<?php

namespace App\Http\Controllers;

use App\Helpers\CanadaPostHelper;
use App\Http\Controllers\Controller;
use App\Mail\OrderShipmentMail;
use App\Models\Order;
use App\Service\CanadaPostService;
use Brian2694\Toastr\Facades\Toastr;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mitrik\Shipping\ServiceProviders\Address\Address;
use Mitrik\Shipping\ServiceProviders\Box\BoxCollection;
use Mitrik\Shipping\ServiceProviders\Box\BoxImperial;
use Mitrik\Shipping\ServiceProviders\Box\BoxMetric;
use Mitrik\Shipping\ServiceProviders\Phone\Phone;
use Mitrik\Shipping\ServiceProviders\ServiceCanadaPost\ServiceCanadaPostCredentials;
use Mitrik\Shipping\ServiceProviders\ServiceProviderService\ServiceProviderService;
use Mitrik\Shipping\ServiceProviders\ServiceProviderShipment\ServiceProviderShipmentCustomsValue;
use Mitrik\Shipping\ServiceProviders\ShipFrom\ShipFrom;
use Mitrik\Shipping\ServiceProviders\ShipTo\ShipTo;

class CanadaPostController extends Controller
{
    public function createShipment(Request $request, $id)
    {
        $test = '';

        $setting = getSetting();
        $order = Order::where('id', $id)->first();

        $customer_number = $setting->canadapost_customer_number;
        $username = $setting->canadapost_username;
        $password = $setting->canadapost_password;
        $mode = $setting->canadapost_mode;

        if($mode == 'live')
        {
            $test = False;
        } else {
            $test = True;
        }
        $credentials = new ServiceCanadaPostCredentials($customer_number, $username, $password, $test);
        $canadaPost = new CanadaPostService($credentials);

        $height = 5;
        $width = 5;
        $legnth = 5;
        $weight = $order->cards->count()*0.08;
        $fromPhone = shippingPhoneNumberFormat($setting->phone_no);
        $toPhone = shippingPhoneNumberFormat($request->phone);
        $itemList = [];
        foreach ($order->details as $key => $detail) {
            $itemList[]= [
                "customs-description" => $detail->product_name,
                "unit-weight" => 0.08,
                "customs-number-of-units" => $order->cards->count(),
                "customs-value-per-unit" => $order->net_unit_price,
                "country-of-origin" => "CA",
                "province-of-origin" => "BC",
            ];
        }
        if(!$order->shipping_method_service_code){
            toastr()->error('Please select a shipping method first', 'Error');
            return back();
        }
        try {
            $result = $canadaPost->ship(
                new ShipFrom(
                    $request->name,              // 'Full Name'
                    $request->name,     // 'Full Name'
                    new Address(
                        $setting->site_name,
                        '',                  // Last Name
                        $setting->site_name,
                        // 'Tyler',                    // First Name
                        // 'stilborn',                 // Last Name
                        // '1511297 BC LTD',           // Company Name (optional)
                        '9-16228 16TH AVE SURREY BC V4A 1S7',       // Address Line 1
                        '',                                     // Address Line 2 (optional)
                        'SURREY',                                // City
                        'V4A 1S7',                         // Postal Code (Canadian format)
                        'BC',                           // Province Code (ISO 2)
                        'CA'                          // Country Code (ISO 2 for Canada)
                    ),
                    new Phone($fromPhone[0], $fromPhone[1], str($setting->phone_no)->after($fromPhone[1])->remove(['-',' '])),  // format : '+1', '555', '1231234'
                    $setting->support_email, // 'Email Address'
                    now()->addDay(),
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
                        stateToIso2($order->transaction->shipping_data['shippingState'] ?? $order->rUser->defaultAddress->state,true), // 'stateCodeIso2'
                        str($order->transaction->shipping_data['shippingCountry'] ?? $order->rUser->defaultAddress->country)->lower()->contains(['united','states']) ? 'US' : 'CA', // 'countryCodeIso2'
                    ),
                    new Phone($toPhone[0], $toPhone[1], str($request->phone)->after($toPhone[1])->remove(['-',' '])),  // format : '+1', '555', '1231234'
                    $request->email ?? $order->rUser->email
                ),
                new BoxCollection([
                    new BoxImperial($legnth, $width, $height, $weight)
                ]),
                new ServiceProviderService(array_key_first($order->shipping_method_service_code), array_values($order->shipping_method_service_code)[0]),
                new ServiceProviderShipmentCustomsValue($order->net_unit_price * $order->cards->count(), 'USD', [
                    "currency" => "USD",
                    "conversion-from-cad" => 0.85,
                    "reason-for-export" => "SOG",
                
                    "sku-list" => [
                        "item" => $itemList,
                    ]
                ]),
                [
                    'delivery-spec' => [
                        'settlement-info' => [
                            'contract-id' => strlen($setting->canadapost_contact_id)==8 ? "00{$setting->canadapost_contact_id}" : $setting->canadapost_contact_id,
                        ]
                    ]
                ]
                // [
                //     "currency" => "USD", // Currency for the declared value
                //     "conversionFromCad" => null, // Optional: Conversion rate if currency is not CAD
                //     "reasonForExport" => "SALE", // Reason for export (e.g., GIFT, SALE, SAMPLE)
                //     "otherReason" => 'OTHER', // Required if reasonForExport is "OTHER"
                //     "skuList" => [ // List of items in the shipment
                //         [
                //             "customsNumberOfUnits" => $order->cards->count(), // Number of units
                //             "customsDescription" => "Cards", // Description of the item
                //             "sku" => "TS123", // SKU or product code
                //             "hsTariffCode" => "6110.20.00.00", // Harmonized System (HS) code
                //             "unitWeight" => $weight, // Weight of each unit in kilograms
                //             "customsValuePerUnit" => $order->net_unit_price, // Value of each unit in the specified currency
                //             "customsUnitOfMeasure" => "PCS", // Unit of measure (e.g., PCS for pieces)
                //             "countryOfOrigin" => "CA" // Country where the item was produced
                //         ],
                //     ]
                // ]
            );
            $order->shipping_api_response = $result->first()->metaData();
            $order->admin_tracking_id = "".$result->first()->trackingNumber();
            $order->admin_tracking_note = "Shipped via Canada Post, Tracking Number: ".$result->first()->trackingNumber().". You can track your order at ".route('user.order.tracking',$order->id);
            $order->save();
            Mail::to($request->email ?? $order->rUser->email)->send(new OrderShipmentMail($order));
            Toastr::success(('Shipped successfully #'.$result->first()->trackingNumber()), 'Success');
        }catch(GuzzleException $e){
            Toastr::error(exceptionMsgConverter($e), 'Error');
        } catch (\Throwable $th) {
            die($th->getMessage());
            \Log::info($th);
            Toastr::error('Failed to make shipment: '.$th->getMessage(), 'Error');
        }
        return back();
        // dd($result);
    }

    public function rate($id,$setRate = false) 
    {

        $test = '';

        $setting = getSetting();
        $order = Order::where('id', $id)->first();

        $customer_number = $setting->canadapost_customer_number;
        $username = $setting->canadapost_username;
        $password = $setting->canadapost_password;
        $mode = $setting->canadapost_mode;

        if($mode == 'live')
        {
            $test = False;
        } else {
            $test = True;
        }
        
        $credentials = new ServiceCanadaPostCredentials($customer_number, $username, $password, $test);
        $canadaPost = new CanadaPostService($credentials);
        $height = 5;
        $width = 5;
        $legnth = 5;
        $weight = $order->cards->count()*0.08;
        $rateArray = collect();

        try {

            $canadaPostRates = $canadaPost->rate(
                new Address(
                    $setting->site_name,                    // First Name
                    '',                                     // Last Name
                    $setting->site_name,                    // Company Name (optional)
                    '9-16228 16TH AVE SURREY BC V4A 1S7',   // Address Line 1
                    '',                                     // Address Line 2 (optional)
                    'SURREY',                               // City
                    'V4A 1S7',                              // Postal Code (Canadian format)
                    'BC',                                   // Province Code (ISO 2)
                    'CA'                                    // Country Code (ISO 2 for Canada)
                ),
                new Address(
                    $order->transaction->shipping_data['shippingName'], // 'First Name'
                    '', // 'Last Name'
                    '',
                    "{$order->transaction->shipping_data['shippingAddress']}", // 'Address line1'
                    '', // 'Address line2'
                    $order->transaction->shipping_data['shippingCity'], // 'city'
                    strtoupper(preg_replace('/\s+/u', '', $order->transaction->shipping_data['shippingZip'])), // 'postalCode'
                    stateToIso2($order->transaction->shipping_data['shippingState'] ?? $order->rUser->defaultAddress->state,true), // 'stateCodeIso2'
                    str($order->transaction->shipping_data['shippingCountry'] ?? $order->rUser->defaultAddress->country)->lower()->contains(['united','states']) ? 'US' : 'CA', // 'countryCodeIso2'
                ),
                new BoxCollection([
                    new BoxMetric($legnth, $width, $height, $weight)
                ])
            );
            foreach ($canadaPostRates as $key => $value) {

                if(str($value->serviceProviderService()->serviceName())->startsWith(["Expedited Parcel"])){

                    $tmpArr = [
                        'serviceCode' => $value->serviceProviderService()->serviceCode(),
                        'serviceName' => $value->serviceProviderService()->serviceName(),
                        'price' => $value->price(),
                        'finalCharge' => (float) number_format(max($value->price() * getSetting()->shipping_cost_maximization, 18), 2),
                        'metaData' => $value->metaData()
                    ];
                    $rateArray->push($tmpArr);
                    if($setRate && $setRate==$value->serviceProviderService()->serviceCode()){
                        $order->shipping_method_service_code = [
                            $value->serviceProviderService()->serviceCode() => $value->serviceProviderService()->serviceName()
                        ];
                        $order->save();
                        session(['checkout.delivery_charge_' . $order->id => $tmpArr]);
                    }else{
                    }
                }

            }
            return [
                'success' => true,
                'data' => $rateArray
            ];
        } catch (\Throwable $th) {
            return [
                'success' => false,
                'message' => exceptionMsgConverter($th),
                'data' => []
            ];
        }
    }

    public function track($order){
        $setting = getSetting();
        $order = Order::where('id', $order)->first();

        $customer_number = $setting->canadapost_customer_number;
        $username = $setting->canadapost_username;
        $password = $setting->canadapost_password;
        $mode = $setting->canadapost_mode;

        if($mode == 'live')
        {
            $test = False;
        } else {
            $test = True;
        }

        $credentials = new ServiceCanadaPostCredentials($customer_number, $username, $password, $test);
        $canadaPost = new CanadaPostService($credentials);
        $data = $canadaPost->track($order);

        return $data;
    }
}
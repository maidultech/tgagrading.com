<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\NamecheapApiController;
use App\Models\OrderDetail;
use App\Models\Transaction;
use Illuminate\Http\Request;

class PaymentProcessingController extends Controller
{

    function process(Request $request, Transaction $transaction){

        $orderItems = $transaction->order->details;

        foreach ($orderItems as $key => $details) {
            if($details->product_type=='domain'){
                return $this->domainRegister($request,$details);
            }
        }

    }
    function domainRegister(Request $request, OrderDetail $orderDetail){
        $namecheap = new NamecheapApiController($request);
        $data = [];
        $user = $orderDetail->order->user;
        // dd($user->country_name);
        $data['DomainName'] = $orderDetail->domain_name;
        $data['Years'] = $orderDetail->duration; // Default value is
        $data['RegistrantFirstName'] = $user->first_name ;
        $data['RegistrantLastName'] = $user->last_name;
        $data['RegistrantAddress1'] = $user->billingAddress->first()->street_address;
        $data['RegistrantCity'] = $user->billingAddress->first()->city;
        $data['RegistrantStateProvince'] = $user->billingAddress->first()->state;
        $data['RegistrantPostalCode'] = $user->billingAddress->first()->postcode;
        $data['RegistrantCountry'] = $user->country_name;
        $data['RegistrantPhone'] = $user->phone_code.'.'.$user->phone;
        $data['RegistrantEmailAddress'] = $user->email;
        $data['RegistrantOrganizationName'] = $user->billingAddress->first()->company_name;
        $namecheap = $namecheap->createDomainCommand($data)->send();
        
        $response = parseXml($namecheap);

        $status_details = (array) (parseXmlResponse($namecheap,'DomainCreateResult')->attributes());

        $orderDetail->namecheap_api_data = (array) $response;
        $orderDetail->status = $response->attributes()['Status'];
        $orderDetail->status_details = $status_details;
        $orderDetail->save();

        return $orderDetail->status;
    }
}

<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\User\UserController;
use App\Models\Address;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Plan;
use App\Models\ServiceLevel;
use App\Models\Transaction;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController
{
    function payment(Request $request){

        $config = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();

        $paymentModeRule = [];

        if($config[0]->config_value=='1'){
            $paymentModeRule[] = 'stripe';
        }
        if($config[1]->config_value=='1'){
            $paymentModeRule[] = 'paypal';
        }

        $request->validate([
            'toc-accept' => 'accepted',
            'shippingName' => 'required|max:255',
            'shippingAddress' => 'required|max:255',
            'shippingCity' => 'required|max:255',
            'shippingState' => 'required|max:255',
            'shippingZip' => 'required|max:255',
            'shippingCountry' => 'required|max:255',
            'shippingPhone' => 'required|max:15',
            'dial_code' => 'required|max:5',
            'payment_mode' => 'required|in:'.implode(',', $paymentModeRule),
        ],[
            'toc-accept.accepted' => 'You have to accept Terms and Condition'
        ]);
        

        $authUser = auth()->user();
        
        $user_id = $authUser->id;

        // adding address if user do no have any address
        $add = Address::where('user_id',$user_id)->first();
        if(!$add){
            $add = new Address();
        }
        $add->user_id = $user_id;
        $add->first_name = $authUser->name;
        $add->last_name = $authUser->last_name;
        $add->street = $request->shippingAddress;
        $add->apt_unit = '';
        $add->city = $request->shippingCity;
        $add->zip_code = $request->shippingZip;
        $add->country = $request->shippingCountry;
        $add->state = $request->shippingCountry;
        $add->country = $request->shippingState;
        $add->phone = $request->shippingPhone;
        $add->dial_code = $request->dial_code;
        $add->save();


        DB::beginTransaction();

        try {
            // if(UserAddress::whereUserId($user_id)->where('type','billing')->exists()){
            //     $address = UserAddress::whereUserId($user_id)->where('type','billing')->first();
            //     $address->company_name = $request->company;
            // }else{
            //     $address = new UserAddress();
            //     $address->user_id = $user_id;
            //     $address->type = 'billing';
            // }
            // $address->company_name = $request->company;
            // $address->street_address = $request->address;
            // $address->street_address2 = $request->address2;
            // $address->city = $request->city;
            // $address->state = $request->state;
            // $address->postcode = $request->postcode;
    
            // $address->save();

            $cartItems = getCartItems();
    
            // order_id
    
            $tmp_order_id = Order::max('order_number') + 1;
    
            $tmp_order_id = $tmp_order_id < 1000 ? 1000 : $tmp_order_id;
    
            $order = new Order;

            // $serviceLevel = ServiceLevel::findOrFail(session('checkout.service_level'));
            $planData = Plan::findOrFail(session('checkout.plan'))
            ;
            $order->order_number = $tmp_order_id;
            $order->item_type = session('checkout.item_type');
            $order->submission_type = session('checkout.submission_type');
            // $order->service_level_id = session('checkout.service_level');
            // $order->service_level_name = $serviceLevel->name;
            // $order->est_day = $serviceLevel->estimated_days;

            $order->est_day = 0;
            $order->status = $order->payment_status = 0;
            $order->user_id = $user_id;
            $order->plan_id = $planData->id;
            $order->plan_details = $planData->toJson();
            $order->note = $cartItems['comments'];
            // $order->discount_percentage = 0;
            // $order->discount = 0;
            // $order->payment_fee = 0;
            // $order->vat = 0;
            
            $order->unit_price = $planData->price;
            $order->total_order_qty = array_sum($cartItems['quantity']);
            // $order->extra_price = $serviceLevel->extra_price;
            // $order->net_unit_price = $planData->price + $serviceLevel->extra_price;
            // $order->total_price = ($planData->price + $serviceLevel->extra_price)*array_sum($cartItems['quantity']);
            $order->extra_price = 0;
            $order->net_unit_price = $planData->price + 0;
            $order->total_price = ($planData->price + 0)*array_sum($cartItems['quantity']);
    
    
            $order->save();

            // set order details
            // `${year} ${brand} ${cardNumber} ${playerName}`

            foreach ($cartItems['year'] as $key => $item) {
                $orderDetails = new OrderDetail();
                $orderDetails->order_id = $order->id;
                $orderDetails->year = $cartItems['year'][$key];
                $orderDetails->brand_name = $cartItems['brand'][$key];
                $orderDetails->brand_id = 1;
                $orderDetails->card = $cartItems['cardNumber'][$key];
                $orderDetails->card_name = $cartItems['playerName'][$key];
                $orderDetails->notes = $cartItems['notes'][$key];
                $orderDetails->qty = $cartItems['quantity'][$key];
                $orderDetails->line_price = $cartItems['quantity'][$key] * $order->net_unit_price;
                $orderDetails->item_name = "{$orderDetails->year} {$orderDetails->brand_name} {$orderDetails->card} {$orderDetails->card_name}";
                $orderDetails->save();
            }
    
    
            // set transaction
            $tmp_trx_id = (int) Transaction::max('transaction_number') + 1;
    
            $tmp_trx_id = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;
    
    
            $transaction = new Transaction;
            $transaction->transaction_number = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;
            $transaction->user_id = $user_id;
            $transaction->amount = session('checkout.total_quantity') * session('checkout.total_unit_price');
            $transaction->pay_date = today();
            $transaction->plan_id = $planData->id;
            $transaction->payment_method = $request->payment_mode;
            $transaction->currency = 'USD';
            $transaction->details = $request->notes;
            $transaction->status = 0;
            $transaction->order_id = $order->id;
            $transaction->shipping_data = $request->except([ '_token']);
            // $transaction->type = 'purchase';

            // $order->payment_method = $request->payment_mode;
            // $order->payment_status = 5;
            // $order->billing_address = $this->formatShippingInfo($request);

            
            $transaction->save();
            
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            toastr()->error('Payment Failed due to internal error');
            return to_route('user.dashboard');

        }

        DB::commit();


        
        session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id]);

        

        if($request->payment_mode=='stripe'){
            $stripe = new StripeController;
            return ($stripe->makePayment($request,$transaction->id));
        }else if($request->payment_mode=='paypal'){
            $paypal = new PayPalController;
            return ($paypal->processTransaction($request,$transaction->id));
        }
    }


    function success(Request $request, $provider, $source = false)
    {
        $trx_id = session('payment.trx_id');
        if($provider=='paypal'){
            $paypal = new PayPalController;
            return $paypal->successTransaction($request,$trx_id);
        }else if($provider=='stripe'){
            return (new StripeController)->syncTransactionStatus();
        }

        toastr()->success('Payment Completed');
        return to_route('user.dashboard');
    }
    
    function cancel(Request $request, $provider, $source = false){

        if(!$source || $source=='payment'){
            try {
                $trnx = Transaction::where('transaction_number',operator: session('payment.trnx_id'))->first();
                if($trnx){
                    $trnx->order->payment_status = 9;
                    $trnx->order->save();
                }
    
            } catch (\Throwable $th) {
                //throw $th;
            }
            clearCartNSession();
            toastr()->warning('Payment Canceled');
        }else{
            toastr()->warning(ucwords($source).' Canceled');
        }
        
        return to_route('user.dashboard');
    }

    
    
}

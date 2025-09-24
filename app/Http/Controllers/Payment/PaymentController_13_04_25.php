<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Payment\PayPalController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\User\UserController;
use App\Mail\OrderMail;
use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Plan;
use App\Models\ServiceLevel;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\UserAddress;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class PaymentController
{
    function payment(Request $request)
    {
        $planData = Plan::findOrFail(session('checkout.plan'));

        // if($planData->type == 'subscription')
        // {
        //     $config = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
        //     $paymentModeRule = [];
        //     if($config[0]->config_value=='1'){
        //         $paymentModeRule[] = 'stripe';
        //     }
        //     if($config[1]->config_value=='1'){
        //         $paymentModeRule[] = 'paypal';
        //     }
        // }

        $rules = [
            'toc-accept' => 'accepted',
            'shippingName' => 'required|max:255',
            'shippingAddress' => 'required|max:255',
            'shippingCity' => 'required|max:255',
            'shippingState' => 'required|max:255',
            'shippingZip' => 'required|max:255',
            'shippingCountry' => 'required|max:255',
            'shippingPhone' => 'required|max:15',
            'dial_code' => 'required|max:5',
        ];
        
        // if ($planData->type == 'subscription') {
        //     $rules['payment_mode'] = 'required|in:' . implode(',', $paymentModeRule);
        // }
        
        $request->validate($rules, [
            'toc-accept.accepted' => 'You have to accept Terms and Conditions.',
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
        $add->state = $request->shippingState;
        $add->country = $request->shippingCountry;
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
            $order->plan_name = $planData->name;
            $order->plan_details = $planData->toJson();
            $order->note = $cartItems['comments'];
            // $order->discount_percentage = 0;
            // $order->discount = 0;
            // $order->payment_fee = 0;
            // $order->vat = 0;
            
            $order->total_order_qty = array_sum($cartItems['quantity']);
            // $order->extra_price = $serviceLevel->extra_price;
            // $order->net_unit_price = $planData->price + $serviceLevel->extra_price;
            // $order->total_price = ($planData->price + $serviceLevel->extra_price)*array_sum($cartItems['quantity']);
            $order->extra_price = 0;  
            if($planData->type == 'subscription')
            {
                $setting = getSetting();
                $total_amount = 0;
                // $gst = $setting->gst_tax*$total_amount/100;
                // $pst = $setting->pst_tax*$total_amount/100;
                $order->extra_price = 0;  

                $order->unit_price = 0;
                $order->net_unit_price = 0;
                $order->total_price = $total_amount + $order->extra_price;
            } else {
                $setting = getSetting();
                $total_amount = ($planData->price + 0)*array_sum($cartItems['quantity']);
                $gst = $setting->gst_tax*$total_amount/100;
                $pst = $setting->pst_tax*$total_amount/100;
                $order->extra_price = $gst + $pst;  

                $order->unit_price = $planData->price;
                $order->net_unit_price = $planData->price + 0;
                $order->total_price = $total_amount + $order->extra_price;
            }
    
            // if(session()->has('checkout.coupon')){

            //     $coupon = Coupon::find(session('checkout.coupon.coupon_id'));
            //     $userRedemptions = Order::where('coupon_id', $coupon->id)
            //     ->where('user_id', Auth::id())->count();

            //         if ($coupon->max_redemptions_per_user !== null && $userRedemptions >= $coupon->max_redemptions_per_user) {
            //             toastr()->error('You have reached the maximum redemption limit for this coupon.');
            //             return back();
            //         }
                    
            //     $order->total_price -= session('checkout.coupon.discount_amount');

            //     $order->coupon_id = session('checkout.coupon.coupon_id');
            //     $order->coupon_discount = session('checkout.coupon.discount_amount');

                
            // }
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
                if($planData->type == 'subscription')
                {
                    $orderDetails->line_price = 0;
                } else {
                    if($authUser->is_subscriber && $authUser->subscription_start < now() && $authUser->subscription_end > now())
                    {
                        $available_card_limit = $authUser->getAvailableCardLimit();
                        
                        if($available_card_limit > 0)
                        {
                            $orderDetails->line_price = 0;
                        } else {
                            $orderDetails->line_price = $cartItems['quantity'][$key] * $order->net_unit_price;
                        }
                    } else {
                        $orderDetails->line_price = $cartItems['quantity'][$key] * $order->net_unit_price;
                    }
                }
                $orderDetails->item_name = "{$orderDetails->year} {$orderDetails->brand_name} {$orderDetails->card} {$orderDetails->card_name}";
                $orderDetails->save();
            }
    
    
            // set transaction
            $tmp_trx_id = (int) Transaction::max('transaction_number') + 1;
    
            $tmp_trx_id = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;
    
    
            $transaction = new Transaction;
            $transaction->transaction_number = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;
            $transaction->user_id = $user_id;
            if($planData->type == 'subscription')
            {
                $transaction->amount = 0;
            } else {

                if($authUser->is_subscriber && $authUser->subscription_start < now() && $authUser->subscription_end > now())
                {
                    $available_card_limit = $authUser->getAvailableCardLimit();
                    
                    if($available_card_limit > 0)
                    {
                        $transaction->amount = 0;
                    } else {
                        $transaction->amount = $order->total_price;
                    }
                } else {
                    $transaction->amount = $order->total_price;
                }
            }
            $transaction->amount = $order->total_price;
            $transaction->pay_date = today();
            $transaction->plan_id = $planData->id;
            $transaction->payment_method = $request->payment_mode ?? null;
            $transaction->currency = 'USD';
            $transaction->details = $request->comments;
            $transaction->status = 0;
            $transaction->order_id = $order->id;
            $transaction->shipping_data = $request->except([ '_token']);
            // $transaction->type = 'purchase';

            // $order->payment_method = $request->payment_mode;
            // $order->payment_status = 5;
            // $order->billing_address = $this->formatShippingInfo($request);

            
            if($authUser->is_subscriber && $authUser->subscription_start < now() && $authUser->subscription_end > now())
            {
                $available_card_limit = $authUser->getAvailableCardLimit();
                
                if($available_card_limit > 0)
                {
                    $subscription_order = Order::where('user_id',$authUser->id)
                        ->where('plan_id', $authUser->current_plan_id)->where('status', 50)->latest()->first();
                    $order->status = 0;
                    $order->payment_status = 1;
                    $order->pay_date = $subscription_order->pay_date ?? now();
                    $order->is_redeemed = 1;
                    
                    $planData = Plan::where('type', 'single')->where('status', 1)->first();
                    $order->plan_id =  $planData->id;
                    $setting = getSetting();
                    $total_amount = ($planData->price + 0)*array_sum($cartItems['quantity']);
                    $gst = $setting->gst_tax*$total_amount/100;
                    $pst = $setting->pst_tax*$total_amount/100;
                    $order->extra_price = $gst + $pst;  
                    $order->unit_price = $planData->price;
                    $order->net_unit_price = $planData->price + 0;
                    $order->total_price = $total_amount + $order->extra_price;

                    $transaction->status = 1;

                    $order->save();
                    $currentSubscription = $authUser->getCurrentSubscription();

                    if ($currentSubscription) {
                        $currentSubscription->order_card_peryear += array_sum($cartItems['quantity']);
                        $currentSubscription->save();
                    }
                }
            }
            
            $transaction->save();

        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            toastr()->error('Payment Failed due to internal error');
            return to_route('user.dashboard');

        }

        DB::commit();


        
        session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id]);

        
        // if($planData->type == 'subscription')
        // {
        //     if($request->payment_mode=='stripe'){
        //         $stripe = new StripeController;
        //         return ($stripe->makePayment($request,$transaction->id, $planData->price));
        //     }else if($request->payment_mode=='paypal'){
        //         $paypal = new PayPalController;
        //         return ($paypal->processTransaction($request,$transaction->id, $planData->price));
        //     }
        // } else {
            $order->rUser->cart_json = null;
            $order->rUser->save();
            clearCartNSession();
            $setting = Setting::first();
            $body = "A new order has been placed by ".$order->rUser?->name.' '.$order->rUser?->last_name.". Below are the order details";
            $data = [
                'greeting' => 'Hi Admin,',
                'subject' => $body,
                'body' => $body,
                'customer' => $order->rUser?->name.' '.$order->rUser?->last_name,
                'order_date' => $order->created_at->format('d M Y, H:i:s'),
                'order_number' => '#'.$order->order_number,
                'total_items' => $order->total_order_qty,
                'total_price' => getDefaultCurrencySymbol().number_format($order->total_price, 2),
                'site_name' => $setting->site_name ?? config('app.name'),
                'site_url' => url('/'),
                'from' => [$setting->site_name.' Support',$setting->support_email],
                'footer' => 1,
            ];

            $bodyUser = "Thank you for your order!<br>Please package your order safely and have it shipped to our TGA Office for processing. <br><br>" .
            nl2br($setting->office_address) . "<br><br>We look forward to receiving your order!";
        
            $dataUser = [
                'greeting' => 'Hello,' . ' ' . $order->rUser?->name.' '.$order->rUser?->last_name,
                'body' => $bodyUser,
                'subject' => "You have a new Message from ".$setting->site_name.": Order Created",
                'from' => [$setting->site_name.' Support',$setting->support_email],
                'order_date' => $order->created_at->format('d M Y, H:i:s'),
                'order_number' => '#'.$order->order_number,
                'total_items' => $order->total_order_qty,
                'total_price' => getDefaultCurrencySymbol().number_format($order->total_price, 2),
                'site_name' => $setting->site_name ?? config('app.name'),
                'site_url' => url('/'),
                'footer' => 1,
            ];
            try {
                Mail::to($setting->email)->send(new OrderMail($data));
                Mail::to(auth()->user()->email)->send(new OrderMail($dataUser));
            } catch (\Exception $e) {
                Log::alert('Order mail not sent. Error: ' . $e->getMessage());
            }

            return to_route('checkout.confirmation',[
                'id' => $order->plan_id, 'order_number' => $order->order_number
            ]);
        // }
    }

    function planPayment(Request $request)
    {

        $planData = Plan::findOrFail($request->plan_id);

        $config = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
        $paymentModeRule = [];
        if($config[0]->config_value=='1'){
            $paymentModeRule[] = 'stripe';
        }
        if($config[1]->config_value=='1'){
            $paymentModeRule[] = 'paypal';
        }

        $rules = [
            'toc-accept' => 'accepted',
            'shippingName' => 'required|max:255',
            'shippingAddress' => 'required|max:255',
            'shippingCity' => 'required|max:255',
            'shippingState' => 'required|max:255',
            'shippingZip' => 'required|max:255',
            'shippingCountry' => 'required|max:255',
            'shippingPhone' => 'required|max:15',
            'dial_code' => 'required|max:5',
        ];
        
        if ($planData->type == 'subscription') {
            $rules['payment_mode'] = 'required|in:' . implode(',', $paymentModeRule);
        }
        
        $request->validate($rules, [
            'toc-accept.accepted' => 'You have to accept Terms and Conditions.',
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
        // $add->state = $request->shippingCountry;
        $add->state = $request->shippingState;
        $add->phone = $request->shippingPhone;
        $add->dial_code = $request->dial_code;
        $add->save();


        DB::beginTransaction();

        try {

    
            $tmp_order_id = Order::max('order_number') + 1;
            $tmp_order_id = $tmp_order_id < 1000 ? 1000 : $tmp_order_id;
    
            $order = new Order;
            $order->order_number = $tmp_order_id;
            $order->item_type = 0;
            $order->submission_type = 0;
            $order->est_day = 0;
            $order->status = $order->payment_status = 0;
            $order->user_id = $user_id;
            $order->plan_id = $planData->id;
            $order->plan_name = $planData->name;
            $order->plan_details = $planData->toJson();
            $order->note = null;
            $order->total_order_qty = 0;
            

            $setting = getSetting();
            $total_amount = $planData->price;
            $gst = $setting->gst_tax*$total_amount/100;
            $pst = $setting->pst_tax*$total_amount/100;
            $order->extra_price = $gst + $pst;
            $order->unit_price = 0;
            $order->net_unit_price = 0;
            $order->total_price = $total_amount + $order->extra_price;

            if(session()->has('checkout.plan.coupon')){
                // $order->total_price -= session('checkout.plan.coupon.discount_amount');
                $coupon = Coupon::find(session('checkout.plan.coupon.coupon_id'));
                $userRedemptions = Order::where('coupon_id', $coupon->id)
                ->where('user_id', Auth::id())->count();

                    if ($coupon->max_redemptions_per_user !== null && $userRedemptions >= $coupon->max_redemptions_per_user) {
                        toastr()->error('You have reached the maximum redemption limit for this coupon.');
                        return back();
                    }
                $order->coupon_id = session('checkout.plan.coupon.coupon_id');
                $order->coupon_discount = session('checkout.plan.coupon.discount_amount'); 
            }

            $order->save();

            session('payment.order_id',$order->id); 
    
            // set transaction
            $tmp_trx_id = (int) Transaction::max('transaction_number') + 1;
            $tmp_trx_id = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;

            $transaction = new Transaction;
            $transaction->transaction_number = $tmp_trx_id < 1000 ? 1000 : $tmp_trx_id;
            $transaction->user_id = $user_id;

            // if(session()->has('checkout.coupon')){
            //     $transaction->amount = $order->total_price - $order->coupon_discount;
            // } else {
            //     $transaction->amount = $order->total_price;
            // }
            
            $transaction->amount = $request->order_total_price;
            $transaction->pay_date = today();
            $transaction->plan_id = $planData->id;
            $transaction->payment_method = $request->payment_mode;
            $transaction->currency = 'USD';
            $transaction->details = $request->comments;
            $transaction->status = 0;
            $transaction->order_id = $order->id;
            $transaction->shipping_data = $request->except([ '_token']);
            $transaction->save();

            $used_wallet = $request->use_wallet ?? 0;
            $used_wallet_balance = $request->used_wallet_balance ?? 0;

            if($request->order_total_price == 0) {

                $transaction->status = 1;
                $transaction->amount =  $request->old_total_price;
                $transaction->save();
                $order->payment_status = 1;
                $order->pay_date = now();
                $order->status = 50;
                
                $order->rUser->cart_json = null;

                $authUser = auth()->user();
                $authUser->is_subscriber = 1;
                $authUser->current_plan_id = $planData->id;
                $authUser->current_plan_name = $planData->name;
                $authUser->subscription_start = now();
                $authUser->subscription_end = now()->addYears($planData->subscription_year);
                $authUser->subscription_card_peryear = $planData->subscription_peryear_card;
                $authUser->save();

                if($order->coupon_id) {
                    $coupon = $order->coupon;
                    $coupon->increment('total_uses');
                }

                $currentYearStart = now();
                for ($i = 0; $i < $planData->subscription_year; $i++) {
                    $subscription = new UserSubscription();
                    $subscription->user_id = $authUser->id;
                    $subscription->subscription_card_peryear = $planData->subscription_peryear_card;
                    if ($i == 0) {
                        $subscription->order_card_peryear = $order->total_order_qty;
                    } else {
                        $subscription->order_card_peryear = 0;
                    }
                    $subscription->year_start = $currentYearStart;
                    $subscription->year_end = $currentYearStart->copy()->addYear();
                    $subscription->save();
                    $currentYearStart = $subscription->year_end;
                }
                $order->save();
                
                $wallet_discount = $request->old_total_price;
                
                if ($used_wallet == 1 && $wallet_discount > 0) {
                    $order->rUser->wallet_balance = $order->rUser->wallet_balance - ($wallet_discount ?? 0);
                    $order->used_wallet = 1;
                    $order->used_wallet_amount = $used_wallet_balance;
                    $order->save();

                    debitWalletBalance($transaction->user_id, $order->used_wallet_amount, 'Order Payment #'.$order->order_number);
                }
                
                $order->rUser->save();

                clearCartNSession();
                toastr()->success('Payment Completed');
                DB::commit();

                return to_route('checkout.plan.confirmation',[
                    'id' => $order->plan_id, 'order_number' => $order->order_number
                 ]);
            } else{
                if($used_wallet == 1){
                    $order->used_wallet_amount = $used_wallet_balance ;
                    $order->save();
                }
            }
            
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            toastr()->error('Payment Failed due to internal error');
            return to_route('user.dashboard');

        }

        DB::commit();

        session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id, 'checkout.plan' => $planData->id, 'used_wallet' => $used_wallet]);

        if($request->payment_mode=='stripe'){
            $stripe = new StripeController;
            return ($stripe->makePayment($request,$transaction->id, $planData->price));
        }else if($request->payment_mode=='paypal'){
            $paypal = new PayPalController;
            return ($paypal->processTransaction($request,$transaction->id, $planData->price));
        }
        
    }
    
    function orderPayment(Request $request)
    {

        $config = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
        $paymentModeRule = [];
        if($config[0]->config_value=='1'){
            $paymentModeRule[] = 'stripe';
        }
        if($config[1]->config_value=='1'){
            $paymentModeRule[] = 'paypal';
        }

        $rules = [
            'toc-accept' => 'accepted',
            'shippingName' => 'required|max:255',
            'shippingAddress' => 'required|max:255',
            'shippingCity' => 'required|max:255',
            'shippingState' => 'required|max:255',
            'shippingZip' => 'required|max:255',
            'shippingCountry' => 'required|max:255',
            'shippingPhone' => 'required|max:15',
            'dial_code' => 'required|max:5',
        ];
        
        $rules['payment_mode'] = 'required|in:' . implode(',', $paymentModeRule);
        
        $request->validate($rules, [
            'toc-accept.accepted' => 'You have to accept Terms and Conditions.',
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
        // $add->state = $request->shippingCountry;
        $add->state = $request->shippingState;
        $add->phone = $request->shippingPhone;
        $add->dial_code = $request->dial_code;
        $add->save();


        DB::beginTransaction();

        try {
            $order = Order::findOrFail($request->order_id);

            // if(session()->has('checkout.coupon')){
            //     $order->total_price -= session('checkout.coupon.discount_amount');

            //     $order->coupon_id = session('checkout.coupon.coupon_id');
            //     $order->coupon_discount = session('checkout.coupon.discount_amount');
                
            // }
            // $order->save();

            $used_wallet = $request->use_wallet ?? 0;
            $used_wallet_balance = $request->used_wallet_balance ?? 0;
  
            // set transaction
            $transaction = Transaction::find($order->transaction->id);
            $transaction->amount = $request->order_total_price;
            $transaction->pay_date = today();
            $transaction->payment_method = $request->payment_mode;
            $transaction->shipping_data = $request->except([ '_token']);
            $transaction->currency = 'USD';
            $transaction->status = 0;
            $transaction->save();

            if($request->order_total_price == 0) {

                $transaction->status = 1;
                $transaction->save();
                $order->payment_status = 1;
                $order->pay_date = now();
                $order->save();
                
                $order->rUser->cart_json = null;
                
                $wallet_discountable = $request->old_total_price;
                if ($used_wallet == 1 && $wallet_discountable > 0) {
                    $order->rUser->wallet_balance = $order->rUser->wallet_balance - $used_wallet_balance ?? 0;
                    $order->used_wallet = 1;
                    $order->used_wallet_amount = $used_wallet_balance;
                    $order->save();

                    debitWalletBalance($transaction->user_id, $order->used_wallet_amount, 'Order Payment #'.$order->order_number);
                }
                $order->rUser->save();

                clearCartNSession();
                Session::forget('is_order_payment');
                toastr()->success('Payment Completed');

                DB::commit();
                session()->flash('msg');
                return redirect()->route('user.order.payment.invoice', $order->id);
            }else{
                if($used_wallet == 1){
                    $order->used_wallet_amount = $used_wallet_balance ;
                    $transaction->save();
                }
            }
            $order->has_insurance = $request->has_insurance ?? 0;
            $order->insurance_value = $request->insurance_value ?? 0;
            $order->insurance_amount = ceil($request->insurance_value/ 100 ) * 1.99;
            $order->save();

        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            toastr()->error('Payment Failed due to internal error');
            return to_route('user.dashboard');
        }

        DB::commit();

        
        session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id, 'used_wallet' => $used_wallet]);

        if($request->payment_mode=='stripe'){
            $stripe = new StripeController;
            return ($stripe->makeOrderPayment($request,$transaction->id));
        }else if($request->payment_mode=='paypal'){
            $paypal = new PayPalController;
            return ($paypal->processOrderTransaction($request,$transaction->id));
        }

    }

    function success(Request $request, $provider, $source = false)
    {
        $trx_id = session('payment.trx_id');

        $trnx = Transaction::where('id',$trx_id)->first();
        if($trnx->order->used_wallet_amount > 0){
            debitWalletBalance($trnx->user_id, $trnx->order->used_wallet_amount, 'Order Payment #'.$trnx->order->order_number);
        }
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
                $trnx = Transaction::where('transaction_number',session('payment.trnx_id'))->first();
                if($trnx){
                    $trnx->order->payment_status = 9;
                    $trnx->order->save();
                    if ($trnx->user) {
                        $trnx->user->cart_json = null;
                        $trnx->user->save();
                    }
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

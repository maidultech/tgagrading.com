<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\User\UserController;
use App\Mail\OrderMail;
use App\Models\Order;
use App\Models\OrderCard;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Toastr;

class StripeController extends Controller
{
    protected $stripe;
    function __construct()
    {
        $this->stripe = new \Stripe\StripeClient(DB::table('config')->where('config_key', 'stripe_secret')->first('config_value')->config_value);
        // $this->stripe = new \Stripe\StripeClient('sk_test_51PI9u6Gij7bPruVhdRYCai8NYURHNhA8274cVxsFr8CAxN9ibXNVmaeSUVGgNY7k2p5niZYCAvpHW1pu3yVOlmtD003tWcxh2p');
    }
    private $trx_id;
    function makePayment( Request $request, $trx_id, $price = null)
    {
        // sk_test_51PI9u6Gij7bPruVhdRYCai8NYURHNhA8274cVxsFr8CAxN9ibXNVmaeSUVGgNY7k2p5niZYCAvpHW1pu3yVOlmtD003tWcxh2p
        $stripe = $this->stripe;
        $this->trx_id = $trx_id;
        $user = $request->user();
        $setting = getSetting();
        
        if ( !$user->stripe_id ) {
            $stripUser = $stripe->customers->create([
                "address" => $user->address,
                "email" => $user->email,
                "name" => $user->name . ' ' . $user->last_name,
                "phone" => $user->phone_code . $user->phone,
            ]);

            $user->stripe_id = $stripUser->id;
            $user->save();
        }

        DB::beginTransaction();
        
        $priceList = [];
        
        if($price == null)
        {
            $cartProducts = getCartItems();
            foreach ( $cartProducts['year'] as $key => $cartItem ) {

                $productName = $cartProducts['year'][$key] . ' ' .
                    $cartProducts['brand'][$key] . ' ' .
                    $cartProducts['cardNumber'][$key] . ' ' .
                    $cartProducts['playerName'][$key];

                $cartProductPrice = session('checkout.total_unit_price');


                $currency = strtolower('usd');

                $product = $stripe->products->create([
                    'name' => $productName,
                ]);

                $price = $stripe->prices->create([
                    'currency' => $currency,
                    'unit_amount' => $cartProductPrice * 100,
                    // 'recurring' => ['interval' => 'month'],
                    'product' => $product->id,
                ]);

                $priceList[] = [
                    'price' => $price->id,
                    'quantity' => $cartProducts['quantity'][$key],
                ];
            }
        } else {
            $planData = Plan::findOrFail(session('checkout.plan'));
            
            // $productDetails = '';
            // foreach ($cartProducts['year'] as $key => $cartItem) {
            //     $productName = $cartProducts['year'][$key] . ' ' .
            //         $cartProducts['brand'][$key] . ' ' .
            //         $cartProducts['cardNumber'][$key] . ' ' .
            //         $cartProducts['playerName'][$key];
        
            //     $productDetails .= $productName . " \nQty: " . $cartProducts['quantity'][$key];

            //     if ($key < count($cartProducts['year']) - 1) {
            //         $productDetails .= ", ";
            //     }
            // }
            
            $gst = $setting->gst_tax*$price/100;
            $pst = $setting->pst_tax*$price/100;
            $total_tax = $gst + $pst;
        
            $priceList = [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $planData->name,
                        ],
                        'unit_amount' => ($price+$total_tax) * 100,
                    ],
                    'quantity' => 1,
                ],
            ];

    
            // if($total_tax > 0) {
            //     $product = $stripe->products->create([
            //         'name' => 'Tax',
            //         'description' => "GST ({$setting->gst_tax}%) : $" . number_format($gst, 2) . ",\n\nPST ({$setting->pst_tax}%) : $" . number_format($pst, 2),
            //     ]);
        
            //     $price = $stripe->prices->create([
            //         'currency' => 'usd',
            //         'unit_amount' => ($gst + $pst) * 100,
            //         'product' => $product->id,
            //     ]);
        
            //     $priceList[] = [
            //         'price' => $price->id,
            //         'quantity' => 1,
            //     ];
            // }            

        }
        $order = Order::find(session('payment.order_id'));

        $stripeApiData = [
            'success_url' => route('payment.success', ['provider' => 'stripe']),
            'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
            'customer' => $user->stripe_id,
            // 'customer_email' => $user->email,
            'line_items' => $priceList,
            'mode' => 'payment',
            'client_reference_id' => $trx_id,
        ];
        if($order->coupon_id) {

            $coupon = $order->coupon;

            $product = $stripe->products->create([
                'name' => "Discount ($coupon->discount_code)",
            ]);

            $couponData = [
                'amount_off' => $order->coupon_discount * 100,
                'duration' => 'once',
                'currency' => 'usd',
                'name' => "Discount ($coupon->discount_code)",
            ];

            $stripeCoupon = $stripe->coupons->create($couponData);
    
            $stripeApiData['discounts'] = [
                [
                    'coupon' => $stripeCoupon->id,
                ],
            ];
        }
        
        

        $payment = $stripe->checkout->sessions->create($stripeApiData);

        
        // session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id]);

        $trnx = Transaction::find(session('payment.trx_id'));
        
        $trnx->transaction_id = $payment->id;
        $trnx->status = 0;
        $trnx->save();
        
        $order->payment_provider_id = $payment->id;
        $order->save();


        if ( $payment && isset($payment['payment_status']) && isset($payment['url']) ) {
            return redirect()->away($payment['url']);
        } else {
            toastr()->error('Payment Failed');
            return back();
        }


    }

    function makeOrderPayment( Request $request, $trx_id)
    {
        // sk_test_51PI9u6Gij7bPruVhdRYCai8NYURHNhA8274cVxsFr8CAxN9ibXNVmaeSUVGgNY7k2p5niZYCAvpHW1pu3yVOlmtD003tWcxh2p
        $stripe = $this->stripe;
        $this->trx_id = $trx_id;
        $user = $request->user();
        $setting = getSetting();

        if ( !$user->stripe_id ) {
            $stripUser = $stripe->customers->create([
                "address" => $user->address,
                "email" => $user->email,
                "name" => $user->name . ' ' . $user->last_name,
                "phone" => $user->phone_code . $user->phone,
            ]);

            $user->stripe_id = $stripUser->id;
            $user->save();
        }

        $order = Order::find(session('payment.order_id'));
        $priceList = [];
        $total_price = 0;

        foreach ($order->details as $key => $item) {
            // $cardCount = OrderCard::where('order_details_id', $item->id)->where('final_grading', '>', 0)->count();
            $cardCount = OrderCard::where('order_details_id', $item->id)
            ->where(function ($query) {
                $query->where('final_grading', '>', 0)
                      ->orWhere('final_grading', '=', 'A');
            })
            ->count();
            if ($cardCount > 0) {
                $productName = $item->item_name;
                $cartProductPrice = $order->net_unit_price;
                $currency = strtolower('usd');
        
                $product = $stripe->products->create([
                    'name' => $productName,
                ]);
        
                $price = $stripe->prices->create([
                    'currency' => $currency,
                    'unit_amount' => $cartProductPrice * 100,
                    'product' => $product->id,
                ]);
        
                $priceList[] = [
                    'price' => $price->id,
                    'quantity' => $cardCount,
                ];
                $total_price += ($cartProductPrice*$cardCount);
            }
        }
        $gst = $setting->gst_tax*$total_price/100;
        $pst = $setting->pst_tax*$total_price/100;
        $total_tax = $gst + $pst;
        $total_price += $total_tax;

        if($total_tax > 0) {    
            // $product = $stripe->products->create([
            //     'name' => "Tax",
            //     'description' => "GST ({$setting->gst_tax}%) : $" . number_format($gst, 2) . ",\n\nPST ({$setting->pst_tax}%) : $" . number_format($pst, 2),
            // ]);
    
            // $price = $stripe->prices->create([
            //     'currency' => $currency,
            //     'unit_amount' => ($gst + $pst) * 100,
            //     'product' => $product->id,
            // ]);
    
            // $priceList[] = [
            //     'price' => $price->id,
            //     'quantity' => 1,
            // ];
            // $product = $stripe->products->create([
            //     'name' => "GST ({$setting->gst_tax}%)",
            // ]);
    
            // $price = $stripe->prices->create([
            //     'currency' => $currency,
            //     'unit_amount' => ($gst) * 100,
            //     'product' => $product->id,
            // ]);
    
            // $priceList[] = [
            //     'price' => $price->id,
            //     'quantity' => 1,
            // ];

            // // pst
            // $product = $stripe->products->create([
            //     'name' => "PST ({$setting->pst_tax}%)",
            // ]);

            // $price = $stripe->prices->create([
            //     'currency' => $currency,
            //     'unit_amount' => ($pst) * 100,
            //     'product' => $product->id,
            // ]);
    
            // $priceList[] = [
            //     'price' => $price->id,
            //     'quantity' => 1,
            // ];


            $stripeApiData = [
                'success_url' => route('payment.success', ['provider' => 'stripe']),
                'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
                'customer' => $user->stripe_id,
                // 'customer_email' => $user->email,
                'line_items' => $priceList,
                'mode' => 'payment',
                'client_reference_id' => $trx_id,
            ];
            
        }

        // discount

        if($order->coupon_id) {

            $coupon = $order->coupon;

            // $product = $stripe->products->create([
            //     'name' => "Discount ($coupon->discount_code)",
            // ]);

            $couponData = [
                'amount_off' => $order->coupon_discount * 100,
                'duration' => 'once',
                'currency' => $currency,
                'name' => "Discount ($coupon->discount_code)",
            ];

            $stripeCoupon = $stripe->coupons->create($couponData);
    
            $stripeApiData['discounts'] = [
                [
                    'coupon' => $stripeCoupon->id,
                ],
            ];

            $coupon_discount = $order->coupon_discount;
        }else{
            $coupon_discount = 0;
        }

        // wallet
        
        $userWalletBalance = auth()->user()->wallet_balance;

        $chargeAmount = $total_price + $total_tax - $coupon_discount;

        if($order->used_wallet && $userWalletBalance > 0){

            if($chargeAmount > $userWalletBalance){
                // $product = $stripe->products->create([
                //     'name' => "Wallet (".getDefaultCurrencySymbol()." $userWalletBalance)",
                // ]);
    
                $couponData = [
                    'amount_off' => $userWalletBalance * 100,
                    'duration' => 'once',
                    'currency' => $currency,
                    'name' => "Wallet (".getDefaultCurrencySymbol()." $userWalletBalance)",
                ];
    
                $stripeCoupon = $stripe->coupons->create($couponData);
        
                $stripeApiData['discounts'] = [
                    [
                        'coupon' => $stripeCoupon->id,
                    ],
                ];

                debitWalletBalance(auth()->id(), -$userWalletBalance,"Order Payment #".$order->order_number);
            }else{
                debitWalletBalance(auth()->id(), -$chargeAmount,"Order Payment #".$order->order_number);

                $this->syncTransactionStatus(true);

                $trnx = Transaction::find(session('payment.trx_id'));
        
                $trnx->transaction_id = "wallet_".time();
                $trnx->status = 0;
                $trnx->save();

                $order->payment_provider_id = "wallet_".time();
                $order->save();

                DB::commit();

                Toastr::success('Payment Completed');
                return redirect()->route('user.dashboard');
            }

            // $product = $stripe->products->create([
            //     'name' => "Wallet ($coupon->discount_code)",
            // ]);

            // $couponData = [
            //     'amount_off' => $order->coupon_discount * 100,
            //     'duration' => 'once',
            //     'currency' => $currency,
            //     'name' => "Wallet ($coupon->discount_code)",
            // ];

            // $stripeCoupon = $stripe->coupons->create($couponData);
    
            // $stripeApiData['discounts'] = [
            //     [
            //         'coupon' => $stripeCoupon->id,
            //     ],
            // ];
        }
        
        $payment = $stripe->checkout->sessions->create($stripeApiData);

        
        // session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id]);

        $trnx = Transaction::find(session('payment.trx_id'));
        
        $trnx->transaction_id = $payment->id;
        $trnx->status = 0;
        $trnx->save();

        $order->payment_provider_id = $payment->id;
        $order->save();

        if ( $payment && isset($payment['payment_status']) && isset($payment['url']) ) {
            Session::put('is_order_payment', 1);
            DB::commit();
            return redirect()->away($payment['url']);
        } else {
            DB::rollBack();
            toastr()->error('Payment Failed');
            return back();
        }


    }

    function syncTransactionStatus($isWallet = false)
    {
        $trnx = Transaction::find( session('payment.trx_id'));
        
        if ( $trnx ) {

            $trnx = $trnx->load('order');
            $stripe_id = $trnx->transaction_id;
            $stripe = $this->stripe;

            if(!$isWallet){
                $stripeData = $stripe->checkout->sessions->retrieve($stripe_id);
            }

            if ( $isWallet || $stripeData?->payment_status == 'paid' ) {

                $trnx->status = 1;
                $trnx->save();

                $order = $trnx->order;
                // $order->status = 5;
                $order->payment_status = 1;
                $order->save();
                
                $planData = Plan::find(session('checkout.plan'));
                if (isset($planData) && $planData->type == 'subscription') {
                    $authUser = auth()->user();
                    $authUser->is_subscriber = 1;
                    $authUser->current_plan_id = $planData->id;
                    $authUser->current_plan_name = $planData->name;
                    $authUser->subscription_start = now();
                    $authUser->subscription_end = now()->addYears($planData->subscription_year);
                    $authUser->subscription_card_peryear = $planData->subscription_peryear_card;
                    $authUser->save();

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
                    $order->status = 50;
                    $order->save();

                    toastr()->success('Payment Completed');
                    $order->rUser->cart_json = null;
                    $order->rUser->save();
                    clearCartNSession();

                    return to_route('checkout.plan.confirmation',[
                        'id' => $order->plan_id, 'order_number' => $order->order_number
                     ]);
                }

                toastr()->success('Payment Completed');
                $order->rUser->cart_json = null;
                $order->rUser->save();
                clearCartNSession();

                $isOrderPayment = Session::get('is_order_payment', 0);
                if ($isOrderPayment) {
                    Session::forget('is_order_payment');
                    return redirect()->route('user.order.payment.invoice', $order->id);
                }
                $setting = Setting::first();
                $body = "A new order has been placed by ".$order->rUser?->name.' '.$order->rUser?->last_name.". Below are the order details";
                $data = [
                    'greeting' => 'Hi Admin,',
                    'body' => $body,
                    'customer' => $order->rUser?->name.' '.$order->rUser?->last_name,
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
                } catch (\Exception $e) {
                    Log::alert('Order mail not sent. Error: ' . $e->getMessage());
                }
                
                return to_route('checkout.confirmation',[
                    'id' => $order->plan_id, 'order_number' => $order->order_number
                 ]);
            } else {
                toastr()->error('This order is Not paid yet');
            }
        } else {
            toastr()->error('Order Not Found');
        }

        return to_route('user.dashboard');
    }

}

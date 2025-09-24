<?php

namespace App\Http\Controllers\Payment;

use App\Mail\OrderMail;
use App\Models\Order;
use App\Models\OrderCard;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSubscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController_09_04_25
{

    public function __construct()
    {
        $config = DB::table('config')->where('config_key', 'like', '%paypal%')->get();
        $mode = $config[0]->config_value;
        $paypal_client_id = $config[1]->config_value;
        $paypal_secret = $config[2]->config_value;
        config()->set('paypal.mode', $mode);
        if ( $mode == 'sandbox' ) {
            config()->set('paypal.sandbox.client_id', $paypal_client_id);
            config()->set('paypal.sandbox.client_secret', $paypal_secret);
        } else {
            config()->set('paypal.live.client_id', $paypal_client_id);
            config()->set('paypal.live.client_secret', $paypal_secret);
        }
    }


    /**
     * create transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function createTransaction()
    {
        return view('transaction');
    }
    /**
     * process transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function processTransaction( Request $request , $trx_id, $price = null)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $setting = getSetting();

            $priceList = [];
            $grand_total = 0;
            $totalItemValue = 0;

            $userWalletBalance = auth()->user()->wallet_balance;
            $used_wallet = session('used_wallet');
            $walletDiscount = 0;
            $totalDiscountAmount = 0;

            if ($used_wallet == 1 && $userWalletBalance > 0) {
                $walletDiscount = $userWalletBalance;
                $price = $price - $walletDiscount;
            }
            session(['wallet_discount' => $walletDiscount]);

            if($price == null)
            {
                $cartProducts = getCartItems();
                foreach ( $cartProducts['year'] as $key => $cartItem ) {
                    array_push($priceList, [
                        "amount" => [
                            "currency_code" => 'CAD',
                            "value" => $cartProducts['quantity'][$key] * session('checkout.total_unit_price'),
                        ],
                        "reference_id" => $trx_id."_{$key}",
                    ]);
                }
            } else {
                $order = Transaction::find($trx_id)->order;

                $coupon_discount = 0;
                $balance = 0;

                if ($order->coupon_id) {
                    $coupon_discount = $order->coupon_discount;
                }
                // if ($used_wallet == 1 && $userWalletBalance > 0) {
                //     $balance  = $userWalletBalance;
                // }

                $totalDiscountAmount = $coupon_discount + $balance;
                $total_amout = $price - $totalDiscountAmount ?? 0;

        
                $gst = $setting->gst_tax*$price/100;
                $pst = $setting->pst_tax*$price/100;
                $total_tax = $gst + $pst;
                
                $grand_total = $total_amout + $total_tax;
                $totalItemValue = $price + $total_tax;

                array_push($priceList, [
                    "amount" => [
                        "currency_code" => 'CAD',
                        "value" => $grand_total, 
                        "breakdown" => [
                                    "item_total" => [
                                        "currency_code" => 'CAD',
                                        "value" => $totalItemValue,
                                    ],
                                    "discount" => [
                                        "currency_code" => 'CAD',
                                        "value" => $totalDiscountAmount ?? 0,
                                    ],
                                ],
                    ],
                    "reference_id" => $trx_id."pay_101",

                ]);
            }


            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('payment.success', ['provider' => 'paypal']),
                    "cancel_url" => route('payment.cancel', ['provider' => 'paypal']),
                ],
                "purchase_units" => $priceList,
            ]);
            

            if ( isset($response['id']) && $response['id'] != null ) {
                // redirect to approve href
                foreach ( $response['links'] as $links ) {
                    if ( $links['rel'] == 'approve' ) {

                        $trnx = Transaction::find($trx_id);
            
                        $trnx->transaction_id = $response['id'];
                        $trnx->status = 0;
                        $trnx->save();
                        
                        $order = Order::find(session('payment.order_id'));
                        $order->payment_provider_id = $response['id'];
                        $order->save();
                        
                        return redirect()->away($links['href']);
                    }
                }
                Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
                Log::info("if",$response, config('paypal') );
                return back();
            } else {
                Log::info("else",$response, config('paypal') );
                Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
                return back();
            }

        } catch (\Exception $e) {
            Log::error("PayPal Transaction Error: " . $e->getMessage(), [
                'trx_id' => $trx_id,
                'request' => $request->all()
            ]);
    
            Toastr::error('Payment Failed: ' . $e->getMessage(), 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }

    }

    public function processOrderTransaction( Request $request , $trx_id)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $setting = getSetting();

            $order = Order::find(session('payment.order_id'));
            $priceList = [];
            $total_price = 0;

            $userWalletBalance = auth()->user()->wallet_balance;
            $used_wallet = session('used_wallet');
            $walletDiscount = 0;
            $totalDiscountAmount = 0;

            $totalValue = 0;
            $totalItemValue = 0;
            $totalDiscountValue = 0;

            if ($used_wallet == 1 && $userWalletBalance > 0) {
                $walletDiscount = $userWalletBalance;
            }
            session(['wallet_discount' => $walletDiscount]);

            foreach ($order->details as $key => $item) {
                $cardCount = OrderCard::where('order_details_id', $item->id)
                    // ->where(function ($query) {
                    //     $query->where('final_grading', '>', 0)
                    //         ->orWhere('final_grading', '=', 'A');
                    // })
                    ->count();

                if ($cardCount > 0) {
                    $perItemDiscount = 0;
                    $perItemWalletDiscount = 0;

                    if ($order->coupon_id) {
                        $perItemDiscount = $order->coupon_discount / count($order->details);
                    }
                    // if ($used_wallet == 1 && $userWalletBalance > 0) {
                    //     $perItemWalletDiscount = $userWalletBalance / count($order->details);
                    // }

                    $totalDiscountAmount = $perItemDiscount + $perItemWalletDiscount;

                    $itemTotalValue = $cardCount * $order->net_unit_price;
                    $itemDiscountValue = $totalDiscountAmount;
                    $itemValue = $itemTotalValue - $itemDiscountValue;

                    $totalValue += $itemValue;
                    $totalItemValue += $itemTotalValue;
                    $totalDiscountValue += $itemDiscountValue;

                    $total_price += $itemTotalValue;
                }
            }

            if($walletDiscount > 0)
            {
                $total_price = $total_price + $order->shipping_charge - $walletDiscount;
                $totalValue = $totalValue - $walletDiscount;
                $totalItemValue = $totalItemValue - $walletDiscount;
            }

            $gst = $setting->gst_tax*($total_price - ($order->coupon_discount??0))/100;
            $pst = $setting->pst_tax*($total_price - ($order->coupon_discount??0))/100;
            $total_tax = $gst + $pst - ($order->coupon_discount??0);

            $totalValue = $totalValue + $total_tax + $order->shipping_charge;
            $totalItemValue = $totalItemValue + $total_tax + $order->shipping_charge;
            
            if($order->has_insurance){
                $totalValue = $totalValue + $order->insurance_amount;
                $totalItemValue = $totalItemValue + $order->insurance_amount;
            }

            array_push($priceList, [
                "amount" => [
                    "currency_code" => 'CAD',
                    "value" => round($totalValue, 2),
                    "breakdown" => [
                        "item_total" => [
                            "currency_code" => 'CAD',
                            "value" => round($totalItemValue, 2),
                        ],
                        "discount" => [
                            "currency_code" => 'CAD',
                            "value" => round($totalDiscountValue, 2),
                        ],
                    ],
                ],
                "reference_id" => $trx_id . "order_1010",
            ]);

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('payment.success', ['provider' => 'paypal']),
                    "cancel_url" => route('payment.cancel', ['provider' => 'paypal']),
                ],
                "purchase_units" => $priceList,
            ]);
            
            if ( isset($response['id']) && $response['id'] != null ) {
                // redirect to approve href
                foreach ( $response['links'] as $links ) {
                    if ( $links['rel'] == 'approve' ) {

                        $trnx = Transaction::find($trx_id);
                        $trnx->transaction_id = $response['id'];
                        $trnx->status = 0;
                        $trnx->save();
                
                        $order->payment_provider_id = $response['id'];
                        $order->save();
                        Session::put('is_order_payment', 1);
                        return redirect()->away($links['href']);
                    }
                }
                Log::info("if",$response, config('paypal') );
                Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
                return back();
            } else {
                Log::info("else",$response, config('paypal') );
                Log::alert($response);
                Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
                return back();
            }
        } catch (\Exception $e) {
            Log::error("PayPal Transaction Error: " . $e->getMessage(), [
                'trx_id' => $trx_id,
                'request' => $request->all()
            ]);
    
            Toastr::error('Payment Failed: ' . $e->getMessage(), 'Error', ["positionClass" => "toast-top-right"]);
            return back();
        }

    }
    /**
     * success transaction.
     *
     * @return \Illuminate\Http\Response
     */
    public function successTransaction( Request $request, $trx_id, $source = false )
    {
        $order = Order::find(session('payment.order_id'));
        
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            
    
            if ( isset($response['status']) && $response['status'] == 'COMPLETED' ) {
    
                $trnx = Transaction::find( $trx_id);
                if ( $trnx) {
                    $trnx = $trnx->load('order');
                    
                    $trnx->status = 1;
                    $trnx->save();
    
                    $order = $trnx->order;
                    // $order->status = 5;
                    $order->payment_status = 1;
                    $order->pay_date = now();
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
    
                        $order->status = 50;
                        $order->save();
    
                        toastr()->success('Payment Completed');
                        $order->rUser->cart_json = null;
    
                        $used_wallet = session('used_wallet');
                        $wallet_discount = session('wallet_discount');
        
                        if ($used_wallet == 1 && $wallet_discount > 0) {
                            $order->used_wallet = 1;
                            $order->save();
                            $order->rUser->wallet_balance = $order->rUser->wallet_balance - $wallet_discount;
                        }
    
                        $order->rUser->save();
                        clearCartNSession(true);
    
                        return to_route('checkout.plan.confirmation',[
                            'id' => $order->plan_id, 'order_number' => $order->order_number
                         ]);
                    }
                    
                    $order->rUser->cart_json = null;
                    $used_wallet = session('used_wallet');
                    $wallet_discount = session('wallet_discount');
    
                    if ($used_wallet == 1 && $wallet_discount > 0) {
                        $order->used_wallet = 1;
                        $order->save();
                        $order->rUser->wallet_balance = $order->rUser->wallet_balance - $wallet_discount;
                    }
                    $order->rUser->save();
                    clearCartNSession(true);
                    toastr()->success('Payment Completed');
    
                    $isOrderPayment = Session::get('is_order_payment', 0);
                    if ($isOrderPayment) {
                        Session::forget('is_order_payment');
                        session()->flash('msg');
                        $setting = getSetting();
                        $body = "The status of your order {$order->order_number} has been updated.";
                        $msg = 'Your payment is successful. We will update your order with the tracking number shortly.';
                        // $invoice_link = url('invoice/' . Crypt::encryptString($order->id));
                    
                        // Generate a unique file name
                        $fileName = 'invoice_' . uniqid() . '_' . $order->user_id . '.pdf';
                        $filePath = public_path('uploads/invoice/' . $fileName);
                        
                        // Ensure the directory exists
                        if (!file_exists(public_path('uploads/invoice'))) {
                            mkdir(public_path('uploads/invoice'), 0777, true);
                        }
                    
                        // Generate PDF and save to the public directory
                        Pdf::loadView('common.payment_invoice_pdf', compact( 'trnx'))->save($filePath);

                        $data = [
                            'subject' => 'Order Update From '.$setting->site_name.': '.'Payment Successful',
                            'greeting' => 'Hi, '.$order->rUser?->name.' '.$order->rUser?->last_name,
                            'body' => $body,
                            'order_number' => $order->order_number,
                            'status' => 'Payment Successful',
                            'site_name' => $setting->site_name ?? config('app.name'),
                            'thanks' => $msg,
                            'invoice_link' => $filePath,
                            'site_url' => url('/'),
                            'footer' => 1,
                        ];
                        try {
                            Mail::to($order->rUser?->email)->send(new OrderMail($data));
                        } catch (\Exception $e) {
                            Log::alert('Order mail not sent. Error: ' . $e->getMessage());
                        }
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
                    toastr()->error('Order Not Found');
                }
    
                
    
                return to_route('user.order.billing', $order->id); 
            } else {
                toastr()->error('Payment declined. Please try again or contact your bank for assistance');
                return to_route('user.order.billing', $order->id);
            }
        } catch (\Exception $e) {
            Toastr::error('Payment Failed: ' . $e->getMessage(), 'Error', ["positionClass" => "toast-top-right"]);
            return to_route('user.order.billing', $order->id);
        }

    }

}

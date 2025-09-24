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
use Brian2694\Toastr\Facades\Toastr;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PayPalController
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

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $setting = getSetting();

        $priceList = [];
        
        if($price == null)
        {
            $cartProducts = getCartItems();
            foreach ( $cartProducts['year'] as $key => $cartItem ) {
                array_push($priceList, [
                    "amount" => [
                        "currency_code" => 'USD',
                        "value" => $cartProducts['quantity'][$key] * session('checkout.total_unit_price'),
                    ],
                    "reference_id" => $trx_id."_{$key}",
                ]);
            }
            dd(
                'dsfsd'
            );
        } else {
            $couponDiscount = Transaction::find($trx_id)->order->coupon_discount;
            array_push($priceList, [
                "amount" => [
                    "currency_code" => 'USD',
                    "value" => $price - $couponDiscount ?? 0, 
                    "breakdown" => [
                                "item_total" => [
                                    "currency_code" => 'USD',
                                    "value" => $price,
                                ],
                                "discount" => [
                                    "currency_code" => 'USD',
                                    "value" => $couponDiscount ?? 0,
                                ],
                            ],
                ],
                "reference_id" => $trx_id."pay_101",

            ]);

            $gst = $setting->gst_tax*$price/100;
            $pst = $setting->pst_tax*$price/100;
            $total_tax = $gst + $pst;

            if($total_tax > 0) {
        
                array_push($priceList, [
                    "amount" => [
                        "currency_code" => 'USD',
                        "value" => $total_tax,
                    ],
                    "reference_id" => $trx_id."tax_101",
                ]);
            }
        }


        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.success', ['provider' => 'paypal']),
                "cancel_url" => route('payment.cancel', ['provider' => 'paypal']),
            ],
            "purchase_units" => $priceList,
        ]);
        
        // dd($response, $priceList, $trx_id);

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
            return back();
        } else {
            Log::info($response, );
            Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
            return back();
        }


    }

    public function processOrderTransaction( Request $request , $trx_id)
    {

        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $setting = getSetting();

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
                
                if($order->coupon_id) {
                    $perItemDiscount = $order->coupon_discount / count($order->details);
                    array_push($priceList, [
                        "amount" => [
                            "currency_code" => 'USD',
                            "value" => ($cardCount * $order->net_unit_price) - $perItemDiscount,
                            "breakdown" => [
                                "item_total" => [
                                    "currency_code" => 'USD',
                                    "value" => $cardCount * $order->net_unit_price,
                                ],
                                "discount" => [
                                    "currency_code" => 'USD',
                                    "value" => $perItemDiscount,
                                ],
                            ],
                        ],
                        "reference_id" => $trx_id."_{$key}",
                    ]);
                }else{
                    array_push($priceList, [
                        "amount" => [
                            "currency_code" => 'USD',
                            "value" => $cardCount * $order->net_unit_price,
                        ],
                        "reference_id" => $trx_id."_{$key}",
                    ]);
                }

                $total_price += ($order->net_unit_price*$cardCount);
            }
        }
        $gst = $setting->gst_tax*$total_price/100;
        $pst = $setting->pst_tax*$total_price/100;
        $total_tax = $gst + $pst;

        array_push($priceList, [
            "amount" => [
                "currency_code" => 'USD',
                "value" => $total_tax,
            ],
            "reference_id" => $trx_id."tax_101",
        ]);


        

        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('payment.success', ['provider' => 'paypal']),
                "cancel_url" => route('payment.cancel', ['provider' => 'paypal']),
            ],
            "purchase_units" => $priceList,
        ]);
        
        // dd($response, $priceList, $trx_id);

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
            Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
            return back();
        } else {
            Log::alert($response);
            Toastr::error(trans($response['message'] ?? 'Something went wrong.'), trans('Error'), ["positionClass" => "toast-top-right"]);
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
                
                $order->rUser->cart_json = null;
                $order->rUser->save();
                clearCartNSession();
                toastr()->success('Payment Completed');

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
                toastr()->error('Order Not Found');
            }

            

            return to_route('user.dashboard');
        } else {
            toastr()->error('This order is Not paid yet');
            return to_route('user.dashboard');
            // ->with('error', $response['message'] ?? 'Something went wrong.');
        }
    }


}

<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TopupController;
use App\Http\Controllers\User\UserController;
use App\Mail\OrderMail;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

        
        $cartProducts = getCartItems();
        $priceList = [];

        if($price == null)
        {
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
        
            $productDetails = '';
        
            foreach ($cartProducts['year'] as $key => $cartItem) {
                $productName = $cartProducts['year'][$key] . ' ' .
                    $cartProducts['brand'][$key] . ' ' .
                    $cartProducts['cardNumber'][$key] . ' ' .
                    $cartProducts['playerName'][$key];
        
                $productDetails .= $productName . " \nQty: " . $cartProducts['quantity'][$key];

                if ($key < count($cartProducts['year']) - 1) {
                    $productDetails .= ", ";
                }
            }
        
            $priceList = [
                [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $planData->name,
                            'description' => $productDetails,
                        ],
                        'unit_amount' => $price * 100,
                    ],
                    'quantity' => 1,
                ],
            ];
        }
        

        $payment = $stripe->checkout->sessions->create([
            'success_url' => route('payment.success', ['provider' => 'stripe']),
            'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
            'customer' => $user->stripe_id,
            // 'customer_email' => $user->email,
            'line_items' => $priceList,
            'mode' => 'payment',
            'client_reference_id' => $trx_id,
        ]);

        
        // session(['payment.order_id' => $order->id,'payment.trx_id' => $transaction->id]);

        $trnx = Transaction::find(session('payment.trx_id'));
        
        $trnx->transaction_id = $payment->id;
        $trnx->status = 0;
        $trnx->save();
        
        $order = Order::find(session('payment.order_id'));
        $order->payment_provider_id = $payment->id;
        $order->save();


        if ( $payment && isset($payment['payment_status']) && isset($payment['url']) ) {
            return redirect()->away($payment['url']);
        } else {
            toastr()->error('Payment Failed');
            return back();
        }


    }


    function syncTransactionStatus( )
    {
        $trnx = Transaction::find( session('payment.trx_id'));
        
        if ( $trnx ) {

            $trnx = $trnx->load('order');
            $stripe_id = $trnx->transaction_id;
            $stripe = $this->stripe;

            $stripeData = $stripe->checkout->sessions->retrieve($stripe_id);

            if ( $stripeData?->payment_status == 'paid' ) {

                $trnx->status = 1;
                $trnx->save();

                $order = $trnx->order;
                // $order->status = 5;
                $order->payment_status = 1;
                $order->save();
                
                $planData = Plan::findOrFail(session('checkout.plan'));

                $authUser = auth()->user();
                $authUser->is_subscriber = 1;
                $authUser->current_plan_id = $planData->id;
                $authUser->current_plan_name = $planData->name;
                $authUser->subscription_start = now();
                $authUser->subscription_end = now()->addYears($planData->subscription_year);
                $authUser->subscription_card_peryear = $planData->subscription_peryear_card;
                $authUser->save();

                // $subscription = new UserSubscription();
                // $subscription->subscription_card_peryear = $planData->subscription_peryear_card;
                // $subscription->order_card_peryear = 0;
                // $subscription->year_start = now();
                // $subscription->year_end = now()->addYears($year_end + 1);
                // $subscription->save();

                toastr()->success('Payment Completed');
                clearCartNSession(true);

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

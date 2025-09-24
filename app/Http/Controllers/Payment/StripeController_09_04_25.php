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
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Stripe\Webhook;
use Toastr;

class StripeController_09_04_25 extends Controller
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
        $stripe = $this->stripe;
        $this->trx_id = $trx_id;
        $user = $request->user();
        $setting = getSetting();
        
        try {
            // if ( !$user->stripe_id ) {
                $stripUser = $stripe->customers->create([
                    "address" => $user->address,
                    "email" => $user->email,
                    "name" => $user->name . ' ' . $user->last_name,
                    "phone" => $user->phone_code . $user->phone,
                ]);
    
                $user->stripe_id = $stripUser->id;
                $user->save();
            // }
    
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
    
    
                    $currency = strtolower('cad');
    
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
                
  

                $userWalletBalance = auth()->user()->wallet_balance;
                $used_wallet = session('used_wallet');
                $walletDiscount = 0;

                if ($used_wallet == 1 && $userWalletBalance > 0) {
                    $walletDiscount = $userWalletBalance;
                    $price = $price - $walletDiscount;
                }

                session(['wallet_discount' => $walletDiscount]);

                
                $gst = $setting->gst_tax*$price/100;
                $pst = $setting->pst_tax*$price/100;
                $total_tax = $gst + $pst;
                // $price += $total_tax;
                $priceList = [
                    [
                        'price_data' => [
                            'currency' => 'cad',
                            'product_data' => [
                                'name' => $planData->name,
                            ],
                            'unit_amount' => $price * 100,
                        ],
                        'quantity' => 1,
                    ],
                ];
    
        
                if($total_tax > 0) {
                    $product = $stripe->products->create([
                        'name' => 'Tax',
                        'description' => "GST ({$setting->gst_tax}%) : $" . number_format($gst, 2) . ",\n\nPST ({$setting->pst_tax}%) : $" . number_format($pst, 2),
                    ]);
            
                    $price = $stripe->prices->create([
                        'currency' => 'cad',
                        'unit_amount' => ($gst + $pst) * 100,
                        'product' => $product->id,
                    ]);
            
                    $priceList[] = [
                        'price' => $price->id,
                        'quantity' => 1,
                    ];
                }            
    
            }
            $order = Order::find(session('payment.order_id'));
    
            $stripeApiData = [
                'success_url' => route('payment.success', ['provider' => 'stripe']),
                'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
                'metadata' => [
                    'title' => 'Total',
                ],
                'customer' => $user->stripe_id,
                // 'customer_email' => $user->email,
                'line_items' => $priceList,
                'mode' => 'payment',
                'client_reference_id' => $trx_id,
            ];
    
            // $userWalletBalance = auth()->user()->wallet_balance;
            // $used_wallet = session('used_wallet');
    
            $totalDiscountAmount = 0;
            $couponDiscount = 0;
            // $walletDiscount = 0;
            
            // Coupon discount
            if ($order->coupon_id) {
                $couponDiscount = $order->coupon_discount;
                $totalDiscountAmount += $couponDiscount;
            }
            
            // Wallet discount
            // if ($used_wallet == 1 && $userWalletBalance > 0) {
            //     $walletDiscount = $userWalletBalance;
            //     $totalDiscountAmount += $walletDiscount;
            // }
    
            // session(['wallet_discount' => $walletDiscount]);
    
            if ($totalDiscountAmount > 0) {
                $couponName = '';
            
                // if ($couponDiscount > 0 && $walletDiscount > 0) {
                //     $couponName = "Discount (Coupon + Wallet)";
                // } elseif ($couponDiscount > 0) {
                //     $coupon = $order->coupon;
                //     $couponName = "Discount ($coupon->discount_code)";
                // } elseif ($walletDiscount > 0) {
                //     $couponName = "Wallet (" . getDefaultCurrencySymbol() . " $walletDiscount)";
                // }
            
                if ($couponDiscount > 0) {
                    $coupon = $order->coupon;
                    $couponName = "Discount ($coupon->discount_code)";
                }

                $couponData = [
                    'amount_off' => $totalDiscountAmount * 100,
                    'duration' => 'once',
                    'currency' => 'cad',
                    'name' => $couponName,
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

        } catch (\Exception $e) {
            dd($e);
            toastr()->error('Payment Failed: ' . $e->getMessage());
            return back();
        }


        if ( $payment && isset($payment['payment_status']) && isset($payment['url']) ) {
            DB::commit();
            return redirect()->away($payment['url']);
        } else {
            DB::rollBack();
            toastr()->error('Payment Failed');
            return back();
        }


    }

    function makeOrderPayment( Request $request, $trx_id)
    {
        $stripe = $this->stripe;
        $this->trx_id = $trx_id;
        $user = $request->user();
        $setting = getSetting();

        try {
            
            // if (!$user->stripe_id) {
                $stripUser = $stripe->customers->create([
                    "address" => $user->address,
                    "email" => $user->email,
                    "name" => $user->name . ' ' . $user->last_name,
                    "phone" => $user->phone_code . $user->phone,
                ]);
    
                $user->stripe_id = $stripUser->id;
                $user->save();
            // }
    
            $order = Order::find(session('payment.order_id'));
            $priceList = [];
            $total_price = 0;
    
            foreach ($order->details as $key => $item) {
                // $cardCount = OrderCard::where('order_details_id', $item->id)->where('final_grading', '>', 0)->count();
                $cardCount = OrderCard::where('order_details_id', $item->id)
                // ->where(function ($query) {
                //     $query->where('final_grading', '>', 0)
                //           ->orWhere('final_grading', '=', 'A');
                // })
                ->count();
                if ($cardCount > 0) {
                    $cartProductPrice = $order->net_unit_price;
                    
                    // $productName = $item->item_name;
                    // $currency = strtolower('cad');
                    // $product = $stripe->products->create([
                    //     'name' => $productName,
                    // ]);
                    // $price = $stripe->prices->create([
                    //     'currency' => $currency,
                    //     'unit_amount' => $cartProductPrice * 100,
                    //     'product' => $product->id,
                    // ]);
                    // $priceList[] = [
                    //     'price' => $price->id,
                    //     'quantity' => $cardCount,
                    // ];
    
                    $total_price += ($cartProductPrice*$cardCount);
                }
            }
    
            // subtotal 
            $currency = strtolower('cad');
    
            // Wallet discount
            $userWalletBalance = auth()->user()->wallet_balance;
            $used_wallet = session('used_wallet');
            $walletDiscount = 0;
            if ($used_wallet == 1 && $userWalletBalance > 0) {
                $walletDiscount = $userWalletBalance;
                $total_price = $total_price - $walletDiscount;
            }
            session(['wallet_discount' => $walletDiscount]);

            $product = $stripe->products->create([
                'name' => "Subtotal",
            ]);
    
            $price = $stripe->prices->create([
                'currency' => $currency,
                'unit_amount' => $total_price * 100,
                'product' => $product->id,
            ]);
    
            $priceList[] = [
                'price' => $price->id,
                'quantity' => 1,
            ];
    
            // Shipping Charge
            $shippingProduct = $stripe->products->create([
                'name' => 'Shipping Charge ('.strtoupper($order->shipping_method).') - '.array_values($order->shipping_method_service_code)[0]??'N/A',
            ]);
    
            $shippingProductPrice = $stripe->prices->create([
                'currency' => $currency,
                'unit_amount' => ($order->shipping_charge) * 100,
                'product' => $shippingProduct->id,
            ]);

            $priceList[] = [
                'price' => $shippingProductPrice->id,
                'quantity' => 1,
            ];

            if ($used_wallet == 1 && $userWalletBalance > 0) {
                $total_price = $total_price + $order->shipping_charge;
            }

            $gst = $setting->gst_tax*($total_price - ($order->coupon_discount ?? 0))/100;
            $pst = $setting->pst_tax*($total_price - ($order->coupon_discount ?? 0))/100;
            $total_tax = $gst + $pst;
            $total_price += $total_tax;
    
            if($total_tax > 0) {    
                // GST
                $product = $stripe->products->create([
                    'name' => 'Tax',
                    'description' => "GST ({$setting->gst_tax}%): ".number_format($gst,2).", PST ({$setting->pst_tax}%): ".number_format($pst,2),
                ]);
        
                $price = $stripe->prices->create([
                    'currency' => $currency,
                    'unit_amount' => number_format($total_tax,2) * 100,
                    'product' => $product->id,
                ]);
        
                $priceList[] = [
                    'price' => $price->id,
                    'quantity' => 1,
                ];
    
                // PST
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
            }
    
            // Insurance Charge
            if($order->has_insurance){
                $insuranceProduct = $stripe->products->create([
                    'name' => 'Insurance Charge for ('.getDefaultCurrencySymbol().$order->insurance_value.')',
                ]);
    
                $insuranceProductPrice = $stripe->prices->create([
                    'currency' => $currency,
                    'unit_amount' => ($order->insurance_amount) * 100,
                    'product' => $insuranceProduct->id,
                ]);
        
                $priceList[] = [
                    'price' => $insuranceProductPrice->id,
                    'quantity' => 1,
                ];
            }
    
            // $stripeShipping = $stripe->shippingRates->create([
            //     'fixed_amount' => [
            //         'amount' => ($order->shipping_charge) * 100,
            //         'currency' => $currency,
            //     ],
            //     'display_name' => 'Shipping Charge ('.$order->shipping_method == 'ups' ? 'UPS Ground' : $order->shipping_method.')',
            //     'type' => 'fixed_amount'
            // ]);
    
            $stripeApiData = [
                'success_url' => route('payment.success', ['provider' => 'stripe']),
                'cancel_url' => route('payment.cancel', ['provider' => 'stripe']),
                'metadata' => [
                    'title' => 'Custom Title Here', // Add your custom title
                ],
                'customer' => $user->stripe_id,
                'line_items' => $priceList,
                'mode' => 'payment',
                'client_reference_id' => $trx_id,
                // 'customer_email' => $user->email,
                // 'shipping_options' => [
                //     'shipping_rate' => $stripeShipping->id,
                // ],
            ];
    
            // $userWalletBalance = auth()->user()->wallet_balance;
            // $used_wallet = session('used_wallet');
    
            $totalDiscountAmount = 0;
            $couponDiscount = 0;
            // $walletDiscount = 0;
            
            // Coupon discount
            if ($order->coupon_id) {
                $couponDiscount = $order->coupon_discount;
                $totalDiscountAmount += $couponDiscount;
            }
            
            // Wallet discount
            // if ($used_wallet == 1 && $userWalletBalance > 0) {
            //     $walletDiscount = $userWalletBalance;
            //     $totalDiscountAmount += $walletDiscount;
            // }
    
            // session(['wallet_discount' => $walletDiscount]);
    
            if ($totalDiscountAmount > 0) {
                $couponName = '';
            
                // if ($couponDiscount > 0 && $walletDiscount > 0) {
                //     $couponName = "Discount (Coupon + Wallet)";
                // } elseif ($couponDiscount > 0) {
                //     $coupon = $order->coupon;
                //     $couponName = "Discount ($coupon->discount_code)";
                // } elseif ($walletDiscount > 0) {
                //     $couponName = "Wallet (" . getDefaultCurrencySymbol() . " $walletDiscount)";
                // }
            
                if ($couponDiscount > 0) {
                    $coupon = $order->coupon;
                    $couponName = "Discount ($coupon->discount_code)";
                }

                $couponData = [
                    'amount_off' => $totalDiscountAmount * 100,
                    'duration' => 'once',
                    'currency' => $currency,
                    'name' => $couponName,
                ];
            
                $stripeCoupon = $stripe->coupons->create($couponData);
            
                $stripeApiData['discounts'] = [
                    [
                        'coupon' => $stripeCoupon->id,
                    ],
                ];
            }
            
            // Create Stripe Checkout session
            $payment = $stripe->checkout->sessions->create($stripeApiData);
    
            $trnx = Transaction::find(session('payment.trx_id'));
            
            $trnx->transaction_id = $payment->id;
            $trnx->status = 0;
            $trnx->save();
    
            $order->payment_provider_id = $payment->id;
            $order->save();

        } catch (\Exception $e) {
            toastr()->error('Payment Failed: ' . $e->getMessage());
            return back();
        }

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
        $order = Order::find(session('payment.order_id'));

        try {
            if ( $trnx ) {
    
                $trnx = $trnx->load('order');
                $stripe_id = $trnx->transaction_id;
                $stripe = $this->stripe;
    
                $stripeData = $stripe->checkout->sessions->retrieve($stripe_id);
                if(!$isWallet){
                }
    
                if ( $isWallet || $stripeData?->payment_status == 'paid' ) {
    
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
                    toastr()->error('Payment declined. Please try again or contact your bank for assistance');
                }
            } else {
                toastr()->error('Order Not Found');
            }
        } catch (\Exception $e) {
            toastr()->error('Payment Failed: ' . $e->getMessage());
        }

        return to_route('user.order.billing', $order->id);
    }


    // public function handleWebhook(Request $request)
    // {
    //     $payload = $request->getContent();
    //     $sig_header = $request->header('Stripe-Signature');
    //     $endpoint_secret = DB::table('config')->where('config_key', 'stripe_webhook_secret')->value('config_value');
    
    //     try {
    //         $event = Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => $e->getMessage()], 400);
    //     }
    
    //     if ($event->type === 'checkout.session.completed') {
    //         $session = $event->data->object;
    
    //         $trnx = Transaction::with('order.rUser')->where('transaction_id', $session->id)->first();
    
    //         if ($trnx) {
    //             $this->handleStripeTransactionSuccess($trnx);
    //         }
    //     }
    
    //     return response()->json(['status' => 'success']);
    // }

    // private function handleStripeTransactionSuccess(Transaction $trnx)
    // {
    //     $order = $trnx->order;

    //     $trnx->status = 1;
    //     $trnx->save();

    //     $order->payment_status = 1;
    //     $order->pay_date = now();
    //     $order->save();

    //     // Update user plan if needed
    //     $planData = Plan::find($order->plan_id);
    //     $user = $order->rUser;

    //     if ($planData && $planData->type === 'subscription') {
    //         $user->is_subscriber = 1;
    //         $user->current_plan_id = $planData->id;
    //         $user->current_plan_name = $planData->name;
    //         $user->subscription_start = now();
    //         $user->subscription_end = now()->addYears($planData->subscription_year);
    //         $user->subscription_card_peryear = $planData->subscription_peryear_card;
    //         $user->save();

    //         // Subscription history creation
    //         $currentYearStart = now();
    //         for ($i = 0; $i < $planData->subscription_year; $i++) {
    //             UserSubscription::create([
    //                 'user_id' => $user->id,
    //                 'subscription_card_peryear' => $planData->subscription_peryear_card,
    //                 'order_card_peryear' => $i === 0 ? $order->total_order_qty : 0,
    //                 'year_start' => $currentYearStart,
    //                 'year_end' => $currentYearStart->copy()->addYear(),
    //             ]);
    //             $currentYearStart = $currentYearStart->copy()->addYear();
    //         }

    //         $order->status = 50;
    //         $order->save();
    //     }

    //     // Send emails (same as before)

    //     return true;
    // }

    // public function syncTransactionStatus2($isWallet = false)
    // {
    //     $trnx = Transaction::with('order.rUser')->find(session('payment.trx_id'));

    //     if (!$trnx) {
    //         toastr()->error('Transaction not found.');
    //         return to_route('user.order.billing', session('payment.order_id'));
    //     }

    //     $order = $trnx->order;

    //     try {
    //         $stripeData = $this->stripe->checkout->sessions->retrieve($trnx->transaction_id);

    //         if ($isWallet || $stripeData?->payment_status === 'paid') {
    //             // Call the shared payment success logic
    //             $this->handleStripeTransactionSuccess($trnx);

    //             // Post-payment browser flow
    //             toastr()->success('Payment Completed');

    //             // Clear cart & wallet session data
    //             $order->rUser->cart_json = null;
    //             $used_wallet = session('used_wallet');
    //             $wallet_discount = session('wallet_discount');

    //             if ($used_wallet == 1 && $wallet_discount > 0) {
    //                 $order->used_wallet = 1;
    //                 $order->rUser->wallet_balance -= $wallet_discount;
    //                 $order->save();
    //                 $order->rUser->save();
    //             }

    //             clearCartNSession(true);

    //             // Redirect user to order confirmation or invoice
    //             $isOrderPayment = Session::get('is_order_payment', 0);
    //             if ($isOrderPayment) {
    //                 Session::forget('is_order_payment');
    //                 return redirect()->route('user.order.payment.invoice', $order->id);
    //             }

    //             return to_route('checkout.confirmation', [
    //                 'id' => $order->plan_id,
    //                 'order_number' => $order->order_number
    //             ]);
    //         } else {
    //             toastr()->error('Payment declined. Please try again or contact your bank.');
    //         }
    //     } catch (\Exception $e) {
    //         toastr()->error('Payment failed: ' . $e->getMessage());
    //     }

    //     return to_route('user.order.billing', $order->id ?? session('payment.order_id'));
    // }
}

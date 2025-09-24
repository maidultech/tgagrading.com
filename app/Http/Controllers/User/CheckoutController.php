<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\CanadaPostController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\UPSController;
use App\Models\Coupon;
use App\Models\ItemBrand;
use App\Models\Order;
use App\Models\Plan;
use App\Models\ServiceLevel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Toastr;

class CheckoutController extends Controller
{
    public function itemType($id)
    {
        $data['plan'] = Plan::findOrFail($id);
        $data['user'] = Auth::user();
        clearCartNSession();
        if ($data['user'] && $data['user']->cart_json) {
            $cartItems = json_decode($data['user']->cart_json, true);
            session(['checkout.items' => $cartItems]);
        }
        $data['total_cards'] = array_sum(collect(session('checkout.items') ?? [])?->get('quantity') ?? []);
        return view('checkout.item_type', $data);
    }

    function itemTypeStore(Request $request, $id){
        
        $request->validate([
            'item_type' => 'required|integer|in:'.implode(',',range(1, count(config('static_array.item_type'))))
        ]);

        session(['checkout.item_type' => $request->item_type]);

        return to_route('checkout.submission.type',$id);
    }

    public function submissionTypeStore(Request $request, $id)
    {
        $request->validate([
            'submission_type' => 'required|integer|in:'.implode(',',range(1, count(config('static_array.submission_type'))))
        ]);

        $plan = Plan::findOrFail($id);

        $authUser = auth()->user();
        if($authUser->is_subscriber && $authUser->subscription_start < now() && $authUser->subscription_end > now() && $plan->type == 'subscription')
        {
            $plan = Plan::where('type', 'single')->first();
            $plan_price = $plan->price;
        } else {
            $plan_price = $plan->price;
        }

        session([
            'checkout.plan' => $plan->id,
            'checkout.total_unit_price' =>  $plan_price,
            'checkout.old_total_unit_price' =>  $plan_price,
        ]);

        session(['checkout.submission_type' => $request->submission_type]);

        return to_route('checkout.item.entry',$id);
    }

    public function submissionType($id)
    {
        $data['plan'] = Plan::findOrFail($id);
        $data['user'] = Auth::user();
        if ($data['user'] && $data['user']->cart_json) {
            $cartItems = json_decode($data['user']->cart_json, true);
            session(['checkout.items' => $cartItems]);
        }
        $data['total_cards'] = array_sum(collect(session('checkout.items') ?? [])?->get('quantity') ?? []);

        return view('checkout.submission_type', $data);
    }

    public function serviceLevelStore(Request $request, $id)
    {
        $request->validate([
            'service_level' => 'required|exists:service_levels,id'
        ]);

        $plan = Plan::findOrFail($id);

        $authUser = auth()->user();
        if($authUser->is_subscriber && $authUser->subscription_start < now() && $authUser->subscription_end > now() && $plan->type == 'subscription')
        {
            $plan = Plan::where('type', 'single')->first();
            $plan_price = $plan->price;
        } else {
            $plan_price = $plan->price;
        }

        $sl_price = ServiceLevel::findOrFail($request->service_level)->extra_price;

        session([
            'checkout.service_level' => $request->service_level,
            'checkout.plan' => $plan->id,
            'checkout.total_unit_price' =>  $plan_price + $sl_price,
            'checkout.old_total_unit_price' =>  $plan_price + $sl_price
        ]);
        // $data['plan'] = Plan::findOrFail($id);
        // $data['serviceLevels'] = ServiceLevel::find($request->submission_type);
        return to_route('checkout.item.entry',$id);
    }


    public function serviceLevel($id)
    {
        $data['plan'] = Plan::findOrFail($id);
        $data['serviceLevels'] = ServiceLevel::get();
        return view('checkout.service_level', $data);
    }


    public function itemEntry($id)
    {
        $data['plan'] = Plan::findOrFail($id);
        $data['user'] = Auth::user();
        $data['brands'] = ItemBrand::where('status', '1')->get();

        if ($data['user'] && $data['user']->cart_json) {
            $cartItems = json_decode($data['user']->cart_json, true);
            session(['checkout.items' => $cartItems]);
        }

        // $data['service_level'] = ServiceLevel::findOrFail(session('checkout.service_level'));
        $data['total_cards'] = array_sum(collect(session('checkout.items') ?? [])?->get('quantity') ?? []);

        return view('checkout.item_entry', $data);
    }

    public function itemEntryStore(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $isApi = $request->get('api') ? true : false;

        $validationRules = [
            'year' => 'required|array|min:1',
            'year.*' => 'required|integer|min:1900|max:2100',
            'brand' => 'required|array|min:1',
            'brand.*' => 'required|string|max:255',
            'cardNumber' => 'required|array',
            'cardNumber.*' => 'required',
            'playerName' => 'required|array|min:1',
            'playerName.*' => 'required|string|max:255',
            'notes' => 'nullable|array',
            'notes.*' => 'nullable|string|max:10000',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric|integer|',
        ];

        if($isApi){
  
            if($request->missing('year')){
                session()->forget(['checkout.total_quantity','checkout.items']);
                $checkoutItems = session('checkout.items');
                if (!is_null($checkoutItems) && !$request->has('order_id')) {
                    $user = $request->user();
                    $user->cart_json = json_encode($checkoutItems);
                    $user->save();
                } else {
                    $user = $request->user();
                    $user->cart_json = null;
                    $user->save();
                }
                return response([
                    'success' => true,
                    'message' => "Card Cleared"
                ]);
            }
        }else{
            $request->validate($validationRules, [
                'year.*.required' => 'Year is required.',
                'year.*.integer' => 'Year must be an integer.',
                'year.*.min' => 'Year must be at least 1900.',
                'year.*.max' => 'Year must not exceed 2100.',
                'brand.*.required' => 'Brand is required.',
                'brand.*.string' => 'Brand must be a string.',
                'brand.*.max' => 'Brand must not exceed 255 characters.',
                'cardNumber.*.required' => 'Card number is required.',
                'playerName.*.required' => 'Player name is required.',
                'playerName.*.string' => 'Player name must be a string.',
                'playerName.*.max' => 'Player name must not exceed 255 characters.',
                'notes.*.string' => 'Note must be a string.',
                'notes.*.max' => 'Note must not exceed 10000 characters.',
                'quantity.*.required' => 'Quantity is required.',
                'quantity.*.numeric' => 'Quantity must be a number.',
                'quantity.*.integer' => 'Quantity must be an integer.',
            ]);
        }

        $user = auth()->user();

        // if($plan->type == 'subscription') 
        // {
        //     if(collect($request->quantity)->sum() > $plan->subscription_peryear_card){
        //         if($isApi){
        //             return response([
        //                 'success' => false,
        //                 'message' => "You have exceeded the maximum card creation limit for this plan."
        //             ]);
        //         }else{
        //             toastr()->error("You have exceeded the maximum card creation limit for this plan.");
        //             return back();
        //         }
        //     }
        // }
        // if($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now())
        // {
        //     $available_card_limit = $user->getAvailableCardLimit();
        //     if($available_card_limit > 0)
        //     {
        //         if(collect($request->quantity)->sum() > $available_card_limit){
        //             if($isApi){
        //                 return response([
        //                     'success' => false,
        //                     'message' => "You have {{$available_card_limit}} card(s) left this year, exceeding the limit."
        //                 ]);
        //             }else{
        //                 toastr()->error("You have ".$available_card_limit." card's left this year, exceeding the limit.");
        //                 return back();
        //             }
        //         }
        //     }
        // }

        $setting = getSetting();
        
        $quantities = collect($request->except('_token'))->get('quantity', []);
        $totalQuantity = array_sum($quantities);
        $old_total_unit_price = session('checkout.old_total_unit_price');

        session(['checkout.total_quantity' => $totalQuantity]);

        if ($totalQuantity >= 100 && $totalQuantity <= 499) {
            session(['checkout.total_unit_price' => $setting->min_bulk_grading_cost]);
        } elseif ($totalQuantity >= 500) {
            session(['checkout.total_unit_price' => $setting->max_bulk_grading_cost]);
        } else {
            session(['checkout.total_unit_price' => $old_total_unit_price]);
        }

        session([
            'checkout.items' => $request->except([
                '_token',
                'order_id'
            ])
        ]);

        $checkoutItems = session('checkout.items');

        if ($checkoutItems && !$request->has('order_id')) {
            $user = $request->user();
            $user->cart_json = json_encode($checkoutItems);
            $user->save();
        }
        
        if($plan->type == 'general') 
        {
            if($plan->minimum_card > collect($request->quantity)->sum()){
                if($isApi){
                    return response([
                        'success' => false,
                        'message' => "Minimum $plan->minimum_card is required for selected plan"
                    ]);
                }else{
                    toastr()->error("Minimum $plan->minimum_card is required for selected plan");
                    return back();
                }
            }
        }

        if($isApi){
            return response([
                'success' => true,
                'unit_price' => session('checkout.total_unit_price'),
            ]);
        }

        

        // $data['service_level'] = ServiceLevel::findOrFail(session('checkout.service_level'));
        
        return to_route('checkout.shipping.billing',$plan->id);
    }

    public function shippingBilling($id)
    {
        $data['plan'] = Plan::findOrFail($id);
        $data['user'] = Auth::user();
        
        $checkout = session('checkout');
        $authUser = $data['user'];

        if ($authUser->is_subscriber && $authUser->subscription_start < now() && $authUser->subscription_end > now() && $data['plan']->type == 'subscription') 
        {
            $generalPlan = Plan::where('type', 'general')->where('status', 1)->first();
            if ($checkout['total_quantity'] >= $generalPlan->minimum_card) {
                $plan_price = $generalPlan->price;

                $checkout['plan'] = $generalPlan->id;
                $checkout['total_unit_price'] = $plan_price;

                session(['checkout' => $checkout]);
            }
        }

        if (!session()->has('checkout.order_id') || empty(session('checkout.order_id'))) {
            if ($data['user'] && $data['user']->cart_json) {
                $cartItems = json_decode($data['user']->cart_json, true);
                session(['checkout.items' => $cartItems]);
            }
        }

        // $data['service_level'] = ServiceLevel::findOrFail(session('checkout.service_level'));
        $data['total_cards'] = array_sum(collect(session('checkout.items'))->get('quantity'));
        if(session()->has('checkout.order_id') && !empty(session('checkout.order_id'))) {
            $data['order_id'] = session('checkout.order_id');
            $order = Order::with('transaction')->find(session('checkout.order_id'));
            if($order->transaction) {
                $shipping_info = $order->transaction->shipping_data;
                $data['shipping_info'] = $shipping_info;
            }
        }
        
        $data['config'] = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
        return view('checkout.shipping_billing', $data);
    }

    public function planCheckout($id)
    {
        $user = auth()->user();
        if($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now())
        {
            return redirect()->route('frontend.index');
        }
        
        $data['plan'] = Plan::findOrFail($id);
        $data['user'] = Auth::user();
        $data['config'] = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
        return view('checkout.plan_checkout', $data);
    }

    public function applyPlanCoupon(Request $request, Plan $plan)
    {
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first('coupon_code'),
            ];
        }

        $coupon = Coupon::where('discount_code', $request->coupon_code)
            ->where('status', 1)
            ->where('expiration_date', '>', now())
            ->first();

        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid coupon code.',
            ];
        }

        if ($coupon->max_uses !== null && $coupon->total_uses >= $coupon->max_uses) {
            return [
                'success' => false,
                'message' => 'This coupon has reached its maximum redemption limit.',
            ];
        }

        $userRedemptions = Order::where('coupon_id', $coupon->id)
            ->where('user_id', Auth::id())->count();

        if ($coupon->max_redemptions_per_user !== null && $userRedemptions >= $coupon->max_redemptions_per_user) {
            return [
                'success' => false,
                'message' => 'You have reached the maximum redemption limit for this coupon.',
            ];
        }

        $discountAmount = $this->calculateDiscount($coupon, $plan->price);
        
        if($discountAmount > $plan->price) {
            return [
                'success' => false,
                'message' => 'Invalid coupon. The discount amount exceeds your checkout total. Please use another coupon.',
            ];
        }



        Session::put('checkout.plan.coupon', [
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->discount_code,
            'discount_amount' => $discountAmount,
        ]);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
        ];
    }

    public function updateShippingMethod(Request $request, Order $order)
    {
        $request->validate([
            'shipping_method' => 'required|in:08,11,USA.EP,DOM.EP,local.pickup',
        ]);

        if (in_array($request->shipping_method, ['USA.EP', 'DOM.EP'])) {
            app(CanadaPostController::class)->rate($order->id, $request->shipping_method);
        } elseif (in_array($request->shipping_method, ['08', '11'])) {
            app(UPSController::class)->rate($order->id, $request->shipping_method);
        } elseif ($request->shipping_method === 'local.pickup') {
            $sessionKey = 'checkout.delivery_charge_' . $order->id;
            session([
                $sessionKey => [
                    'serviceCode' => 'local.pickup',
                    'serviceName' => 'Local Pickup',
                    'price' => 0.00,
                    'finalCharge' => 0.00,
                    'metaData' => []
                ]
            ]);
        }

        return [
            'success' => true,
            'message' => 'Shipping method updated successfully!',
        ];
    }

    public function orderBilling($id)
    {

        $order = Order::withSum('details', 'qty')->findOrFail($id);

        $order->load('cards');
        $data['user'] = Auth::user();
        $data['config'] = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
     

        // session()->forget(['checkout.coupon']);
        $upsShippingRates = app(UPSController::class)->rate($id) ?? [];
        $cpShippingRates = app(CanadaPostController::class)->rate($id) ?? [];

        // dd($upsShippingRates, $cpShippingRates);
        if($upsShippingRates['success'] == false){
            $data['order'] = $order;
            $data['error'] = $upsShippingRates['message'];
            $data['has_error'] = 1;
            return view('checkout.order_billing', $data);
        }else if($cpShippingRates['success'] == false){
            $data['order'] = $order;
            $data['error'] = $cpShippingRates['message'];
            $data['has_error'] = 1;
            return view('checkout.order_billing', $data);
        }

        $sessionKey = 'checkout.delivery_charge_' . $order->id;

        if(session($sessionKey)){
            $shippingCharge = session($sessionKey);
        }else{
            $shippingCharge = $upsShippingRates['data']->where('serviceCode','08')?->first();
            if(!$shippingCharge){
                $shippingCharge = $upsShippingRates['data']->where('serviceCode','11')?->first();
            }
            if(!$shippingCharge){
                $shippingCharge = $cpShippingRates['data']->where('serviceCode','USA.EP')?->first();
            }
            if(!$shippingCharge){
                $shippingCharge = $cpShippingRates['data']->where('serviceCode','DOM.EP')?->first();
            }

            if (!$shippingCharge && Auth::user()->local_pickup == 1) {
                $shippingCharge = [
                    'serviceCode' => 'local.pickup',
                    'serviceName' => 'Local Pickup',
                    'price' => 0.00,
                    'finalCharge' => 0.00,
                    'metaData' => []
                ];
            }

            if(!$shippingCharge){
                $data['order'] = $order;
                $data['error'] = 'Shipping method service data not found.';
                $data['has_error'] = 1;
                return view('checkout.order_billing', $data);
            }
        }

        // if(!$order->shipping_method_service_code){
        //     toastr()->error('Please select a shipping method service data not found.');
        //     return back();
        // }

        $order->shipping_method_service_code = [
            $shippingCharge['serviceCode'] => $shippingCharge['serviceName']
        ];
        
        $upsShippingRates['data'] = $upsShippingRates['data']->map(function ($rate) {
            if ($rate['serviceName'] === 'UPS Standard') {
                $rate['serviceName'] = 'UPS Standard';
            }
            return $rate;
        });
        $cpShippingRates['data'] = $cpShippingRates['data']->map(function ($rate) {
            if ($rate['serviceName'] === 'Expedited Parcel') {
                $rate['serviceName'] = 'Canada Post';
            }
            return $rate;
        });

        if($shippingCharge['serviceName'] == 'Expedited Parcel') {
            $shippingCharge['serviceName'] = 'Canada Post'; 
        } elseif($shippingCharge['serviceName'] == 'UPS Standard') {
            $shippingCharge['serviceName'] = 'UPS Standard';
        }

        $shippingMethods = array_merge(
            $upsShippingRates['data']->toArray(),
            $cpShippingRates['data']->toArray()
        );

        if (Auth::user()->local_pickup == 1) {
            $shippingMethods[] = [
                'serviceCode' => 'local.pickup',
                'serviceName' => 'Local Pickup',
                'price' => 0.00,
                'finalCharge' => 0.00,
                'metaData' => []
            ];
        }

        $data['shippingMethods'] = $shippingMethods;
        $data['shippingCharges'] = $shippingCharge;

        $order->shipping_charge = $data['shippingCharges']['finalCharge'];

        if (in_array($shippingCharge['serviceCode'], ['08', '11'])) {
            $order->shipping_method = 'ups';
        } elseif (in_array($shippingCharge['serviceCode'], ['USA.EP', 'DOM.EP'])) {
            $order->shipping_method = 'canada_post';
        } elseif ($shippingCharge['serviceCode'] === 'local.pickup') {
            $order->shipping_method = 'local_pickup';
        }
        $order->save();

        $data['order'] = $order;
        $data['plan'] = Plan::findOrFail($data['order']->plan_id);
        
        // $data['user'] = Auth::user();
        // $data['config'] = DB::table('config')->whereIn('config_key',['stripe_enable','paypal_enable'])->get();
       
        return view('checkout.order_billing', $data);
    }

    public function confirmation($id, $edit_flag, $order_number)
    {   
        abort_if( Order::where('order_number',$order_number)->where('user_id',auth()->id())->doesntExist(), 404);
        $data['order']=Order::where('order_number',$order_number)->where('user_id',auth()->id())->first();
        $data['plan'] = Plan::findOrFail($id);
        $data['edit_flag'] = $edit_flag;
        return view('checkout.confirmation', $data);
    }

    public function planConfirmation($id,$order_number)
    {   abort_if(Order::where('order_number',$order_number)->where('user_id',auth()->id())->doesntExist(),404)    ;
        $data['order']=Order::where('order_number',$order_number)->where('user_id',auth()->id())->first();
        $data['plan'] = Plan::findOrFail($id);
        return view('checkout.plan_confirmation', $data);
    }

    public function applyCoupon(Request $request, Order $order)
    {

        if(!$order){
            return [
                'success' => false,
                'message' => 'Order not found.',
            ];
        }
        $order->load('cards');

        // $total_price = $order->net_unit_price * $order->cards->where('final_grading', '>', 0)->count();
   
        if($order->is_redeemed == 1 && $order->extra_cards > 0) {
            $total_price = $order->net_unit_price * $order->extra_cards;
        } else {
            $total_price = $order->net_unit_price * $order->cards->count();
        }
        $validator = Validator::make($request->all(), [
            'coupon_code' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return [
                'success' => false,
                'message' => $validator->errors()->first('coupon_code'),
            ];
        }

        $coupon = Coupon::where('discount_code', $request->coupon_code)
            ->where('status', 1)
            ->where('expiration_date', '>', now())
            ->first();

            
        if (!$coupon) {
            return [
                'success' => false,
                'message' => 'Invalid coupon code.',
            ];
        }
        

        if ($coupon->max_uses !== null && $coupon->total_uses >= $coupon->max_uses) {
            return [
                'success' => false,
                'message' => 'This coupon has reached its maximum redemption limit.',
            ];
        }

        $userRedemptions = Order::where('coupon_id', $coupon->id)
            ->where('user_id', Auth::id())->count();

        if ($coupon->max_redemptions_per_user !== null && $userRedemptions >= $coupon->max_redemptions_per_user) {
            return [
                'success' => false,
                'message' => 'You have reached the maximum redemption limit for this coupon.',
            ];
        }


        $discountAmount = $this->calculateDiscount($coupon, $total_price);
        
        // if($discountAmount > $total_price) {
        //     return [
        //         'success' => false,
        //         'message' => 'Invalid coupon. The discount amount exceeds your checkout total. Please use another coupon.',
        //     ];
        // }

        $coupon->increment('total_uses');
        $coupon->refresh();
        
        
        

        $order->coupon_id = $coupon->id;
        $order->coupon_discount = $discountAmount;
        $order->save();

        Session::put('checkout.coupon', [
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->discount_code,
            'discount_amount' => $discountAmount,
        ]);


        return [
            'success' => true,
            'message' => 'Coupon applied successfully!',
        ];
    }

    private function calculateDiscount(Coupon $coupon, $subtotal)
    {
        if ($coupon->discount_type === 'Percent') {
            return ($subtotal * $coupon->discount_value) / 100;
        } elseif ($coupon->discount_type === 'Fixed') {
            // return min($coupon->discount_value, $subtotal);
            return $coupon->discount_value;
        }

        return 0;
    }

    public function addressUpdate(Request $request, $id) 
    {
        try {
            $order = Order::findOrFail($id);
            $transaction = $order->transaction;
    
            $userAddress = [
                'shippingName' => $request->shippingName,
                'shippingAddress' => $request->shippingAddress,
                'shippingCity' => $request->shippingCity,
                'shippingState' => $request->shippingState,
                'shippingZip' => $request->shippingZip,
                'shippingCountry' => $request->shippingCountry,
                'shippingPhone' => ('+' . $request->dial_code . $request->shippingPhone)
            ];
    
            $transaction->shipping_data = $userAddress;
            $transaction->save();
    
            toastr()->success('Shipping & Billing information updated successfully');
            return back();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            toastr()->error('Order not found.');
            return back();
        } catch (\Throwable $e) {
            Log::error('Address Update Error: ' . $e->getMessage());
            toastr()->error('An error occurred while updating the address. Please try again.');
            return back();
        }
    }
    

    public function orderEdit($id)
    {
        $order = Order::with('details')->findOrFail($id);
        $user = Auth::user();

        $items = [
            'year' => [],
            'brand' => [],
            'cardNumber' => [],
            'playerName' => [],
            'notes' => [],
            'quantity' => [],
            'comments' => $order->note,
            'api' => 'entry-page',
        ];

        foreach ($order->details as $detail) {
            $items['year'][] = $detail->year;
            $items['brand'][] = $detail->brand_name;
            $items['cardNumber'][] = $detail->card;
            $items['playerName'][] = $detail->card_name;
            $items['notes'][] = $detail->notes;
            $items['quantity'][] = (string) $detail->qty;
            $items['details_id'][] = (int) $detail->id;
        }

        // Build full checkout session
        $checkoutSession = [
            'item_type' => (string) $order->OrderCard,
            'plan' => $order->plan_id,
            'total_unit_price' => (float) $order->net_unit_price,
            'submission_type' => (string) $order->submission_type,
            'total_quantity' => (int) $order->total_order_qty,
            'order_edit' => 1,
            'order_id' => (int) $order->id,
            'items' => $items,
        ];

        session(['checkout' => $checkoutSession]);

        // Prepare the view data like itemEntry()
        $data['plan'] = Plan::findOrFail($order->plan_id);
        $data['user'] = $user;
        $data['brands'] = ItemBrand::where('status', '1')->get();

        $cartItems = $items; // already built above
        $data['total_cards'] = array_sum($cartItems['quantity'] ?? []);

        return view('checkout.item_entry', $data);
    }
}

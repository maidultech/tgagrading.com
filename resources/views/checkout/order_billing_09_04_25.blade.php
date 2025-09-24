@extends('frontend.layouts.app')

@section('title')
    {{ 'Checkout' }}
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/css/intlTelInput.css">
    <style>
        .iti.iti--allow-dropdown.iti--show-flags.iti--inline-dropdown {
            width: 100% !important;
        }

        .toc-wrapper .form-check-label:before {
            top: 0;
            right: 0;
            left: 0;
        }

        .checkout_wrapper .item_list .info {
            font-size: 13px;
        }
    </style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Checkout') }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= checkout start  ============================ -->
<div class="checkout-sec pb-5">
    <div class="container">
        
        <form action="@if(!isset($has_error) || $has_error != 1) {{ route('payment.order.store') }} @else {{ route('checkout.order.address.update', $order->id) }} @endif" method="post" id="submit_order">
            @csrf
            <div class="row gy-4 gy-lg-0">
                @if(isset($has_error) && $has_error == 1)
                <div class="col-lg-12 text-center mb-4">
                    <h4 class="text-danger">{{$error}}</h4>
                    <h6> If you need any help, please <a href="{{route('frontend.contact')}}">contact</a> our support team for assistance.</h6>
                </div>
                @endif
                @if(!isset($has_error) || $has_error != 1)
                <div class="col-lg-4 order-lg-2">
                    <div class="checkout_sidebar rounded rounded-4 border-light border-light-subtle card border-1 p-4 sticky-top"
                        style="top:1rem;">
                        <div class="heading mb-4">
                            <h4>Summary</h4>
                        </div>
                        <div class="sidebar_box">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Item Type</td>
                                    <td class="text-end">Trading Cards</td>
                                </tr>
                                <tr>
                                    <td>Submission Type</td>
                                    <td class="text-end">Grading</td>
                                </tr>
                                {{-- <tr>
                                    <td>Service Level</td>
                                    <td class="text-end">{{ $service_level->name }}</td>
                                </tr> --}}

                                <tr class="insurance_input" style="display: none;">
                                    <td>
                                        <label for="">Total Value Of Order</label>
                                        <br>
                                        <span style="font-size: 11px">(Based on your evaluation)</small>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" name="insurance_value"
                                            id="insurance_value" value="">
                                    </td>
                                </tr>
                                <tr>
                                    <td>Price</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ $order->net_unit_price }}/item
                                    </td>
                                </tr>
                                @php
                                    $total_price = number_format(
                                        // $order->net_unit_price * $order->cards->where('final_grading', '>', 0)->count(),
                                        $order->net_unit_price * $order->cards->count(),
                                        2,
                                    );
                                    $total_price = (float) str_replace(',', '', $total_price);
                                    $gtotalAmount = $total_price;
                                @endphp

                                <tr>
                                    {{-- <td>Items x {{ $order->cards->where('final_grading', '>', 0)->count() }}</td> --}}
                                    <td>Items x {{ $order->cards->count() }}</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ $total_price }}
                                    </td>
                                </tr>
                                
                                <tr style="border-bottom: 0 !important;">
                                    <td  style="border-bottom: 0 !important;" class="pb-0">
                                        <div class="form-check form-switch">
                                            <label for="has_insurance">
                                                Insurance <br>
                                                <span class="text-success">$1.99 per $100 of value</span>
                                            </label>
                                            <input class="form-check-input" type="checkbox" id="has_insurance"
                                                name="has_insurance" value="1">
                                            {{-- <label class="form-check-label" for="has_insurance"></label> --}}
                                        </div>
                                    </td>
                                    <td class="text-end pb-0"  style="border-bottom: 0 !important;">
                                        {{ getDefaultCurrencySymbol() }} <span class="insurance-amount">0.00</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="pt-1">
                                        <div class="text-danger insurance-error-msg">
                                            By not choosing insurance on your package, you waive the right to claim
                                            against any loss or damage during transit to your home address. Click to
                                            choose insurance.
                                        </div>
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format($total_price, 2) }}
                                        @php  $gtotalAmount += $total_price; @endphp
                                    </td>
                                </tr> --}}
                                @if ($shippingCharges['serviceName'])
                                    <tr>
                                        <td colspan="2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div><label for="">Shipping Charge</label><br>
                                                    <span style="font-size: 11px">({{ $shippingCharges['serviceName'] }}) -
                                                    {{ getDefaultCurrencySymbol() . number_format($shippingCharges['finalCharge'],2) }}</span>
                                                </div>
                                                <div>
                                                    {{-- {{ getDefaultCurrencySymbol() }}{{ number_format($shippingCharges['data']['finalCharge'], 2) }} --}}
                                                    <select name="shpping_methods" id="shipping_method"
                                                        class="form-control form-select"
                                                        style="padding: .375rem 2.25rem .375rem .75rem; ">
                                                        @foreach ($shippingMethods as $item)
                                                            <option @selected($item['serviceCode'] == $shippingCharges['serviceCode'])
                                                                value="{{ $item['serviceCode'] }}">{{ $item['serviceName'] }}
                                                                {{ getDefaultCurrencySymbol() . number_format($item['finalCharge'],2) }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @php
                                                        $gtotalAmount += $shippingCharges['finalCharge'];
                                                    @endphp
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                @if($shippingCharges['serviceName'])
                                    <input type="hidden" name="shipping_charge" id="shipping_charge" value="{{$shippingCharges['finalCharge']}}">
                                @else
                                    <input type="hidden" name="shipping_charge" id="shipping_charge" value="0">
                                @endif

                                <tr>
                                    <td>GST ({{ $setting->gst_tax }}%)</td>
                                    <td class="text-end">
                                        @php
                                        $__gstTax = ($setting->gst_tax * ($total_price - ($order->coupon_discount??0))) / 100;
                                        @endphp
                                        {{ getDefaultCurrencySymbol() }}<span class="gst-amount" data-amount="{{ $__gstTax }}">{{ number_format($__gstTax, 2) }}</span>
                                        @php  $gtotalAmount += $__gstTax; @endphp
                                    </td>
                                </tr>
                                <tr>
                                    <td>PST ({{ $setting->pst_tax }}%)</td>
                                    <td class="text-end">
                                        @php
                                        $__pstTax = ($setting->pst_tax * ($total_price - ($order->coupon_discount??0))) / 100;
                                        @endphp
                                        {{ getDefaultCurrencySymbol() }}<span class="pst-amount" data-amount="{{ $__pstTax }}">{{ number_format($__pstTax, 2) }}</span>
                                        @php  $gtotalAmount += $__pstTax; @endphp
                                    </td>
                                </tr>

                                @if ($order->coupon_id)
                                    <tr>
                                        <td>Coupon Discount</td>
                                        <td class="text-end">
                                            - {{ getDefaultCurrencySymbol() }}
                                            {{ number_format($order->coupon_discount, 2) }}
                                        </td>
                                    </tr>
                                @endif

                                @if ($user->wallet_balance > 0)
                                    <tr>
                                        <td style="width: 50%"> <label for="use_wallet">Use Wallet Balance</label>
                                            ({{ getDefaultCurrencySymbol() . ' ' . $user->wallet_balance }})</td>
                                        <td>
                                            <div class="form-check form-switch position-absolute end-0">
                                                <input value="1" class="form-check-input" type="checkbox"
                                                    id="use_wallet" name="use_wallet" value="1">
                                                <label class="form-check-label" for="use_wallet"></label>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>Total</td>
                                    <td class="text-end">
                                        @php($gtotalAmount -= $order->coupon_discount)
                                        {{ getDefaultCurrencySymbol() }}<span
                                            data-amount="{{ (float) str_replace(',', '', $gtotalAmount) }}"
                                            class="est-total">{{ number_format($gtotalAmount, 2) }}</span>
                                    </td>
                                </tr>

                            </table>
                            @if (!$order->coupon_id)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header">
                                                <div class="card-title">Coupon</div>
                                            </div>
                                            <div class="card-body">
                                                <div action="{{ route('user.order.apply.coupon', $order->id) }}"
                                                    id="couponApplyForm">
                                                    @csrf
                                                    <div class="form-group">
                                                        <label for="">Enter Coupon Code</label>
                                                        <input type="text" class="form-control"
                                                            placeholder="Enter Promo Code" name="coupon_code"
                                                            id="coupon_code">
                                                    </div>
                                                    <div class="form-group text-center mt-2">
                                                        <button type="submit" id="couponApplyBtn"
                                                            class="btn btn-primary">Apply</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @if ($user->wallet_balance > 0)
                                <input type="hidden" name="used_wallet_balance" id="used_wallet_balance">
                            @endif
                            <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">
                            <input type="hidden" name="order_total_quantity" id="order_total_quantity"
                                {{-- value="{{ $order->cards->where('final_grading', '>', 0)->count() }}"> --}}
                                value="{{ $order->cards->count() }}">
                            <input type="hidden" name="order_total_price" id="order_total_price"
                                value="{{ $gtotalAmount }}">
                            <input type="hidden" name="old_total_price" id="old_total_price"
                                value="{{ $gtotalAmount }}">
                            <div class="form-check mt-3 toc-wrapper">
                                <input required name="toc-accept" class="form-check-input" type="checkbox"
                                    value="1" id="tocAgree">
                                <label class="form-check-label top-0 right-0 bg-transparent" for="tocAgree">
                                    <div class="ms-1">
                                        I have read and agree to the <a href="{{ route('frontend.terms') }}" target="_blank">Terms of
                                            Service
                                        </a>
                                    </div>
                                </label>
                            </div>
                            <div class="checkout_wrapper payment-method">
                                <div class="heading mt-4 mb-2">
                                    <h4>Payment Method</h4>
                                </div>
                                <div class="item_list">
                                    @if ($config[0]->config_value == '1')
                                        <!-- Stripe Option -->
                                        <div class="form-check mb-2 p-0 gap-1">
                                            <input class="form-check-input d-none" value="stripe"
                                                @checked(old('payment_mode', 'stripe') == 'stripe') type="radio" name="payment_mode"
                                                id="item_1">
                                            <label class="form-check-label p-3 rounded-4 border w-100" for="item_1">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon me-3 col-1">
                                                        <i class="fa-brands fa-cc-mastercard"></i>
                                                    </div>
                                                    <div class="col-9">
                                                        <div class="name fw-semibold">Credit Card</div>
                                                        <div class="info">Pay securely using your credit
                                                            card</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                        {{-- <p>
                                            Payment will be required once your order has been completed. You will be notified via
                                            email at time of completion.
                                        </p> --}}
                                    @endif
                                    @if ($config[1]->config_value == '1')
                                        <!-- PayPal Option -->
                                        <div class="form-check mb-2 p-0">
                                            <input class="form-check-input d-none" @checked(old('payment_mode', 'stripe') == 'paypal')
                                                value="paypal" type="radio" name="payment_mode" id="item_2">
                                            <label class="form-check-label p-3 rounded-4 border w-100" for="item_2">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon me-3 col-1">
                                                        <i class="fa-brands fa-paypal"></i>
                                                    </div>
                                                    <div class="col-9">
                                                        <div class="name fw-semibold">PayPal</div>
                                                        <div class="info">Pay securely using your PayPal account
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="">
                                <button type="submit" class="btn w-100 btn-primary py-3 mt-4">
                                    Pay For Order
                                </button>
                                <a 
                                {{-- href="{{ route('checkout.item.entry', $plan->id) }}" --}}
                                href="{{ route('user.orders') }}"

                                    class="btn btn-light py-3 w-100 mt-3" id="order_back">Back to Orders</a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="@if(!isset($has_error) || $has_error != 1) col-lg-8 @else col-lg-12 @endif order-lg-1">
                    <div class="checkout_wrapper px-xl-5">
                        @if(isset($has_error) && $has_error == 1)
                        <div class="heading mb-3">
                            <h4>Item List</h4>
                        </div>
                        <div class="item_table mb-4">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:50%;">Items(<span
                                                    {{-- class="card-item-count">{{ $order->cards->where('final_grading', '>', 0)->count() }}</span>) --}}
                                                    class="card-item-count">{{ $order->cards->count() }}</span>)
                                            </th>
                                            <th style="width:10%;">Qty.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-entry-wrapper">
                                        @foreach ($order->details as $key => $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <img src="{{ asset('images/placeholder.jpg') }}"
                                                                class="img-fluid" alt="">
                                                        </div>
                                                        <div>{{ $item->item_name }}</div>
                                                    </div>
                                                </td>
                                                <td>{{ $item->cards->count() }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

                        <div class="heading mb-4">
                            <h4>Shipping & Billing</h4>
                            <p>Complete your submission by providing shipment details.</p>
                        </div>
                        <div class="sipping_form row">
                            <div class="mb-3 col-md-6">
                                <label for="shippingName" class="form-label">Full Name</label>
                                <input name="shippingName"
                                    value="{{ $order->transaction->shipping_data['shippingName'] ?? $user->full_name }}"
                                    type="text" class="form-control" id="shippingName" placeholder="Enter your full name" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingAddress" class="form-label">Address</label>
                                <input name="shippingAddress"
                                    value="{{ $order->transaction->shipping_data['shippingAddress'] ?? $user->defaultAddress?->street . ' ' . $user->defaultAddress?->apt_unit }}"
                                    type="text" class="form-control" id="shippingAddress" placeholder="Enter your address" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCity" class="form-label">City</label>
                                <input name="shippingCity" 
                                    value="{{ $order->transaction->shipping_data['shippingCity'] ?? $user->defaultAddress?->city }}" 
                                    type="text" class="form-control" id="shippingCity" placeholder="Enter your city" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingState" class="form-label">State/Province</label>
                                <input name="shippingState" 
                                    value="{{ $order->transaction->shipping_data['shippingState'] ?? $user->defaultAddress?->state }}"
                                    type="text" class="form-control" id="shippingState" placeholder="Enter your state" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingZip" class="form-label">Postal Code</label>
                                <input name="shippingZip" 
                                    value="{{ $order->transaction->shipping_data['shippingZip'] ?? $user->defaultAddress?->zip_code }}"
                                    type="text" class="form-control" id="shippingZip" placeholder="Enter your zip code" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCountry" class="form-label">Country</label>
                                <select name="shippingCountry" class="form-control form-select" id="shippingCountry">
                                    <option @selected($order->transaction->shipping_data['shippingCountry'] == 'Canada') value="Canada">Canada</option>
                                    <option @selected($order->transaction->shipping_data['shippingCountry'] == 'United States') value="United States">United States</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingPhone" class="form-label">Phone Number</label> <br>
                                <input name="shippingPhone"
                                    value="{{ $user->dial_code ? '+' . $user->dial_code : '+1' }}{{ $user->phone ?? ($order->transaction->shipping_data['shippingPhone'] ?? '') }}"
                                    type="text" class="form-control w-100" id="shippingPhone"
                                    placeholder="Enter your phone number" required>
                                <input type="hidden" name="dial_code">
                            </div>

                            @if(isset($has_error) && $has_error == 1)
                            <div class="my-3 col-md-12 text-center">
                                <button type="submit" class="btn btn-primary">Update</button>
                            </div>
                            @endif
                        </div>
                        @if(!isset($has_error) || $has_error != 1)
                        <div class="heading mb-3 mt-4">
                            <h4>Item List</h4>
                        </div>
                        <div class="item_table">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:50%;">Items(<span
                                                    class="card-item-count">{{ $order->cards->count() }}</span>)
                                            </th>
                                            <th style="width:10%;">Qty.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-entry-wrapper">
                                        @foreach ($order->details as $key => $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <img src="{{ asset('images/placeholder.jpg') }}"
                                                                class="img-fluid" alt="">
                                                        </div>
                                                        <div>{{ $item->item_name }}</div>
                                                        {{-- <input type="hidden" name="year[]"
                                                                value="{{ $cartItems['year'][$loop->index] }}">
                                                            <input type="hidden" name="brand[]"
                                                                value="{{ $cartItems['brand'][$loop->index] }}">
                                                            <input type="hidden" name="cardNumber[]"
                                                                value="{{ $cartItems['cardNumber'][$loop->index] }}">
                                                            <input type="hidden" name="playerName[]"
                                                                value="{{ $cartItems['playerName'][$loop->index] }}">
                                                            <input type="hidden" name="notes[]"
                                                                value="{{ $cartItems['notes'][$loop->index] }}">
                                                            <input type="hidden" name="quantity[]"
                                                                value="{{ $cartItems['quantity'][$loop->index] }}"> --}}
                                                    </div>
                                                </td>
                                                <td>{{ $item->cards->count() }}</td>
                                                {{-- <td>
                                                        <div class="d-flex align-items-center">
                                                            <a href="#"
                                                                class="btn btn-outline-light btn-xs rounded-pill p-2 me-2 edit-item">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="#212121" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path
                                                                        d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                                    <path d="M13.5 6.5l4 4" />
                                                                </svg>
                                                            </a>
                                                            <a href="#"
                                                                class="btn btn-outline-danger border-light btn-xs rounded-pill p-2 delete-item">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="24"
                                                                    height="24" viewBox="0 0 24 24" fill="none"
                                                                    stroke="currentColor" stroke-width="2"
                                                                    stroke-linecap="round" stroke-linejoin="round"
                                                                    class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                                    <path stroke="none" d="M0 0h24v24H0z"
                                                                        fill="none" />
                                                                    <path d="M4 7l16 0" />
                                                                    <path d="M10 11l0 6" />
                                                                    <path d="M14 11l0 6" />
                                                                    <path
                                                                        d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                                    <path
                                                                        d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                                </svg>
                                                            </a>
                                                        </div>
                                                    </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif
                        {{-- <div class="heading mb-4 mt-4">
                            <h4>Payment Method</h4>
                        </div>
                        <div class="item_list">    
                            @if ($config[0]->config_value == '1')
                                <div class="form-check mb-2 p-0 gap-1">
                                    <input class="form-check-input d-none" value="stripe" @checked(old('payment_mode', 'stripe') == 'stripe')
                                        type="radio" name="payment_mode" id="item_1">
                                    <label class="form-check-label p-3 rounded-4 border w-100" for="item_1">
                                        <div class="d-flex align-items-center">
                                            <div class="icon me-3">
                                                <i class="fa-brands fa-stripe"></i>
                                            </div>
                                            <div>
                                                <div class="name fw-semibold">Stripe</div>
                                                <div class="info">Pay securely using your credit or debit card</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endif
                            @if ($config[1]->config_value == '1')
                            <div class="form-check mb-2 p-0">
                                <input class="form-check-input d-none" @checked(old('payment_mode', 'stripe') == 'paypal') value="paypal"
                                    type="radio" name="payment_mode" id="item_2">
                                <label class="form-check-label p-3 rounded-4 border w-100" for="item_2">
                                    <div class="d-flex align-items-center">
                                        <div class="icon me-3">
                                            <i class="fa-brands fa-paypal"></i>
                                        </div>
                                        <div>
                                            <div class="name fw-semibold">PayPal</div>
                                            <div class="info">Pay securely using your PayPal account</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endif
                        </div> --}}
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!--======================= checkout end ============================ -->

@endsection

@push('script')
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/intlTelInput.min.js"></script>
<script>
    const input = document.querySelector("#shippingPhone");
    var iti = window.intlTelInput(input, {
        initialCountry: "{{ $order->transaction->shipping_data['shippingCountry'] == 'Canada' ? 'ca' : 'usa' }}",
        separateDialCode: true,
        autoPlaceholder: false,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
    });

    // Function to get the dial code
    function getDialCode() {
        const countryData = iti.getSelectedCountryData();
        if (countryData.dialCode === "1" && countryData.iso2 !== "ca" && "{{$order->transaction->shipping_data['shippingCountry']}}" == 'Canada') {
            iti.setCountry("ca");
            countryData.name = 'Canada';
        }
        $('#shippingCountry').val(countryData.name)
        $('input[name="dial_code"]').val(countryData.dialCode);
    }
    getDialCode();

    // Example of using it when the input changes
    input.addEventListener('countrychange', function() {
        getDialCode();
    });

    $(document).on('click', '#couponApplyBtn', function(e) {
        e.preventDefault();
        var coupon_code = $('#coupon_code').val();
        $('#couponApplyForm').find('input,button').attr('disabled', true);
        $.ajax({
            url: "{{ route('user.order.apply.coupon', $order->id) }}",
            type: 'POST',
            data: {
                coupon_code: coupon_code,
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success == true) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        location.reload();
                    }, 3000);
                } else {
                    toastr.error(response.message);
                    $('#couponApplyForm').find('input,button').attr('disabled', false);
                }
            }
        });
    });

    $(document).on('click input', '#has_insurance,#insurance_value', function() {
        if ($(this).attr('id') == 'has_insurance') {
            if ($(this).is(':checked')) {
                $('.insurance-error-msg').hide();
                $('.insurance_input').show();
                $('#insurance_value').attr('required', true)
            } else {
                $('.insurance-error-msg').show();
                $('.insurance_input').hide();
                $('#insurance_value').attr('required', false)
            }
        }
        adjustTotalPrice();

    });

    function getInsurancePrice() {
        var insurance_value = parseFloat($('#insurance_value').val());
        var insurance_amount = 0;
        if (insurance_value > 0) {
            insurance_amount = Math.ceil(insurance_value / 100) * 1.99;
        }
        $('.insurance-amount').text(insurance_amount.toFixed(2));
        return insurance_amount;
    }

    function adjustTotalPrice() {
        var item_total = parseFloat("{{ $total_price }}");
        var shipping_charge = parseFloat($('#shipping_charge').val());
        var total_amount = item_total + shipping_charge;

        var gst_amount = parseFloat($('.gst-amount').data('amount'));
        var pst_amount = parseFloat($('.pst-amount').data('amount'));
        var total = parseFloat($('.est-total').data('amount'));

        const gstTax = {{ $setting->gst_tax }};
        const pstTax = {{ $setting->pst_tax }};
        const couponDiscount = {{ $order->coupon_discount ?? 0 }};

        if ($('#use_wallet').is(':checked')) {
            var wallet_balance = parseFloat("{{ $user->wallet_balance }}");
            if (wallet_balance >= total_amount) {
                $('#used_wallet_balance').val(total_amount);
                total = (0.00);
                $('.gst-amount').text(0.00);
                $('.pst-amount').text(0.00);

                $('.payment-method').hide();
            } else {
                total = total_amount - wallet_balance;

                const taxableAmount = total - couponDiscount;
                gst_amount = (gstTax * taxableAmount) / 100;
                pst_amount = (pstTax * taxableAmount) / 100;

                $('.gst-amount').text(gst_amount.toFixed(2));
                $('.pst-amount').text(pst_amount.toFixed(2));

                total = total + gst_amount + pst_amount;

                $('#used_wallet_balance').val(wallet_balance);
            }
        } else {
            $('.payment-method').show();
        }

        if ($('#has_insurance').is(':checked')) {
            var insurance_value = parseFloat($('#insurance_value').val());
            var insurance_amount = 0;
            insurance_amount = getInsurancePrice();
            total += insurance_amount;
            order_total_price += insurance_amount;
        }
        $('.est-total').text(total.toFixed(2));
        $('.gst-amount').text(gst_amount.toFixed(2));
        $('.pst-amount').text(pst_amount.toFixed(2));
        $('#order_total_price').val(total);

    }
    $(document).on('click', '#use_wallet', function() {
        adjustTotalPrice();
    });
    $(document).on('change', '#shipping_method', function() {
        var intiToastr = toastr.info("Updating Shipping Method")
        $('body').css('cursor', 'wait');
        $.ajax({
                url: '{{ route('user.update.shipping.method', $order->id) }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                data: {
                    'shipping_method': $(this).val()
                },
            })
            .done(function() {
                intiToastr.hide()
                toastr.success("Shipping Method updated, reloading this page")
                window.location.reload();
            })
            .fail(function() {
                console.log("error");
            })
            .always(function() {
               
                console.log("complete");
            });

    });
</script>
@endpush

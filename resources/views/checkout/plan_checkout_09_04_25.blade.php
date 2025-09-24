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
    </style>
@endpush

@php
    $available_card_limit = 0;
   
@endphp

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Checkout') }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= checkout start  ============================ -->
<div class="checkout-sec pb-5">
    <div class="container">
        <div class="mb-3 mb-lg-5">
            <div class="checkout_steps text-center">
                @include('checkout.plan_steps')
            </div>
        </div>
        <form action="{{ route('payment.plan.store') }}" method="post" id="submit_order">
            @csrf
            <input type="hidden" name="plan_id" id="plan_id" value="{{$plan->id}}">
            <div class="row gy-4 gy-lg-0">
                <div class="col-lg-12">
                    <div class="px-xl-5 mb-4">
                        <div class="heading mb-1">
                            <span class="pb-0" style="font-size: 22px; font-weight: 600; color: #034ea1;">{{$plan->name}}</span>
                        </div>
                        <span class="subtitle" style="font-size: 14px; font-weight: 500; color: #4f4d4d;">
                            Get {{$plan->subscription_peryear_card}} cards graded every year for {{$plan->subscription_year}} years for  {{ getDefaultCurrencySymbol() }}{{ $plan->price }}
                        </span>
                    </div> 
                </div>
                <div class="col-lg-4 order-lg-2">
                    <div class="checkout_sidebar rounded rounded-4 border-light border-light-subtle card border-1 p-4 sticky-top"
                        style="top:1rem;">
                        <div class="heading d-flex justify-content-between align-items-center mb-3">
                            <h4 class="pb-0">Summary</h4>
                        </div>
                        <div class="sidebar_box">
                            <table class="table table-bordered">
                                <tr>
                                    <td>Plan</td>
                                    <td class="text-end">{{$plan->name}}</td>
                                </tr>
                                {{-- <tr>
                                    <td>Price</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{$plan->price}}
                                    </td>
                                </tr> --}}
                                @php
                                    $total_price = number_format($plan->price, 2);
                                    $gtotalAmount = 0;
                                @endphp
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format($plan->price,2) }}
                                        @php  $gtotalAmount += $total_price; @endphp
                                    </td>
                                </tr>
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
                                @if (session('checkout.coupon'))    
                                <tr>
                                    <td>Coupon Discount</td>
                                    <td class="text-end">
                                        - {{ getDefaultCurrencySymbol() }} {{ number_format(session('checkout.coupon.discount_amount'),2) }}
                                    </td>
                                </tr>
                                @endif
                                @if($user->wallet_balance > 0)
                                <tr>
                                    <td style="width: 50%"> <label for="use_wallet">Use Wallet Balance</label> ({{ getDefaultCurrencySymbol().' '.$user->wallet_balance }})</td>
                                    <td class="text-end">
                                        <input type="checkbox" name="use_wallet" id="use_wallet" value="1">
                                    </td>
                                </tr>
                                @endif
                                <tr>
                                    <td>Estimated Total</td>
                                    <td class="text-end">
                                        @php($gtotalAmount -= session('checkout.coupon.discount_amount'))
                                        {{ getDefaultCurrencySymbol() }}<span data-amount="{{ $gtotalAmount }}" class="est-total">{{ number_format($gtotalAmount, 2) }}</span>
                                    </td>
                                </tr>
                            </table>
                            @if (!session('checkout.coupon'))    
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <div class="card-title">Coupon</div>
                                        </div>
                                        <div class="card-body">
                                            <div action="{{ route('checkout.plan.apply.coupon',$plan->id) }}" id="couponApplyForm">
                                                @csrf
                                                <div class="form-group">
                                                    <label for="">Enter Coupon Code</label>
                                                    <input type="text" class="form-control" placeholder="Enter Promo Code" name="coupon_code" id="coupon_code">
                                                </div>
                                                <div class="form-group text-center mt-2">
                                                    <button type="button" id="couponApplyBtn" class="btn btn-primary">Apply</button>
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
                            <input type="hidden" name="order_total_price" id="order_total_price" value="{{$gtotalAmount}}">
                            <input type="hidden" name="old_total_price" id="old_total_price" value="{{$gtotalAmount}}">
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
                            @if($plan->type == 'subscription')
                            <div class="checkout_wrapper payment-method">
                                <div class="heading mt-4 mb-2">
                                    <h4>Payment Method</h4>
                                </div>
                                <div class="item_list">    
                                    @if ($config[0]->config_value == '1')
                                        <!-- Stripe Option -->
                                        <div class="form-check mb-2 p-0 gap-1">
                                            <input class="form-check-input d-none" value="stripe" @checked(old('payment_mode', 'stripe') == 'stripe')
                                                type="radio" name="payment_mode" id="item_1">
                                            <label class="form-check-label p-3 rounded-4 border w-100" for="item_1">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon me-3 col-1">
                                                        <i class="fa-brands fa-cc-mastercard"></i>
                                                    </div>
                                                    <div class="col-9">
                                                        <div class="name fw-semibold">Credit Card</div>
                                                        <div class="info">Pay securely using your credit or debit card</div>
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
                                        <input class="form-check-input d-none" @checked(old('payment_mode', 'stripe') == 'paypal') value="paypal"
                                            type="radio" name="payment_mode" id="item_2">
                                        <label class="form-check-label p-3 rounded-4 border w-100" for="item_2">
                                            <div class="d-flex align-items-center">
                                                <div class="icon me-3 col-1">
                                                    <i class="fa-brands fa-paypal"></i>
                                                </div>
                                                <div class="col-9">
                                                    <div class="name fw-semibold">PayPal</div>
                                                    <div class="info">Pay securely using your PayPal account</div>
                                                </div>
                                            </div>
                                        </label>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                            <div class="">
                                <button type="submit" class="btn w-100 btn-primary py-3 mt-4" id="submit_order_btn">
                                    Submit Order
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8 order-lg-1">
                    <div class="checkout_wrapper px-xl-5">
                        <div class="heading mb-4">
                            <h4>Shipping & Billing</h4>
                            <p>Complete your submission by providing shipment details.</p>
                        </div>
                        <div class="sipping_form row">
                            <div class="mb-3 col-md-6">
                                <label for="shippingName" class="form-label">Full Name <span>*</span></label>
                                <input name="shippingName"
                                    value="{{ $user->defaultAddress ? $user->defaultAddress->first_name . ' ' . $user->defaultAddress->last_name : $user->full_name }}"
                                    type="text" class="form-control" id="shippingName"
                                    placeholder="Enter your full name" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingAddress" class="form-label">Address <span>*</span></label>
                                <input name="shippingAddress"
                                    value="{{ $user->defaultAddress?->street . ' ' . $user->defaultAddress?->apt_unit }}"
                                    type="text" class="form-control" id="shippingAddress"
                                    placeholder="Enter your address" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCity" class="form-label">City <span>*</span></label>
                                <input name="shippingCity" value="{{ $user->defaultAddress?->city }}" type="text"
                                    class="form-control" id="shippingCity" placeholder="Enter your city" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingState" class="form-label">State/Province <span>*</span></label>
                                <input name="shippingState" value="{{ $user->defaultAddress?->state }}" type="text"
                                    class="form-control" id="shippingState" placeholder="Enter your state" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingZip" class="form-label">Postal Code <span>*</span></label>
                                <input name="shippingZip" value="{{ $user->defaultAddress?->zip_code }}" type="text"
                                    class="form-control" id="shippingZip" placeholder="Enter your zip code" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCountry" class="form-label">Country <span>*</span></label>
                                <input name="shippingCountry" value="{{ $user->defaultAddress?->country }}"
                                    type="text" class="form-control" id="shippingCountry"
                                    placeholder="Enter your country" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingPhone" class="form-label">Phone Number <span>*</span></label> <br>
                                <input name="shippingPhone"
                                    value="{{ $user->dial_code ? '+'.$user->dial_code : '' }}{{ $user->phone ?? '' }}"
                                    type="text" class="form-control w-100" id="shippingPhone"
                                    placeholder="Enter your phone number" required>
                                <input type="hidden" name="dial_code">
                            </div>
                        </div>
                        {{-- @if($plan->type == 'subscription')
                            <div class="heading mb-4 mt-4">
                                <h4>Payment Method</h4>
                            </div>
                            <div class="item_list">
                                
                                @if ($config[0]->config_value == '1')
                                    <!-- Stripe Option -->
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
                                <!-- PayPal Option -->
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
                            </div>
                        @endif --}}
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
        initialCountry: "ca",
        separateDialCode: true,
        autoPlaceholder: false,
        utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
    });

    // Function to get the dial code
    function getDialCode() {
        const countryData = iti.getSelectedCountryData();
        if (countryData.dialCode === "1" && countryData.iso2 !== "ca") {
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
            url: $('#couponApplyForm').attr('action'),
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
                    $('#couponApplyForm').find('input,button').removeAttr('disabled');
                }
            }
        });
    });

    $(document).on('click', '#use_wallet', function() {

        var item_total = parseFloat("{{ $total_price }}");
        var total_amount = item_total;

        var gst_amount = parseFloat($('.gst-amount').data('amount'));
        var pst_amount = parseFloat($('.pst-amount').data('amount'));
        var total = parseFloat($('.est-total').data('amount'));
        
        const gstTax = {{ $setting->gst_tax }};
        const pstTax = {{ $setting->pst_tax }};
        const couponDiscount = {{ $order->coupon_discount ?? 0 }};

        if ($(this).is(':checked')) {
            var wallet_balance = parseFloat("{{ $user->wallet_balance }}");
            if (wallet_balance >= total) {
                $('#used_wallet_balance').val(total);
                $('.est-total').text('0.00');
                $('#order_total_price').val(0.00);
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
                $('.est-total').text(total.toFixed(2));
                $('#order_total_price').val(total);
            }
        } else {
            $('.est-total').text(total.toFixed(2));
            $('.gst-amount').text(gst_amount.toFixed(2));
            $('.pst-amount').text(pst_amount.toFixed(2));
            $('#order_total_price').val(total);
            $('.payment-method').show();
        }
    });
</script>
@endpush

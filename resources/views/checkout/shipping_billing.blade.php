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
                @include('checkout.steps')
            </div>
        </div>
        @if(isset($order_id) && !empty($order_id))
        <form action="{{ route('payment.update') }}" method="post" id="submit_order">
            @csrf
            <input type="hidden" name="order_id" id="order_id" value="{{$order_id}}">
        @else
        <form action="{{ route('payment.store') }}" method="post" id="submit_order">
            @csrf
        @endif
            <div class="row gy-4 gy-lg-0">
                <div class="col-lg-4 order-lg-2">
                    <div class="checkout_sidebar rounded rounded-4 border-light border-light-subtle card border-1 p-4 sticky-top"
                        style="top:1rem;">
                        <div class="heading d-flex justify-content-between align-items-center mb-3">
                            <h4 class="pb-0">Summary</h4>
                            @if($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now())
                                @php
                                    $available_card_limit = $user->getAvailableCardLimit();
                                @endphp
                                @if($available_card_limit > 0)
                                    <span class="badge bg-info">You have {{$available_card_limit}} FREE card(s) remaining this year</span>
                                @endif
                            @endif
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
                                @if($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now() && $available_card_limit > 0)
                                    @if(($total_cards - $available_card_limit) > 0)
                                        @php
                                            $items_count = $total_cards - $available_card_limit;
                                            $gtotalAmount = 0;
                                        @endphp
                                        <tr>
                                            <td>Price</td>
                                            <td class="text-end">
                                                {{ getDefaultCurrencySymbol() }}{{ session('checkout.total_unit_price') }}/item
                                            </td>
                                        </tr>
                                        
                                        <tr>
                                            <td>Items x {{ $items_count }}</td>
                                            <td class="text-end">
                                                {{ getDefaultCurrencySymbol() }}{{ number_format(session('checkout.total_unit_price') * $items_count, 2) }}
                                            </td>
                                        </tr>

                                        @php
                                            $totalAmount = session('checkout.total_unit_price') * $items_count;
                                        @endphp
                                        <tr>
                                            <td>Subtotal</td>
                                            <td class="text-end">
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($totalAmount, 2) }}
                                                @php  $gtotalAmount += $totalAmount; @endphp
                                            </td>
                                        </tr>
                                        <tr data-total_amount="{{ $gtotalAmount }}" class="gst_tax" style="display: none;">
                                            <td>
                                                GST (<span class="gst_tax_percentage">0</span>%)
                                            </td>
                                            <td class="text-end">
                                                {{ getDefaultCurrencySymbol() }}<span class="gst_tax_amount">0.00</span>
                                            </td>
                                        </tr>
                                
                                        <tr data-total_amount="{{ $gtotalAmount }}" class="pst_tax" style="display: none;">
                                            <td>
                                                PST (<span class="pst_tax_percentage">0</span>%)
                                            </td>
                                            <td class="text-end">
                                                {{ getDefaultCurrencySymbol() }}<span class="pst_tax_amount">0.00</span>
                                            </td>
                                        </tr>
                                
                                        <tr>
                                            <td>Total</td>
                                            <td class="text-end">
                                                {{ getDefaultCurrencySymbol() }}<span class="grand_total_amount">{{ number_format($gtotalAmount, 2) }}</span>
                                            </td>
                                        </tr>
                                    @endif
                                @else
                                    <tr>
                                        <td>Price</td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}{{ session('checkout.total_unit_price') }}/item
                                        </td>
                                    </tr>
                                    @php
                                        $items_count = $total_cards;
                                        $gtotalAmount = 0;
                                    @endphp
                                    <tr>
                                        <td>Items x {{ $items_count }}</td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}{{ number_format(session('checkout.total_unit_price') * $items_count, 2) }}
                                        </td>
                                    </tr>

                                    @php
                                        $totalAmount = $available_card_limit > 0 
                                            ? session('checkout.total_unit_price') * $items_count - session('checkout.total_unit_price') * $available_card_limit
                                            : session('checkout.total_unit_price') * $items_count;
                                    @endphp
                                    <tr>
                                        <td>Subtotal</td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}{{ number_format($totalAmount, 2) }}
                                            @php  $gtotalAmount += $totalAmount; @endphp
                                        </td>
                                    </tr>
                                    <tr data-total_amount="{{ $gtotalAmount }}" class="gst_tax" style="display: none;">
                                        <td>
                                            GST (<span class="gst_tax_percentage">0</span>%)
                                        </td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}<span class="gst_tax_amount">0.00</span>
                                        </td>
                                    </tr>
                            
                                    <tr data-total_amount="{{ $gtotalAmount }}" class="pst_tax" style="display: none;">
                                        <td>
                                            PST (<span class="pst_tax_percentage">0</span>%)
                                        </td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}<span class="pst_tax_amount">0.00</span>
                                        </td>
                                    </tr>
                            
                                    <tr>
                                        <td>Total</td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}<span class="grand_total_amount">{{ number_format($gtotalAmount, 2) }}</span>
                                        </td>
                                    </tr>
                                @endif
                            </table>
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
                            {{-- @if($plan->type == 'subscription')
                            <div class="checkout_wrapper">
                                <div class="heading mt-4 mb-2">
                                    <h4>Payment Method</h4>
                                </div>
                                <div class="item_list">    
                                    @if ($config[0]->config_value == '1')
                                        <div class="form-check mb-2 p-0 gap-1">
                                            <input class="form-check-input d-none" value="stripe" @checked(old('payment_mode', 'stripe') == 'stripe')
                                                type="radio" name="payment_mode" id="item_1">
                                            <label class="form-check-label p-3 rounded-4 border w-100" for="item_1">
                                                <div class="d-flex align-items-center">
                                                    <div class="icon me-3 col-1">
                                                        <i class="fa-brands fa-stripe"></i>
                                                    </div>
                                                    <div class="col-9">
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
                            @endif --}}
                            <div class="">
                                <button type="submit" class="btn w-100 btn-primary py-3 mt-4" id="submit_order_btn">
                                    Submit Order
                                </button>
                                @if(session()->has('checkout.order_edit') && !empty(session('checkout.order_edit')))
                                    <a href="{{ route('user.order.edit', parameters: session('checkout.order_id')) }}"
                                        class="btn btn-light py-3 w-100 mt-3" id="order_back">Back to Item Entry</a>
                                @else
                                    <a href="{{ route('checkout.item.entry', $plan->id) }}"
                                        class="btn btn-light py-3 w-100 mt-3" id="order_back">Back to Item Entry</a>
                                @endif
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
                                <label for="shippingCountry" class="form-label">Country <span>*</span></label>
                                <select name="shippingCountry" class="form-control form-select" id="shippingCountry" required>
                                    <option @selected($user->defaultAddress?->country=='Canada') value="Canada">Canada</option>
                                    <option @selected($user->defaultAddress?->country=='United States') value="United States">United States</option>
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingState" class="form-label">State/Province <span>*</span></label>
                                {{-- <input name="shippingState" value="{{ $user->defaultAddress?->state }}" type="text"
                                    class="form-control" id="shippingState" placeholder="Enter your state" required> --}}

                                @php
                                    $states = getStateCodeMap(ucwords($user->defaultAddress?->country ?? 'Canada')) ?? '[]';
                                @endphp
                                <select name="shippingState" class="form-control" id="shippingState" required>
                                    <option value="">Select State/Province</option>
                                    @foreach ($states as $key => $state)
                                        <option @selected($user->defaultAddress?->state == ucwords($key)) value="{{ ucwords($key) }}">{{ ucwords($key) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCity" class="form-label">City <span>*</span></label>
                                <input name="shippingCity" value="{{ $user->defaultAddress?->city }}" type="text"
                                    class="form-control" id="shippingCity" placeholder="Enter your city" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingAddress" class="form-label">Address <span>*</span></label>
                                <input name="shippingAddress"
                                    value="{{ $user->defaultAddress?->street }}"
                                    type="text" class="form-control" id="shippingAddress"
                                    placeholder="Enter your address" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingZip" class="form-label">Postal Code <span>*</span></label>
                                <input name="shippingZip" value="{{ $user->defaultAddress?->zip_code }}" type="text"
                                    class="form-control" id="shippingZip" placeholder="Enter your zip code" required>
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
                        @if($plan->type != 'subscription' || count(session('checkout.items')['year']) != 0)
                        <div class="heading mb-3 mt-4">
                            <h4>Item List</h4>
                        </div>
                        <div class="item_table">
                            <div class="table-responsive">
                                <table class="table align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:50%;">Items(<span
                                                    class="card-item-count">{{ session('checkout.items') ? count(session('checkout.items')['year']) : 0 }}</span>)
                                            </th>
                                            <th style="width:10%;">Qty.</th>
                                        </tr>
                                    </thead>
                                    <tbody id="item-entry-wrapper">
                                        @session('checkout.items')
                                            @php($cartItems = session('checkout.items'))
                                            @foreach ($cartItems['year'] as $key => $item)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            {{-- <div class="me-3">
                                                                <img src="{{ asset('images/placeholder.jpg') }}"
                                                                    class="img-fluid" alt="Image">
                                                            </div> --}}
                                                            <div>{{ $cartItems['year'][$loop->index] }}
                                                                {{ $cartItems['brand'][$loop->index] }}
                                                                {{ $cartItems['cardNumber'][$loop->index] }}
                                                                {{ $cartItems['playerName'][$loop->index] }}</div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $cartItems['quantity'][$loop->index] }}</td>
                                                </tr>
                                            @endforeach
                                        @endsession
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @endif

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
    function ucwords(str) {
        return str.replace(/\b\w/g, function(char) {
            return char.toUpperCase();
        });
    }
    var states = {
        "Canada" : @json(getStateCodeMap('Canada')),
        "United States" : @json(getStateCodeMap('United States'))   
    };
    $(document).on('change', '#shippingCountry', function() {
        const country = $(this).val();
        const stateSelect = $('#shippingState');
        stateSelect.empty();
        currentStates = states[country];
        stateSelect.append(`<option value="">Select State/Province</option>`);
        $.each(currentStates, function(key, value) {
            stateSelect.append(`<option value="${ucwords(key)}">${ucwords(key)}</option>`);
        });
    });
    const input = document.querySelector("#shippingPhone");
    var iti = window.intlTelInput(input, {
        initialCountry: "ca",
        separateDialCode: true,
        autoPlaceholder: false,
        onlyCountries: ["ca", "us"], // Allow only Canada and USA
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
</script>
<script>
    function updateTaxesForState(state) {
        const totalAmount = parseFloat(document.querySelector('.gst_tax').dataset.total_amount || 0);

        if (state) {
            fetch(`/get-state-taxes/${state}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    // Show and populate GST
                    const gstAmount = (data.gst * totalAmount) / 100;
                    document.querySelector('.gst_tax_percentage').textContent = data.gst;
                    document.querySelector('.gst_tax_amount').textContent = gstAmount.toFixed(2);
                    document.querySelector('.gst_tax').style.display = 'table-row';

                    // Show and populate PST
                    const pstAmount = (data.pst * totalAmount) / 100;
                    document.querySelector('.pst_tax_percentage').textContent = data.pst;
                    document.querySelector('.pst_tax_amount').textContent = pstAmount.toFixed(2);
                    document.querySelector('.pst_tax').style.display = 'table-row';

                    // Update Grand Total
                    const grandTotal = totalAmount + gstAmount + pstAmount;
                    document.querySelector('.grand_total_amount').textContent = grandTotal.toFixed(2);
                });
        } else {
            document.querySelector('.gst_tax').style.display = 'none';
            document.querySelector('.pst_tax').style.display = 'none';
        }
    }

    document.getElementById('shippingState').addEventListener('change', function () {
        updateTaxesForState(this.value);
    });

    // Trigger on page load for initially selected state
    document.addEventListener('DOMContentLoaded', function () {
        const initialState = document.getElementById('shippingState').value;
        if (initialState) {
            updateTaxesForState(initialState);
        }
    });
</script>
@endpush

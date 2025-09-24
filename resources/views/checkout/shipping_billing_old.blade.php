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
        <form action="{{ route('payment.store') }}" method="post">
            @csrf
            <div class="row gy-4 gy-lg-0">
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
                                <tr>
                                    <td>Price</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ session('checkout.total_unit_price') }}/item
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>Max Decl. Value</td>
                                    <td class="text-end">$1,500/item</td>
                                </tr> --}}
                                @php
                                    $items_count = $total_cards;
                                @endphp
                                <tr>
                                    <td>Items x {{ $items_count }}</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(session('checkout.total_unit_price') * $items_count, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(session('checkout.total_unit_price') * $items_count, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estimated Total</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(session('checkout.total_unit_price') * $items_count, 2) }}
                                    </td>
                                </tr>
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
                            <div class="">
                                <button type="submit" class="btn w-100 btn-primary py-3 mt-4">
                                    Review Order
                                </button>
                                <a href="{{ route('checkout.item.entry', $plan->id) }}"
                                    class="btn btn-light py-3 w-100 mt-3">Back to Item
                                    Entry</a>
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
                                <label for="shippingName" class="form-label">Full Name</label>
                                <input name="shippingName"
                                    value="{{ $user->defaultAddress ? $user->defaultAddress->first_name . ' ' . $user->defaultAddress->last_name : $user->full_name }}"
                                    type="text" class="form-control" id="shippingName"
                                    placeholder="Enter your full name" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingAddress" class="form-label">Address</label>
                                <input name="shippingAddress"
                                    value="{{ $user->defaultAddress?->street . ' ' . $user->defaultAddress?->apt_unit }}"
                                    type="text" class="form-control" id="shippingAddress"
                                    placeholder="Enter your address" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCity" class="form-label">City</label>
                                <input name="shippingCity" value="{{ $user->defaultAddress?->city }}" type="text"
                                    class="form-control" id="shippingCity" placeholder="Enter your city" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingState" class="form-label">State/Province</label>
                                <input name="shippingState" value="{{ $user->defaultAddress?->state }}" type="text"
                                    class="form-control" id="shippingState" placeholder="Enter your state" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingZip" class="form-label">Postal Code</label>
                                <input name="shippingZip" value="{{ $user->defaultAddress?->zip_code }}" type="text"
                                    class="form-control" id="shippingZip" placeholder="Enter your zip code" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingCountry" class="form-label">Country</label>
                                <input name="shippingCountry" value="{{ $user->defaultAddress?->country }}"
                                    type="text" class="form-control" id="shippingCountry"
                                    placeholder="Enter your country" required>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="shippingPhone" class="form-label">Phone Number</label> <br>
                                <input name="shippingPhone"
                                    value="{{ $user->dial_code ? '+'.$user->dial_code : '+1' }}{{ $user->phone ?? '' }}"
                                    type="text" class="form-control w-100" id="shippingPhone"
                                    placeholder="Enter your phone number" required>
                                <input type="hidden" name="dial_code">
                            </div>
                        </div>
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
        $('#shippingCountry').val(countryData.name)
        $('input[name="dial_code"]').val(countryData.dialCode);
    }
    getDialCode();

    // Example of using it when the input changes
    input.addEventListener('countrychange', function() {
        getDialCode();
    });
</script>
@endpush

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
                            @php
                                $basePrice = $plan->price;
                                $couponDiscount = session('checkout.plan.coupon.discount_amount') ?? 0;
                                $subtotal = $basePrice - $couponDiscount;
                            @endphp
                        
                            <table class="table table-bordered">
                                <tr>
                                    <td>Plan</td>
                                    <td class="text-end">{{ $plan->name }}</td>
                                </tr>
                            
                                <tr>
                                    <td>Subtotal</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format($plan->price, 2) }}
                                    </td>
                                </tr>
                                <tr class="gst_row" style="display: none;" data-subtotal="{{ $subtotal }}">
                                    <td>GST (<span class="gst_tax_percentage">0</span>%)</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}<span class="gst-amount">0.00</span>
                                    </td>
                                </tr>
                            
                                <tr class="pst_row" style="display: none;" data-subtotal="{{ $subtotal }}">
                                    <td>PST (<span class="pst_tax_percentage">0</span>%)</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}<span class="pst-amount">0.00</span>
                                    </td>
                                </tr>
                            
                                @if (session('checkout.plan.coupon'))
                                <tr>
                                    <td>Coupon Discount</td>
                                    <td class="text-end">
                                        - {{ getDefaultCurrencySymbol() }}{{ number_format($couponDiscount, 2) }}
                                    </td>
                                </tr>
                                @endif
                            
                                @if($user->wallet_balance > 0)
                                <tr>
                                    <td>Use Wallet Balance ({{ getDefaultCurrencySymbol() }}{{ number_format($user->wallet_balance,2) }})</td>
                                    <td class="text-end">
                                        <input type="checkbox" name="use_wallet" id="use_wallet" value="1">
                                    </td>
                                </tr>
                                @endif
                                <tr class="wallet_discount_div" style="display: none;">
                                    <td>Discount Applied</td>
                                    <td class="text-end">
                                        - {{ getDefaultCurrencySymbol() }}
                                        <span class="wallet_discount"></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Estimated Total</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}<span class="est-total" data-base="{{ $subtotal }}">{{ number_format($subtotal, 2) }}</span>
                                    </td>
                                </tr>
                            </table>
                            @if (!session('checkout.plan.coupon'))    
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
                            <input type="hidden" name="order_total_price" id="order_total_price" value="{{$subtotal}}">
                            <input type="hidden" name="old_total_price" id="old_total_price" value="{{$subtotal}}">
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
                                <?php
                                    $states = getStateCodeMap(ucwords($user->defaultAddress?->country ?? 'Canada')) ?? '[]';
                                ?>
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
                                    value="{{ $user->defaultAddress?->street . ' ' . $user->defaultAddress?->apt_unit }}"
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
</script>
<script>
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
</script>
<script>
    function fetchAndUpdatePlanTaxes(state) {
        const subtotal = parseFloat(document.querySelector('.gst_row')?.dataset.subtotal || 0);
        let total = subtotal;

        if (!state) return;

        fetch(`/get-state-taxes/${state}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) return;

                const gstRate = parseFloat(data.gst || 0);
                const pstRate = parseFloat(data.pst || 0);
                const gstAmount = Math.max(0, (gstRate * subtotal) / 100);
                const pstAmount = Math.max(0, (pstRate * subtotal) / 100);

                document.querySelector('.gst_tax_percentage').textContent = gstRate;
                document.querySelector('.gst-amount').textContent = gstAmount.toFixed(2);
                document.querySelector('.gst_row').style.display = 'table-row';

                document.querySelector('.pst_tax_percentage').textContent = pstRate;
                document.querySelector('.pst-amount').textContent = pstAmount.toFixed(2);
                document.querySelector('.pst_row').style.display = 'table-row';

                total += gstAmount + pstAmount;
                total = Math.max(0, total);

                updateEstimatedPlanTotal(total);

                if (document.getElementById('use_wallet')?.checked) {
                    applyPlanWalletLogic();
                }
            });
    }

    function updateEstimatedPlanTotal(amount) {
        const totalElement = document.querySelector('.est-total');
        totalElement.textContent = amount.toFixed(2);
        totalElement.dataset.final = amount;
        document.getElementById('order_total_price').value = amount.toFixed(2);
    }

    function applyPlanWalletLogic() {
        const walletBox = document.getElementById('use_wallet');
        if (!walletBox || !walletBox.checked) return;

        let subtotal = parseFloat(document.querySelector('.gst_row')?.dataset.subtotal || 0);
        const walletBalance = parseFloat("{{ $user->wallet_balance }}");
        const state = document.getElementById('shippingState')?.value || '';

        let usedWallet = Math.min(walletBalance, subtotal);
        subtotal -= usedWallet;

        document.getElementById('used_wallet_balance').value = usedWallet;

        document.querySelector('.wallet_discount_div').style.display = 'table-row';
        document.querySelector('.wallet_discount').textContent = usedWallet.toFixed(2);

        fetch(`/get-state-taxes/${state}`)
            .then(res => res.json())
            .then(data => {
                if (data.error) return;

                const gstRate = parseFloat(data.gst || 0);
                const pstRate = parseFloat(data.pst || 0);
                const gstAmount = Math.max(0, (gstRate * subtotal) / 100);
                const pstAmount = Math.max(0, (pstRate * subtotal) / 100);
                const finalTotal = subtotal + gstAmount + pstAmount;

                document.querySelector('.gst_tax_percentage').textContent = gstRate;
                document.querySelector('.gst-amount').textContent = gstAmount.toFixed(2);
                document.querySelector('.gst_row').style.display = 'table-row';

                document.querySelector('.pst_tax_percentage').textContent = pstRate;
                document.querySelector('.pst-amount').textContent = pstAmount.toFixed(2);
                document.querySelector('.pst_row').style.display = 'table-row';

                updateEstimatedPlanTotal(finalTotal);

                if (finalTotal === 0) {
                    document.querySelectorAll('.payment-method')?.forEach(e => e.style.display = 'none');
                } else {
                    document.querySelectorAll('.payment-method')?.forEach(e => e.style.display = '');
                }
            });
    }

    document.getElementById('use_wallet')?.addEventListener('click', function () {
        const walletBox = this;
        const state = document.getElementById('shippingState')?.value || '';

        if (walletBox.checked) {
            applyPlanWalletLogic();
        } else {
            // Restore original
            const baseSubtotal = parseFloat(document.querySelector('.gst_row')?.dataset.subtotal || 0);

            fetch(`/get-state-taxes/${state}`)
                .then(res => res.json())
                .then(data => {
                    if (data.error) return;

                    const gstRate = parseFloat(data.gst || 0);
                    const pstRate = parseFloat(data.pst || 0);
                    const gstAmount = Math.max(0, (gstRate * baseSubtotal) / 100);
                    const pstAmount = Math.max(0, (pstRate * baseSubtotal) / 100);
                    const fullTotal = baseSubtotal + gstAmount + pstAmount;

                    document.querySelector('.gst_tax_percentage').textContent = gstRate;
                    document.querySelector('.gst-amount').textContent = gstAmount.toFixed(2);
                    document.querySelector('.gst_row').style.display = 'table-row';

                    document.querySelector('.pst_tax_percentage').textContent = pstRate;
                    document.querySelector('.pst-amount').textContent = pstAmount.toFixed(2);
                    document.querySelector('.pst_row').style.display = 'table-row';

                    updateEstimatedPlanTotal(fullTotal);
                    document.getElementById('used_wallet_balance').value = 0;

                    document.querySelector('.wallet_discount_div').style.display = 'none';
                    document.querySelector('.wallet_discount').value = 0;

                    document.querySelectorAll('.payment-method')?.forEach(e => e.style.display = '');
                });
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const stateDropdown = document.getElementById('shippingState');
        if (stateDropdown?.value) {
            fetchAndUpdatePlanTaxes(stateDropdown.value);
        }

        stateDropdown?.addEventListener('change', function () {
            fetchAndUpdatePlanTaxes(this.value);
        });
    });
</script>
<script>
    window.addEventListener('pageshow', function (event) {
      if (event.persisted) {
        window.location.reload();
      }
    });
</script>
@endpush

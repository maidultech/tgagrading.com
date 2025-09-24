@extends('frontend.layouts.app')

@section('title')
    {{ 'Checkout' }}
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--single {
            height: 46px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 45px !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px !important;
        }
        .form-control:focus {
            border-color: #bdbdbd !important;
        }
        #item_search {
            border-top-left-radius: 0px;
            border-bottom-left-radius: 0px;
        }
        .twitter-typeahead {
            flex: 1 0 0%;
        }
        .tt-menu{
            width: 100%;
            padding: .8rem;
            background: white;
            border: 1px solid #dce3ea;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }
        .tt-suggestion:not(:first){
            border-top: 1px solid #dce3ea;
        }
        .tt-suggestion:not(:last-child){
            border-bottom: 1px solid #dce3ea;
        }

        .tt-suggestion:hover{
            background: var(--primary);
            color: #fff !important;
        }
        .tt-suggestion{
            padding: .8rem !important;
        }
        #entry_input_form_wrapper input[type=number]::-webkit-inner-spin-button,
        #entry_input_form_wrapper input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        /* Hide number input spinners in Firefox */
        #entry_input_form_wrapper input[type=number] {
            -moz-appearance: textfield; /* Firefox */
        }
        .shippingBillingBtn:disabled{
            background-color: gray;
        }
        .checkout_steps .text-primary a {
            color: #fbcc02 !important;
        }
    </style>

<style>
            .tt-menu {
                max-height: 500px;
                overflow-y: auto;
                background-color: white;
                border: 1px solid #ccc;
                border-radius: 4px;
                width: 100%;
                z-index: 9999;
            }

            .tt-suggestion {
                padding: 10px;
                cursor: pointer;
            }


            .empty-message {
                padding: 10px;
                color: #999;
            }
</style>

@endpush

@php
    $available_card_limit = 0
@endphp

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('Checkout') }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= checkout start  ============================ -->
{{--  @dd($brands)  --}}
<div class="checkout-sec pb-5">
    <div class="container">
        <div class="mb-3 mb-lg-5">
            <div class="checkout_steps text-center">
                @include('checkout.steps')
            </div>
        </div>
        <div class="row gy-4 gy-lg-0">
            <div class="col-lg-12 mb-3 text-center">
                <span class="fw-bold text-danger minimum-cards"></span>
            </div>
            <div class="col-lg-4 order-lg-2">
                <div class="checkout_sidebar rounded  rounded-4 border-light border-light-subtle card border-1 p-4 sticky-top"
                    style="top:1rem;">
                    <div class="heading d-flex justify-content-between align-items-center mb-3">
                        <h4>Summary</h4>
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
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() . $plan->price + $service_level->extra_price }}/item
                                </td>
                            </tr> --}}
                            <!-- <tr>
                                        <td>Max Decl. Value</td>
                                        <td class="text-end">$1,500/item</td>
                                    </tr> -->
                            @if($plan->type == 'subscription')
                            <tr>
                                <td>Price</td>
                                <td class="text-end">
                                    {{-- {{ getDefaultCurrencySymbol() }}{{ session('checkout.total_unit_price') }} --}}
                                    Free
                                </td>
                            </tr>
                            @elseif($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now() && $available_card_limit > 0)
                            @else
                            <tr>
                                <td>Items x <span
                                        class="item-count">{{ session('checkout.items') ? $total_cards : 0 }}</span>
                                </td>
                                <td class="text-end">$ <span
                                        class="totalprice">{{ number_format($total_cards * session('checkout.total_unit_price'),2,'.','') }}</span>
                                </td>
                            </tr>
                            @endif
                        </table>
                        <div class="">
                            <button @disabled(!(session('checkout.items') && is_array(session('checkout.items')) && count(session('checkout.items')))) type="button" class="btn w-100 btn-light py-3 mt-4 shippingBillingBtn">
                                Proceed to Checkout
                            </button>
                            @if(!session()->has('checkout.order_edit') || empty(session('checkout.order_edit')))
                                <a href="{{ route('checkout.submission.type', $plan->id) }}" class="btn btn-light py-3 w-100 mt-3">Back to Submission Type</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 order-lg-1">
                <div class="checkout_wrapper px-xl-5">
                    <div class="heading mb-4">
                        <h4>Enter Items</h4>
                        <p>Add items you want to submit to TGA for grading</p>
                    </div>
                    <div class="entry_form">
                        <form id="entry_input_form_wrapper" method="POST">
                            <div class="row gy-3 gx-lg-2">

                                <div class="col-12">
                                    <div class="input-group">
                                        <span class="input-group-text pe-0 bg-transparent border-end-0">
                                            <i class="fa fa-search"></i>
                                        </span>
                                        <input type="text" name="item_search" id="item_search"
                                            class="form-control border-start-0"
                                            placeholder="Search for your card to submit, if no card is found then please enter below"
                                            autocomplete="off">
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-4 col-xl-2">
                                    <label for="year" class="form-label">Year</label>
                                    <input type="number" class="form-control" id="year" placeholder="Year"
                                        required min="1900" max="2100">
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-2">
                                    <label for="brand" class="form-label">Brand</label>
                                    <select class="form-control brand-select" id="brand" required>
                                        @foreach ($brands as $brand)
                                            <option value="{{$brand->name}}">{{$brand->name}}</option>
                                        @endforeach
                                    </select>
                                    
                                    {{-- <input type="text" class="form-control" id="brand" placeholder="Brand"
                                        required> --}}
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-2">
                                    <label for="cardNumber" class="form-label">Card #</label>
                                    <input type="text" class="form-control" id="cardNumber" placeholder="Card #"
                                        required onkeyup="this.value = this.value.replace(/\s+/g, '')">
                                </div>

                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <label for="playerName" class="form-label">Player/Card Name</label>
                                    <input type="text" class="form-control" id="playerName"
                                        placeholder="Player/Card Name" required>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-3">
                                    <label for="notes" class="form-label">Notes</label>
                                    <input type="text" class="form-control" id="notes" placeholder="Authenticate only, etc">
                                </div>
                                <div class="col-sm-12 mt-0">
                                    <small class="text-danger">
                                        <i class="fa fa-solid fa-circle-exclamation"></i> <span class="ms-1">If your brand is not listed, please enter it in manually</span>
                                    </small>
                                </div>
                                <div class="col-sm-6 col-lg-4 col-xl-4">
                                    <label for="notes" class="form-label">Quantity</label>
                                    <input type="number" id="quantity" required name="quantity" class="form-control"
                                        placeholder="Quantity">
                                </div>

                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Add</button>
                                    <button type="button" class="btn btn-light DiscardBtn">Clear</button>
                                </div>
                            </div>
                        </form>
                        <form action="{{ route('checkout.item.entry.store', parameters: $plan->id) }}" id="checkoutForm"
                            method="post">
                            <!-- items -->
                            @csrf

                            @if(session()->has('checkout.order_id') && !empty(session('checkout.order_id')))
                                <input type="hidden" name="order_id" value="{{ session('checkout.order_id') }}">
                            @endif

                            <div class="item_table mt-5">
                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:50%;">Items(<span
                                                        class="card-item-count">{{ session('checkout.items') ? array_sum(session('checkout.items')['quantity']) : 0 }}</span>)
                                                </th>
                                                <th style="width:10%;">Qty.</th>
                                                <th style="width:10%;">Action</th>
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
                                                                <input type="hidden" name="year[]"
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
                                                                    value="{{ $cartItems['quantity'][$loop->index] }}">
                                                                @if(session()->has('checkout.order_edit') && !empty(session('checkout.order_edit')))
                                                                    <input type="hidden" name="details_id[]"
                                                                        value="{{ $cartItems['details_id'][$loop->index] }}">
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td id="item-entry-qty">{{ $cartItems['quantity'][$loop->index] }}</td>
                                                        <td>
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
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endsession
                                            {{-- <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <img src="../as sets/images/2.jpg" class="img-fluid"
                                                                alt="">
                                                        </div>
                                                        <div class="">
                                                            1999 Pokemon Game 74 Item Finder Shadowless
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>2</td>
                                                <td>$14</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#"
                                                            class="btn btn-outline-light btn-xs rounded-pill p-2 me-2">
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
                                                            class="btn btn-outline-danger border-light btn-xs rounded-pill p-2">
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
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            <img src="../assets/images/3.png" class="img-fluid"
                                                                alt="">
                                                        </div>
                                                        <div class="">
                                                            1999 Pokemon Game 74 Item Finder Shadowless
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>2</td>
                                                <td>$14</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="#"
                                                            class="btn btn-outline-light btn-xs rounded-pill p-2 me-2">
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
                                                            class="btn btn-outline-danger border-light btn-xs rounded-pill p-2">
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
                                                </td>
                                            </tr> --}}
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- comments  -->
                            <div class=" mt-4">
                                <label for="comments" class="form-label mb-1"><strong>Comments or
                                        Requests</strong></label>
                                <p>Write anything you would like to share about your submission with us</p>
                                <textarea name="comments" id="comments" class="form-control" rows="4">{{session('checkout.items.comments') ?? ''}}</textarea>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--======================= checkout end ============================ -->

@endsection

@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById("item_search").focus();
    });
</script>
@auth
{{-- <script>
    function refreshCsrf() {
        fetch('{{ route("refresh-csrf") }}')
            .then(res => res.json())
            .then(data => {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.token);
                document.querySelectorAll('input[name="_token"]').forEach(el => {
                    el.value = data.token;
                });
                console.log('Meta CSRF token:', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                const sampleInput = document.querySelector('input[name="_token"]');
                if (sampleInput) {
                    console.log('Sample _token input value:', sampleInput.value);
                }
            });
    }

    document.addEventListener('DOMContentLoaded', refreshCsrf);
    window.addEventListener('focus', refreshCsrf);
</script> --}}
@endauth
<script>
    const plan = {!! $plan !!};


    $(document).ready(function() {
        const $itemEntryWrapper = $('#item-entry-wrapper');

        let editingRow = null;

        let itemUnitPrice = {{ session('checkout.total_unit_price') }};

        function generateRow(year, brand, cardNumber, playerName, notes, quantity) {
            const displayName = `${year} ${brand} ${cardNumber} ${playerName}`;
            return `
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div>${displayName}</div>
                    <input type="hidden" name="year[]" value="${year}">
                    <input type="hidden" name="brand[]" value="${brand}">
                    <input type="hidden" name="cardNumber[]" value="${cardNumber}">
                    <input type="hidden" name="playerName[]" value="${playerName}">
                    <input type="hidden" name="notes[]" value="${notes}">
                    <input type="hidden" name="quantity[]" value="${quantity}">
                </div>
            </td>
            <td id="item-entry-qty">${quantity}</td>
            <td>
                <div class="d-flex align-items-center">
                    <a href="#" class="btn btn-outline-light btn-xs rounded-pill p-2 me-2 edit-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#212121" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                            <path d="M13.5 6.5l4 4" />
                        </svg>
                    </a>
                    <a href="#" class="btn btn-outline-danger border-light btn-xs rounded-pill p-2 delete-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 7l16 0" />
                            <path d="M10 11l0 6" />
                            <path d="M14 11l0 6" />
                            <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                            <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                        </svg>
                    </a>
                </div>
            </td>
        </tr>
    `;
        }


        $(document).on('submit', '.entry_form #entry_input_form_wrapper', function(event) {
            event.preventDefault();

            const currentItemCount = $itemEntryWrapper.find('tr').length;

            // if(plan.type=='single' && currentItemCount >= 1){
            //     toastr.warning('In single plan you can not add more than 1 card')
            //     return ;
            // }

            const year = $('#year').val();
            const brand = $('#brand').val();
            const cardNumber = $('#cardNumber').val();
            const playerName = $('#playerName').val();
            const notes = $('#notes').val();
            const quantity = $('#quantity').val();
            let $brandSelect = $('.entry_form #entry_input_form_wrapper #brand');

            if (!year || !brand || !cardNumber || !playerName || !quantity) {
                alert('Please fill out all required fields.');
                return;
            }

            if (editingRow) {

                editingRow.replaceWith(generateRow(year, brand, cardNumber, playerName, notes,
                    quantity));

                editingRow = null;

            } else {

                $itemEntryWrapper.append(generateRow(year, brand, cardNumber, playerName, notes,
                    quantity));
            }

            updateQtyPrice()

            $brandSelect.val("{{ $brands[0]->name }}").trigger('change');
            $('.entry_form #entry_input_form_wrapper')[0].reset();
        });


        $itemEntryWrapper.on('click', '.delete-item', function(event) {
            event.preventDefault();

            if (confirm("Are you sure?")) {
                $(this).closest('tr').remove();

                const currentItemCount = $itemEntryWrapper.find('tr').length;
                $('.item-count').text(currentItemCount);
                $('.totalprice').text(parseFloat(currentItemCount * itemUnitPrice).toFixed(2));
                updateQtyPrice()
            }
        });

        $itemEntryWrapper.on('click', '.edit-item', function(event) {
            event.preventDefault();
            editingRow = $(this).closest('tr');

            $('#year').val(editingRow.find('input[name="year[]"]').val());
            let brand = editingRow.find('input[name="brand[]"]').val();
            let $brandSelect = $('#brand');

            if ($brandSelect.find('option[value="' + brand + '"]').length) {
                $brandSelect.val(brand).trigger('change');
            } else {
                let newOption = new Option(brand, brand, true, true);
                $brandSelect.append(newOption).trigger('change');
            }

        
            $('#cardNumber').val(editingRow.find('input[name="cardNumber[]"]').val());
            $('#playerName').val(editingRow.find('input[name="playerName[]"]').val());
            $('#notes').val(editingRow.find('input[name="notes[]"]').val());
            $('#quantity').val(editingRow.find('input[name="quantity[]"]').val());
        });

        $(document).on('click', '.shippingBillingBtn', function(event) {
            event.preventDefault();
            let currentItemsQty = 0;

            $itemEntryWrapper.find('tr').map(function(k, v) {
                currentItemsQty += parseInt($(this).find('[name="quantity[]"]').val())
            });

            if (plan.type == 'general' && currentItemsQty < plan.minimum_card) {
                $('.minimum-cards').text("A minimum of " + plan.minimum_card + " cards must be added for this service");
                // toastr.error("You have to add minimum " + plan.minimum_card + " card")
                return
            }
            $('#checkoutForm').submit()
        })
        $(document).on('click', '.DiscardBtn', function(event) {
            event.preventDefault();
            let $brandSelect = $('.entry_form #entry_input_form_wrapper #brand');
            $('.entry_form #entry_input_form_wrapper')[0].reset();
            $brandSelect.val("{{ $brands[0]->name }}").trigger('change');
            editingRow = null;
        })

        function updateQtyPrice() {
            const totalQty = $('#item-entry-wrapper td#item-entry-qty').toArray().reduce((sum, td) => {
                return sum + (parseInt($(td).text()) || 0);
            }, 0);
            // console.log(totalQty);
            $('.card-item-count').text(totalQty);

            let currentItemsQty = 0;
            $itemEntryWrapper.find('tr').map(function(k, v) {
                currentItemsQty += parseInt($(this).find('[name="quantity[]"]').val())
            });
            $('.item-count').text(currentItemsQty);

            if(currentItemsQty==0){
                $('.shippingBillingBtn').attr('disabled',true)
            }else{
                $('.shippingBillingBtn').attr('disabled',false)

            }

            $('.totalprice').text(parseFloat(currentItemsQty * itemUnitPrice).toFixed(2));


            $.ajax({
                url: '{{ route("checkout.item.entry.store",$plan->id) }}?api=entry-page',
                type: 'POST',
                processData:false,
                contentType: false,
                headers :{
                    'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr('content'),
                    // 'Content-Type' : 'multipart/form-data'
                },
                data: new FormData($('#checkoutForm')[0]),
            })
            .done(function(response) {
                if (response.success && response.unit_price) {
                    itemUnitPrice = parseFloat(response.unit_price);
                    console.log(itemUnitPrice);
                    
                    $('.totalprice').text(parseFloat(currentItemsQty * itemUnitPrice).toFixed(2));
                }
            });
            // .done(function(e) {
            //     console.log("success"+e);
            // })
            // .fail(function() {
            //     console.log("error");
            // })
            // .always(function() {
            //     console.log("complete");
            // });


        }

    });
</script>
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.js"></script> --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/corejs-typeahead/1.3.4/typeahead.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

    $(".brand-select").select2({
        tags: true
    });

    var cardQuery = new Bloodhound({
        datumTokenizer: Bloodhound.tokenizers.whitespace,
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        remote: {
            url: '{{ route('api.card.search', '%QUERY%') }}',
            wildcard: '%QUERY%',
            transform: function(response) {
                return response.success ? response.data : [];
            }
        }

    });


    {{--  $('#item_search').typeahead(null, {
        name: 'card-search',
        display: function(datum) {
            return `${datum.year || 'N/A'} ${datum.brand_name || 'N/A'} ${datum.card || 'N/A'} ${datum.card_name || 'N/A'}`;
        },
        minLength: 2,
        source: cardQuery,
        highlight: true,
        templates: {
            empty: [
                '<div class="empty-message">',
                'Unable to find any card, please enter the card info below',
                '</div>'
            ].join('\n'),
        }
    }).on('typeahead:select',function(event,suggestion){
        $('.entry_form #entry_input_form_wrapper #year').val(suggestion.year);
        // Handle brand select2 with tagging support
        let brand = suggestion.brand_name;
        let $brandSelect = $('.entry_form #entry_input_form_wrapper #brand');

        if ($brandSelect.find("option[value='" + brand + "']").length === 0) {
            let newOption = new Option(brand, brand, true, true);
            $brandSelect.append(newOption).trigger('change');
        } else {
            $brandSelect.val(brand).trigger('change');
        }
        // $('.entry_form #entry_input_form_wrapper #brand').val(suggestion.brand_name);
        $('.entry_form #entry_input_form_wrapper #cardNumber').val(suggestion.card);
        $('.entry_form #entry_input_form_wrapper #playerName').val(suggestion.card_name);
        $('.entry_form #entry_input_form_wrapper #notes').val(suggestion.notes);
    });  --}}

    $('#item_search').typeahead(
    {
        hint: true,
        highlight: true,
        minLength: 2
    },
    {
        name: 'card-search',
        display: function (datum) {
            return `${datum.year || 'N/A'} ${datum.brand_name || 'N/A'} ${datum.card || 'N/A'} ${datum.card_name || 'N/A'}`;
        },
        limit: 1000, // show up to 1000 matches or use `Infinity`
        source: cardQuery,
        highlight: true,
        templates: {
            empty: [
                '<div class="empty-message">',
                'Unable to find any card, please enter the card info below',
                '</div>'
            ].join('\n'),
        }
    }
)
.on('typeahead:select', function (event, suggestion) {
    $('.entry_form #entry_input_form_wrapper #year').val(suggestion.year);

    let brand = suggestion.brand_name;
    let $brandSelect = $('.entry_form #entry_input_form_wrapper #brand');

    if ($brandSelect.find("option[value='" + brand + "']").length === 0) {
        let newOption = new Option(brand, brand, true, true);
        $brandSelect.append(newOption).trigger('change');
    } else {
        $brandSelect.val(brand).trigger('change');
    }

    $('.entry_form #entry_input_form_wrapper #cardNumber').val(suggestion.card);
    $('.entry_form #entry_input_form_wrapper #playerName').val(suggestion.card_name);
    $('.entry_form #entry_input_form_wrapper #notes').val(suggestion.notes);
});


</script>

{{--  <script>
$('#item_search').typeahead(
    {
        hint: true,
        highlight: true,
        minLength: 2
    },
    {
        name: 'card-search',
        display: function (datum) {
            return `${datum.year || 'N/A'} ${datum.brand_name || 'N/A'} ${datum.card || 'N/A'} ${datum.card_name || 'N/A'}`;
        },
        limit: 1000, // show up to 1000 matches or use `Infinity`
        source: cardQuery,
        highlight: true,
        templates: {
            empty: [
                '<div class="empty-message">',
                'Unable to find any card, please enter the card info below',
                '</div>'
            ].join('\n'),
        }
    }
)
.on('typeahead:select', function (event, suggestion) {
    $('.entry_form #entry_input_form_wrapper #year').val(suggestion.year);

    let brand = suggestion.brand_name;
    let $brandSelect = $('.entry_form #entry_input_form_wrapper #brand');

    if ($brandSelect.find("option[value='" + brand + "']").length === 0) {
        let newOption = new Option(brand, brand, true, true);
        $brandSelect.append(newOption).trigger('change');
    } else {
        $brandSelect.val(brand).trigger('change');
    }

    $('.entry_form #entry_input_form_wrapper #cardNumber').val(suggestion.card);
    $('.entry_form #entry_input_form_wrapper #playerName').val(suggestion.card_name);
    $('.entry_form #entry_input_form_wrapper #notes').val(suggestion.notes);
});

</script>  --}}
@endpush

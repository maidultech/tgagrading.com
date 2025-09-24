@extends('frontend.layouts.app')

@section('title')
    {{ 'Checkout' }}
@endsection



@push('style')

@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    {{-- @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Checkout') }}</li>
    @endsection --}}
    <!-- ======================= breadcrumb end  ============================ -->
    <!-- ======================= checkout start  ============================ -->
    <div class="checkout-sec pb-5">
        <div class="container">
            <div class="mb-3 mb-lg-5">
                <div class="checkout_steps text-center">
                    @include('checkout.plan_steps')
                </div>
            </div>
            <div class="order_confirmation text-center">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-6">
                        <img src="{{ asset($setting->order_confirmation_image ?? 'frontend/assets/images/confirmed.png') }}" class="img-fluid mb-3" width="350" alt="Confirmation Image">
                        <h2>Your Order Has Been Confirmed!</h2>
                        <p class="mb-2">
                            Congratulations! You have successfully purchased <br> Plan: {{ $order->plan_name ?? $order->rPlan->name }} <br><br>
                            <address>
                                {!! nl2br($setting->office_address) !!}
                            </address>
                            <br>
                            You can download your invoice from the following link: <a href="{{ route('user.order.invoice.download', $order->id) }}">Download Invoice</a>
                        </p>
                        <a href="{{ route('user.dashboard') }}" class="btn btn-primary py-3 px-4 m-1">Return to Your Account Page</a>
                        <!-- <a href="#" class="btn btn-light py-3 px-4 m-1">My Order</a> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--======================= checkout end ============================ -->

@endsection

@push('script')
@endpush

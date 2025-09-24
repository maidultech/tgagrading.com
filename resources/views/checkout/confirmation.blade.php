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
                    @include('checkout.steps')
                </div>
            </div>
            <div class="order_confirmation text-center">
                <div class="row d-flex justify-content-center">
                    <div class="col-xl-6">
                        <img src="{{ asset($setting->order_confirmation_image ?? 'frontend/assets/images/confirmed.png') }}" 
                            class="img-fluid mb-3" width="350" alt="Confirmation Image" style="object-fit: contain; max-height: 300px;">
                        <h2>Your order has been {{$edit_flag == 'edit' ? 'edited' : 'created'}}!</h2>
                        <p class="mb-4">
                            Your order #{{ $order->order_number }} has been {{$edit_flag == 'edit' ? 'edited' : 'created'}}. <br>

                           
                            <div>
                            <a href="{{ route('user.invoice.show', $order->id) }}" target="_blank" onclick="printInvoice(event)" class="link">Please print out your submission</a> <br>
                            and place it inside your shipping box with your cards.<br>
                            Here is our mailing address
                            </div>
                           

                            <br>
                            <address>
                                {!! nl2br($setting->office_address) !!}
                            </address>
                            <p>
                            Ensure your cards are cleaned and free of any dust, dirt or fingerprints to receive the optimal grade. Also ensure you are using clean plastic protective sleeves to avoid your cards from picking up any dust or dirt after cleaning.
                            </p>
                            
                            <br>
                            Once you have shipped your order, please update the tracking number in your 
                            <br>
                            <a href="{{ route('user.order.invoice',$order->id) }}"><strong>Order Page</strong></a> to avoid any delays in processing. <br>
                            Thank you and we look forward to receiving your cards!
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
<script>
    function printInvoice(event) {
        event.preventDefault(); // Prevent the default behavior
        const printWindow = window.open(event.currentTarget.href, '_blank');
        printWindow.onload = () => {
            printWindow.print(); // Trigger the print dialog when the content loads
        };
    }
</script>
@endpush

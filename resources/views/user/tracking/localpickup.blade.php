@extends('frontend.layouts.app')

@section('title')
    {{ 'Track Shipment' }}
@endsection



@push('style')
<style>
    .btn-small {
        padding: 5px 7px !important;
        font-size: 12px !important;
        border-radius: 5px;
    }
    p {
        font-size: 14px;
        /* letter-spacing: 1px; */
        font-weight: 500;
        line-height: normal;
    }
    table {
        margin-top: 20px;
        font-size: 14px;
    }

    .status-bar {
        height: 2px;
        background: gray;
        position: relative;
        top: 20px;
        margin: 0 auto;
    }
    ul.progress-barr {
        width: 100%;
        margin: 0;
        padding: 0;
        font-size: 0;
        list-style: none;
        display: flex;
        flex-direction: row;
        justify-content: center;
    }

    li.section {
        display: inline-block;
        padding-top: 45px;
        font-size: 13px;
        font-weight: bold;
        line-height: 16px;
        color: gray;
        vertical-align: top;
        position: relative;
        text-align: center;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 25% !important;
        text-wrap-mode: wrap;
    }
    li.section.visited {
        color: #034ea1;
    }
    li.section.visited.current:before {
        box-shadow: 0 0 0 2px #034EA1;
    }
    li.section.visited:before {
        content: '\2714';
        background: #034EA1;
    }
    li.section:before {
        content: 'x';
        position: absolute;
        top: 3px;
        left: calc(50% - 15px);
        z-index: 1;
        width: 30px;
        height: 30px;
        color: white;
        border: 2px solid white;
        border-radius: 17px;
        line-height: 27px;
        background: gray;
    }
    .current-status {
        height: 2px;
        width: 0;
        border-radius: 1px;
        background: #034ea1;
    }


@media (max-width: 992px) {
    ul.progress-barr {
        flex-direction: column;
        justify-content: center;
    }

    li.section {
        text-overflow: unset;
        width: 100% !important;
        white-space: normal; /* Use white-space for proper text wrapping */
    }

    .status-bar {
        display: none;
    }
}
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('User') }}</li>
        <li class="breadcrumb-item">{{ __('Track Shipment') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <div class="account_seciton pb-5 pt-3">
        <div class="container">
            <div class="section_heading mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="">
                        <h1>Track Shipment</h1>
                    </div>
                    <a href="{{ route('user.orders') }}" class="btn btn-light btn-sm px-3 rounded-2 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="#034ea2" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-chevron-left">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Back
                    </a>
                </div>
            </div>
            <div class="row gy-4 gy-lg-0 mt-5">
                <h4 class="text-center">SHIPMENT TRACKING INFORMATION</h4>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p>Local Pickup</p>
                        <p>
                            {!! nl2br($order->shipping_notes)  !!}
                        </p>
                        <p><strong>Delivery Status:</strong> Delivered</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Merchant:</strong> {{$setting->site_name}}</p>
                        <p><strong>Weight:</strong> {{$order->cards->count()*0.08}}KG</p>
                        <p><strong>Grand Total Paid:</strong> {{ getDefaultCurrencySymbol().number_format($order->transaction->amount,2) }}</p>
                    </div>
                </div>
                
                <!-- Progress Bar -->

                <div class="progress-barr-wrapper mt-5">
                    <div class="status-bar" style="width: 50%;">
                        <div class="current-status"
                            style="width: {{ $trackingStatus === 'Delivered' ? '100' : ($trackingStatus === 'In Transit' ? '50' : '0') }}%;">
                        </div>
                    </div>
                    <ul class="progress-barr">
                        <li class="section visited">
                            Accepted
                        </li>
                        
                        <li class="section visited">
                            In Transit
                        </li>
                        
                        <li class="section visited current">
                            Delivered
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
@endpush

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
            @if(!empty($trackingDetails))
            <div class="row gy-4 gy-lg-0 mt-5">
                <h4 class="text-center">SHIPMENT TRACKING NUMBERS</h4>
                <div class="row mt-4">
                    <div class="col-md-6">
                        <p><strong>Tracking Number:</strong> {{$trackingDetails['trackingNumber']}}</p>
                        <p><strong>Destination:</strong>
                            {{ $order->transaction?->shipping_data['shippingAddress'] }},
                            {{ $order->transaction?->shipping_data['shippingCity'] }},
                            {{ $order->transaction?->shipping_data['shippingState'] }},
                            {{ $order->transaction?->shipping_data['shippingCountry'] }},
                            {{ strtoupper(preg_replace('/\s+/u', '', $order->transaction->shipping_data['shippingZip'])) }}
                        </p>

                        <p><strong>Delivery Status:</strong> <span title="{{ $trackingDetails['currentStatus']['simplifiedTextDescription'] ?? $trackingDetails['currentStatus']['description'] }}">{{$trackingDetails['currentStatus']['description']}}</span></p>
                        <p><strong>Expected Delivery:</strong> @if(isset($trackingDetails['deliveryDate'][0])){{ \Carbon\Carbon::parse($trackingDetails['deliveryDate'][0]['date'])->format('F j, Y') }}@endif</p>
                        <p><strong>Carrier Name:</strong> {{$trackingDetails['service']['description']}}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Merchant:</strong> {{$setting->site_name}}</p>
                        <p><strong>Weight:</strong> {{$order->cards->count()*0.08}}KG</p>
                        <p><strong>Grand Total Paid:</strong> {{ getDefaultCurrencySymbol().$order->transaction->amount }}</p>
                    </div>
                </div>
                <div class="progress-barr-wrapper mt-5">
                    <div class="status-bar" style="width: 50%;">
                        <div class="current-status"
                            style="width: {{ $trackingStatus === 'Delivered' ? '100' : ($trackingStatus === 'In Transit' ? '50' : '0') }}%;">
                        </div>
                    </div>
                    <ul class="progress-barr">
                        <li class="section {{ $trackingStatus === 'Accepted' ? 'visited current' : (in_array($trackingStatus, ['In Transit', 'Delivered']) ? 'visited' : '') }}">
                            Accepted
                        </li>
                        
                        <li class="section {{ $trackingStatus === 'In Transit' ? 'visited current' : ($trackingStatus === 'Delivered' ? 'visited' : '') }}">
                            In Transit
                        </li>
                        
                        <li class="section {{ $trackingStatus === 'Delivered' ? 'visited current' : '' }}">
                            Delivered
                        </li>
                    </ul>
                </div>
                <!-- Delivery Progress Table -->
                <div class="mt-4">
                    <h5>Delivery Progress</h5>
                    <p class="text-muted">This is the most up-to-date information available as per the carrier's website.</p>
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                            <tr>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Location</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trackingDetails['activity'] as $event)
                            <tr>
                                <td>
                                    {{ \Carbon\Carbon::parse($event['date'])->format('F d, Y') ?? 'N/A' }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($event['time'])->format('h:i A') ?? 'N/A' }}
                                </td>
                                <td>
                                    @if (isset($event['location']['address']['city']))
                                        {{ $event['location']['address']['city'] }}, {{ $event['location']['address']['countryCode'] }}
                                    @endif
                                </td>
                                <td>
                                    {{ $event['status']['description'] ?? '' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="row gy-4 gy-lg-0 mt-5">
                @if(empty($order->admin_tracking_id))
                    <h5 class="text-center text-danger">The tracking information will be available on the next business day.</h5>
                    {{-- <h5 class="text-center text-danger">Tracking information not updated yet.</h5> --}}
                @else
                    @if($is_invalid == 1)
                        {{-- <h5 class="text-center text-danger">The tracking information provided is invalid. Please <a href="{{route('frontend.contact')}}">contact</a> our support team for assistance.</h5> --}}
                        <h5 class="text-center text-danger">The tracking information will be available on the next business day.</h5>
                    @else
                        <h5 class="text-center text-danger">The tracking information will be available on the next business day.</h5>
                    @endif
                @endif
            </div>
            @endif
        </div>
    </div>

@endsection

@push('script')
@endpush

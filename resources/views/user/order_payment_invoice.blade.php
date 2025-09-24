@extends('frontend.layouts.app')

@section('title')
    {{ 'My Orders' }}
@endsection



@push('style')
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User') }}</li>
    <li class="breadcrumb-item">{{ __('Invoice') }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->

<div class="account_seciton pb-5 pt-3">
    <div class="container">
        <div class="section_heading mb-4">
            <div class="d-flex align-items-center justify-content-between">
                <div class="">
                    <h1>Invoice</h1>
                </div>
                <div class="d-print-none">
                    <a href="{{ route('user.orders') }}" class="btn btn-light btn-sm px-3 rounded-2 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="#034ea2" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-chevron-left">
                            <path d="m15 18-6-6 6-6" />
                        </svg>
                        Back
                    </a>
                    <a href="{{ route('user.order.payment.invoice.download',$trnx->order_id) }}" class="btn btn-light btn-sm px-3 rounded-2 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="#034ea2" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="lucide lucide-printer">
                            <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />
                            <path d="M6 9V3a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v6" />
                            <rect x="6" y="14" width="12" height="8" rx="1" />
                        </svg>
                        Print
                    </a>
                </div>
            </div>
        </div>
        @include('common.payment_invoice_body')
    </div>
</div>
@endsection

@push('script')
@endpush

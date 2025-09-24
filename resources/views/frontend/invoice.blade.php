@extends('frontend.layouts.app')

@section('title')
    {{ 'My Orders' }}
@endsection



@push('style')
<style>
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
        width: 13% !important;
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
                    <a href="{{ route('user.order.invoice.download',$trnx->order_id) }}" class="btn btn-light btn-sm px-3 rounded-2 py-2">
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
        @if($trnx->order?->rPlan?->type == 'subscription' && $trnx->order?->total_order_qty == 0)
            @include('common.plan_invoice_body')
        @else
            @include('common.invoice_body')
        @endif
    </div>
</div>
@endsection

@push('script')
@endpush

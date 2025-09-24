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
                    @if($trnx->order?->status == 40 && $trnx->order?->shipping_method != 'local_pickup')
                    <button data-bs-toggle="modal" data-bs-target="#shippingInformationModal" class="btn btn-light btn-sm px-3 rounded-2 py-2">
                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="#034ea2"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-package"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" /><path d="M12 12l8 -4.5" /><path d="M12 12l0 9" /><path d="M12 12l-8 -4.5" /><path d="M16 5.25l-8 4.5" /></svg>
                        Shipping Information
                    </button>
                    @endif

                    @if($trnx->order?->status < 10)
                    <button data-bs-toggle="modal" data-bs-target="#CustomerShippingInformationModal" class="btn btn-light btn-sm px-3 rounded-2 py-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#034ea2" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-truck"><path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2"/><path d="M15 18H9"/><path d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14"/><circle cx="17" cy="18" r="2"/><circle cx="7" cy="18" r="2"/></svg>
                        {{ !empty($trnx->order?->customer_tracking_url) ? 'Update' : 'Enter'}} Incoming Tracking Number
                    </button>
                    @endif
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
{{-- Customer Shipping Information Modal --}}

  <div class="modal fade" id="CustomerShippingInformationModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Customer Shipping Information</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('user.order.trackingInfoStore',$trnx->order->order_number) }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group mb-3">
                    <label for="">Tracking Number</label>
                    <input required type="text" value="{{ $trnx->order->customer_tracking_url }}" class="form-control" placeholder="Enter your incoming tracking number" name="tracking_url">
                </div>
                <div class="form-group mb-3">
                    <label for="">Courier Company</label>
                    <div id="tracking-selector">
                        <input required type="text" value="{{ $trnx->order->customer_tracking_note }}" class="form-control" placeholder="Enter courier company name" name="tracking_note">
                        {{-- <select class="form-control form-select" id="tracking-select" name="tracking_note">
                            <option value="Canada Post" {{ $trnx->order->customer_tracking_note == 'Canada Post' ? 'selected' : '' }}>Canada Post</option>
                            <option value="UPS" {{ $trnx->order->customer_tracking_note == 'UPS' ? 'selected' : '' }}>UPS</option>
                            <option value="Other">Other</option>
                        </select> --}}
                    </div>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary p-2" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary p-2">Save changes</button>
              </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="shippingInformationModal" tabindex="-1" aria-labelledby="shippingInformationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="shippingInformationModalLabel">Shipping Information</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="form-group mb-3">
                <label for="">Tracking Number :  </label>
                <span>{{ $trnx->order?->admin_tracking_id }}</span>
            </div>
            @if($trnx->order?->admin_tracking_note)
            <div class="form-group mb-3">
                {{-- <label for="">Tracking Note</label> --}}
                <label for="">Courier Company :  </label>
                <span>{{$trnx->order?->admin_tracking_note}}</span>
                {{-- <select class="form-control form-select" readonly>
                    <option value="Canada Post" {{ $trnx->order?->admin_tracking_note == 'Canada Post' ? 'selected' : '' }}>Canada Post</option>
                    <option value="UPS" {{ $trnx->order?->admin_tracking_note == 'UPS' ? 'selected' : '' }}>UPS</option>
                    <option value="Other" {{ $trnx->order?->admin_tracking_note != 'Canada Post' && $trnx->order?->admin_tracking_note != 'UPS' ? 'selected' : '' }}>Other</option>
                </select> --}}
            </div>
            @endif
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary p-2" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('script')
@endpush

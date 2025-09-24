@extends('admin.layouts.master')
@section('order', 'active')

@section('title') {{ $title ?? '' }} @endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

@push('style')
<style>
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
        top: 2px;
        left: calc(50% - 15px);
        z-index: 1;
        width: 30px;
        height: 30px;
        color: white;
        border: 2px solid white;
        border-radius: 17px;
        line-height: 30px;
        background: gray;
    }
    .current-status {
        height: 2px;
        width: 0;
        border-radius: 1px;
        background: #034ea1;
    }
</style>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

@endpush
@section('content')
    <div class="content-wrapper">    

        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title">{{ $title ?? '' }}</h4>
                                    </div> 
                                    <div class="col-6 text-right">
                                        {{-- <a href="{{route('admin.order.certificate.index', $order->id)}}" class="btn btn-sm btn-primary btn-gradient">{{__('Grades')}}</a> --}}
                                        <button id="download-pdf-btn" class="btn btn-sm text-white" style="background: linear-gradient(to right, #f7971e, #ffd200); border: none;">
                                        <i class="fa fa-file-pdf"></i> INVOICE DOWNLOAD
                                        </button>
                                        <a href="{{ route('admin.card.index',['order' => $trnx->order?->id]) }}" class="btn btn-sm btn-primary btn-gradient">{{__('Grades')}}</a>
                                        <a href="{{ route('admin.outgoing.order') }}" class="btn btn-sm btn-primary btn-gradient">{{__('Back')}}</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-0 table-responsive" id="invoice-content">
                                @if($trnx->order?->rPlan?->type == 'subscription' && $trnx->order?->total_order_qty == 0)
                                    @include('common.plan_invoice_body')
                                @else
                                    @include('common.invoice_body')
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('script')
<script>
    document.getElementById('download-pdf-btn').addEventListener('click', function () {
        const invoice = document.getElementById('invoice-content');
        const printContents = invoice.cloneNode(true);
        const printArea = document.createElement('div');
        printArea.id = 'print-area';
        printArea.appendChild(printContents);
        printArea.style.padding = '20px';
        document.body.innerHTML = '';
        document.body.appendChild(printArea);
        window.print();
        location.reload();
    });
</script>
@endpush




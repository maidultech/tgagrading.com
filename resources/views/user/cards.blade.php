@extends('frontend.layouts.app')

@section('title')
    {{ 'My Orders' }}
@endsection



@push('style')
<!-- DataTables Bootstrap 5 CSS -->
<link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

<!-- Responsive plugin CSS -->
<link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

<style>
.orders_table th:first-child {
    border-radius: 0px;
    border-top-left-radius: 16px;
}
.orders_table th:last-child {
    border-radius: 0px;
    border-top-right-radius: 16px;
}

table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control,table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control {
    position: relative;
    padding-left: 30px;
    cursor: pointer
}

table.dataTable.dtr-inline.collapsed>tbody>tr>td.dtr-control:before,table.dataTable.dtr-inline.collapsed>tbody>tr>th.dtr-control:before {
    top: 50%;
    left: 5px;
    height: 1em;
    width: 1em;
    margin-top: -9px;
    display: block;
    position: absolute;
    color: white;
    border: .15em solid white;
    border-radius: 1em;
    box-shadow: 0 0 .2em #444;
    box-sizing: content-box;
    text-align: center;
    text-indent: 0 !important;
    font-family: "Courier New",Courier,monospace;
    line-height: 1em;
    content: "+";
    background-color: #0275d8
}

table.dataTable.dtr-inline.collapsed>tbody>tr.parent>td.dtr-control:before,table.dataTable.dtr-inline.collapsed>tbody>tr.parent>th.dtr-control:before {
    content: "-";
    background-color: #d33333
}
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('User') }}</li>
        <li class="breadcrumb-item">{{ __('My Orders') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <div class="account_seciton pb-5 pt-3">
        <div class="container">
            <div class="section_heading mb-4">
                <h1>My Orders</h1>
            </div>
            <div class="row gy-4 gy-lg-0">
                <div class="col-lg-3">
                    @section('user_orders','active')
                    @include('user.sidebar')
                </div>
                <div class="col-lg-9">
                    <div class="user_dashboard p-3 p-xl-4 rounded border border-light">
                        <div class="header mb-4">
                            <div class="title">
                                <h4>Card List</h4>
                            </div>
                        </div>
                        <div class="orders_table table-responsive overflow-hidden">
                            <table id="dataTables" class="table m-0 align-middle">
                                <thead>
                                <tr>
                                    <th>Order#</th>
                                    <th>Card</th>
                                    <th>Cert</th>
                                    <th>Centering</th>
                                    <th>Corners</th>
                                    <th>Edges</th>
                                    <th>Surface</th>
                                    <th style="white-space: nowrap">Final Grading</th>
                                    <th style="white-space: nowrap">Uploaded Image</th>
                                    {{-- <th>Action</th> --}}
                                </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $lastCertificateNo = null;
                                        $serial = 0;
                                    @endphp
                                    @foreach ($order->details as $detail)
                                        @foreach (range(1, $detail->qty) as $item)
                                            @php
                                                $cardDetails = $detail->cards()->take(1)->skip($loop->index)->first();
                                                $certNo = $cardDetails->card_number ?? ($lastCertificateNo = getNewCertificate2($lastCertificateNo));
                                                $serial++;
                                            @endphp

                                            <tr>
                                                <td>{{ $order->order_number }}</td>
                                                <td>{{ $cardDetails?->item_name ?? $detail->item_name }}</td>
                                                <td>@if ($cardDetails?->final_grading) {{ $cardDetails?->is_no_grade===1 ? null : $certNo }} @endif</td>
                                                <td>{{ $cardDetails?->centering }}</td>
                                                <td>{{ $cardDetails?->corners }} </td>
                                                <td>{{ $cardDetails?->edges }}</td>
                                                <td>{{ $cardDetails?->surface }}</td>
                                                <td style="width: 15%;">
                                                    @if ($cardDetails?->is_no_grade == 1)
                                                        No Grade
                                                        @if(!empty($cardDetails?->no_grade_reason))
                                                            <br><small class="text-info">({{ $cardDetails?->no_grade_reason }})</small>
                                                        @endif
                                                    @else
                                                        {{ $cardDetails?->final_grading }}
                                                        @if ($cardDetails?->final_grading)
                                                            <br>
                                                            @php($avg_grade = collect([
                                                                $cardDetails?->centering,
                                                                $cardDetails?->corners,
                                                                $cardDetails?->edges,
                                                                $cardDetails?->surface,
                                                            ])->filter()->avg())
                                                            {{-- @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (int) str($cardDetails?->final_grading)->before('.')->value()) --}}
                                                            @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (float) str($cardDetails?->final_grading)->value())
                                                            @if($avg_grade == 10)
                                                                <small class="ml-2 text-info" style="white-space: nowrap;">( {{ $finalGradings[(string)$grading][1] ?? '' }} )</small>
                                                            @else
                                                                <small class="ml-2 text-info" style="white-space: nowrap;">( {{ $finalGradings[(string)$grading][0] ?? '' }} )</small>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </td>
                                                <td>
                                                    @if(!empty($cardDetails?->front_page))
                                                        <div class="d-flex flex-column mb-3">
                                                            <label for="" class="form-label" style="color: #6e6e6e; font-weight: 600 !important; text-align: center;">Front</label>
                                                            <a href="{{ asset($cardDetails?->front_page) }}" target="_blank" class="text-center">
                                                                <img src="{{ asset($cardDetails?->front_page) }}" class="img img-rounded" alt="Front" style="max-width: 110px; height: 50px; object-fit: contain;">
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if(!empty($cardDetails?->back_page))
                                                        <div class="d-flex flex-column">
                                                            <label for="" class="form-label" style="color: #6e6e6e; font-weight: 600 !important; text-align: center;">Back</label>
                                                            <a href="{{ asset($cardDetails?->back_page) }}" target="_blank" class="text-center">
                                                                <img src="{{ asset($cardDetails?->back_page) }}" class="img img-rounded" alt="Back" style="max-width: 110px; height: 50px; object-fit: contain;">
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                                {{-- <td>
                                                    <div class="dropdown">
                                                        @if ($cardDetails)
                                                        <a href="{{ route('admin.order.certificate.label', ['order' => $cardDetails->order_id, 'id' => $cardDetails->id]) }}" title="print the label" class="">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                                class="icon icon-tabler icons-tabler-outline icon-tabler-download">
                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
                                                                <path d="M7 11l5 5l5 -5" />
                                                                <path d="M12 4l0 12" />
                                                            </svg>
                                                        </a>
                                                        @endif
                                                    </div>
                                                </td> --}}
                                                {{-- <td class="text-center">
                                                    <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" @checked($cardDetails?->is_no_grade) type="checkbox" class="cert_no_grade" name="cert_no_grade[{{ $detail->id }}][{{ $cardDetails?->id }}]">
                                                </td>
                                                <td style="min-width:150px">

                                                    <textarea name="cert_no_grade_reason[{{ $detail->id }}][{{ $cardDetails?->id }}]" class="form-control  cert_no_grade_reason {{ $cardDetails?->no_grade_reason ? '' : 'd-none' }} ">{{ $cardDetails?->no_grade_reason }}</textarea>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Responsive plugin JS -->
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#dataTables').DataTable({
            paging: false,   
            searching: false,
            ordering: false, 
            info: false,     
            responsive: true 
        });
    });
</script>
@endpush

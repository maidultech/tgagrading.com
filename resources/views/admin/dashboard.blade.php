@extends('admin.layouts.master')
@section('dashboard', 'active')

@section('title') {{ $data['title'] ?? '' }} @endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@php
   $order = $data['order'];
   $finalGradings = $data['finalGradings'];
   $rowCount = 0;
@endphp

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">{{ $data['title'] ?? __('messages.common.dashboard') }}</h1>
                        <div>{{ __('messages.Welcome_Back') }}, {{ Auth::user()->name }}</div>
                    </div>
                    {{-- <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{__('messages.common.dashboard')}}</a></li>

                        </ol>
                    </div> --}}
                </div>
            </div>
        </div>

        <div class="content">

            <div class="container-fluid">
                <div class="row d-flex justify-content-end">
                    <div class="col-md-3 d-flex align-items-center mb-3">
                        <div class="col-md-3">
                            <label for="date_range" class="mb-0">Date Range </label>
                        </div>
                        <div class="col-md-9">
                            <form id="dateRangeForm" action="{{ route('admin.dashboard') }}" method="GET">
                                <input type="text" class="form-control" id="date_range" name="date_range" placeholder="Date Range"  value="{{ request('date_range') }}">
                            </form>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3>{{ $data['total_orders'] ?? 0 }}</h3>
                                <p>Total Order Submitted</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ route('admin.order.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3>{{ $data['total_graded_cards'] ?? 0 }}</h3>
                                <p>Grading Complete</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-stats-bars"></i>
                            </div>
                            <a href="{{ route('admin.outgoing.order') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3>{{ $data['users_count'] }}</h3>
                                <p>Total Customer Signups</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="{{ route('admin.customer.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">

                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>{{getDefaultCurrencySymbol()}}{{ number_format($data['totalTransaction'], 2) }}</h3>
                                <p>Total Amount Paid</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-pie-graph"></i>
                            </div>
                            <a href="{{ route('admin.transaction.index') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="clearfix hidden-md-up"></div>

                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                        <h4 class="card-title">{{ __('messages.user_dashboard.latest_cards') }} </h4>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('admin.card.index') }}"
                                            class="btn btn-sm btn-primary btn-gradient">{{ __('messages.common.view_all') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="auto">SN</th>
                                            <th width="auto">Card</th>
                                            <th width="auto">Order Number</th>
                                            <th width="auto">User</th>
                                            <th width="auto">Cert Number</th>
                                            <th width="auto">Centering</th>
                                            <th width="auto">Corners</th>
                                            <th width="auto">Edges</th>
                                            <th width="auto">Surface</th>
                                            <th width="auto">Final Grading</th>
                                            <th width="auto">Action</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th width="auto">SN</th>
                                            <th width="auto">Card</th>
                                            <th width="auto">Order Number</th>
                                            <th width="auto">User</th>
                                            <th width="auto">Cert Number</th>
                                            <th width="auto">Centering</th>
                                            <th width="auto">Corners</th>
                                            <th width="auto">Edges</th>
                                            <th width="auto">Surface</th>
                                            <th width="auto">Final Grading</th>
                                            <th width="auto">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $lastCertificateNo = null;
                                            $serial = 0;
                                        @endphp
                                        @foreach ($order as $order)
                                            @foreach ($order->details as $detail)
                                                @foreach (range(1, $detail->qty) as $item)
                                                    @php
                                                        if ($rowCount >= 10) break 3;
                                                        $cardDetails = $detail->cards()->take(1)->skip($loop->index)->first();
                                                        $certNo = $cardDetails->card_number ?? ($lastCertificateNo = getNewCertificate2($lastCertificateNo));
                                                        $serial++;
                                                        $rowCount++;
                                                    @endphp
                                    
                                                    <tr>
                                                        <td class="text-center">
                                                            {{ $serial }}
                                                        </td>
                                                        <td>
                                                            {{ $cardDetails?->item_name ?? $detail->item_name }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.order.view',$order->id) }}">{{ $order->order_number }}</a>
                                                        </td>
                                                        <td>{{ $order->rUser?->name.' '.$order->rUser?->last_name }}</td>
                                                        <td>@if ($cardDetails?->final_grading) {{ $cardDetails?->is_no_grade===1 ? null : $certNo }} @endif</td>
                                                        <td>{{ $cardDetails?->centering }}</td>
                                                        <td>{{ $cardDetails?->corners }} </td>
                                                        <td>{{ $cardDetails?->edges }}</td>
                                                        <td>{{ $cardDetails?->surface }}</td>
                                                        <td>
                                                            @if ($cardDetails?->is_no_grade == 1)
                                                                No Grade
                                                                @if(!empty($cardDetails?->no_grade_reason))
                                                                    <br><small class="text-info">({{ $cardDetails?->no_grade_reason }})</small>
                                                                @endif
                                                            @else
                                                                {{ $cardDetails?->final_grading }}
                                                                @if ($cardDetails?->final_grading)
                                                                    @php($avg_grade = collect([
                                                                        $cardDetails?->centering,
                                                                        $cardDetails?->corners,
                                                                        $cardDetails?->edges,
                                                                        $cardDetails?->surface,
                                                                    ])->filter()->avg())
                                                                    {{-- @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (int) str($cardDetails?->final_grading)->before('.')->value()) --}}
                                                                    @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (float) str($cardDetails?->final_grading)->value())
                                                                    @if($avg_grade == 10)
                                                                        <small class="ml-2 text-info">( {{ $finalGradings[(string)$grading][1] ?? '' }} )</small>
                                                                    @else
                                                                        <small class="ml-2 text-info">( {{ $finalGradings[(string)$grading][0] ?? '' }} )</small>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($cardDetails && $cardDetails->is_no_grade != 1 && $cardDetails->is_graded != 0)
                                                            <div class="dropdown">
                                                                <a href="{{ route('admin.order.certificate.label', ['order' => $cardDetails->order_id, 'id' => $cardDetails->id]) }}" title="print the label" class="btn btn-xs btn-secondary  btn-sm btn-gradient"><i class="fas fa-download"></i> Download</a>
                                                            </div>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-5">
                        <div class="card">
                            <div class="card-body">
                                <h5>Enter Grades for Grading Calculation -TESTING</h5>

                                <form method="POST" action="{{ route('admin.gradingCalculation') }}">
                                    @csrf
                                    <label for="centering">Centering:</label>
                                    <input type="number" name="centering" id="centering" min="1" max="10"
                                        step="0.5" required class="form-control"
                                        value="{{ request()->get('centering') ?? '' }}">

                                    <label for="corners">Corners:</label>
                                    <input type="number" name="corners" id="corners" min="1" max="10"
                                        step="0.5" required class="form-control"
                                        value="{{ request()->get('corners') ?? '' }}">

                                    <label for="edges">Edges:</label>
                                    <input type="number" name="edges" id="edges" min="1" max="10"
                                        step="0.5" required class="form-control"
                                        value="{{ request()->get('edges') ?? '' }}">

                                    <label for="surface">Surface:</label>
                                    <input type="number" name="surface" id="surface" min="1" max="10"
                                        step="0.5" required class="form-control"
                                        value="{{ request()->get('surface') ?? '' }}">
                                    <br>
                                    <button type="submit" class="btn btn-success">Calculate Final Grade
                                        {{ request()->get('finalGrade') ? ': '.request()->get('finalGrade') : '' }} </button>

                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="mb-3">QR Code Generator</h5>
                                <form id="qrGenerator">
                                    @csrf
                                    <div class="row">
                                        <div class="mb-3 col-md-9 form-group">
                                            <label for="qr_link">Link</label>
                                            <input type="text" name="qr_link" id="qr_link" class="form-control" required>
                                        </div>
                                        <div class="mb-3 col-md-3 form-group">
                                            <label for="site_logo">Site Logo</label>
                                            <select name="site_logo" id="site_logo" class="form-control">
                                                <option value="1">Yes</option>
                                                <option value="0">No</option>
                                            </select>
                                        </div>

                                        <div class="col-md-12 mb-3" id="qrPreview" style="display: none;">
                                            <img id="qrImage" src="" alt="Generated QR Code" class="img-thumbnail" width="250">
                                        </div>

                                        <div class="col-12 mb-3">
                                            <button type="button" id="generateQR" class="btn btn-primary">Generate</button>
                                            <button type="button" id="downloadQR" class="btn btn-success" style="display: none;">Download</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        flatpickr("#date_range", {
            mode: "range",
            dateFormat: "d-m-Y",
            altInput: true,
            altFormat: "d-m-Y",
            maxDate: "today",
            defaultDate: "{{ request('date_range') }}".split(" to "),
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    document.getElementById('dateRangeForm').submit();
                }
            }
        });
    });
    document.addEventListener("DOMContentLoaded", function () {
        // Check if "scrollToBottom" exists in the URL query parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get("scrollToBottom") === "true") {
            window.scrollTo({
                top: document.body.scrollHeight,
                behavior: "smooth"
            });
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/qr-code-styling@1.5.0/lib/qr-code-styling.js">
</script>

<script>
    let qrCode

    $('#generateQR').click(function () {
        const link = $('#qr_link').val().trim();
        const siteLogo = $('#site_logo').val();

        if (link === "") {
            alert("Link field is required.");
            return;
        }

        const logo = siteLogo === "1" ? '{{$setting->site_logo}}' : null;

        qrCode = new QRCodeStyling({
            width: 300,
            height: 300,
            type: "canvas",
            data: link,
            image: logo,
            imageOptions: {
                crossOrigin: "anonymous",
                imageSize: 0.9, // 40% of QR size
                margin: 5,
            },
        });

        // Render to temp div, convert to PNG, and inject to <img>
        const tempDiv = document.createElement("div");
        qrCode.append(tempDiv);

        setTimeout(() => {
            qrCode.getRawData("png").then(blob => {
                const url = URL.createObjectURL(blob);
                $('#qrImage').attr('src', url);
                $('#qrPreview').show();
                $('#downloadQR').show().data('url', url);
            });
        }, 300); // slight delay for render
    });

    $('#downloadQR').click(function () {
        qrCode.download({ name: "qr_code", extension: "png" });
    });
</script>

@endpush
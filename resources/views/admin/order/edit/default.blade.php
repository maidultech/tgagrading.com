@extends('admin.layouts.master')
@section('order', 'active')

@section('title')
    {{ $title ?? '' }}
@endsection

@push('style')
    <style>
        .item_table {
            border: 1px solid #EEE;
            border-radius: 10px;
            background: transparent;
            overflow: hidden !important;
        }
    </style>
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
                                        <a href="{{ route('admin.order.index') }}"
                                           class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body  p-0" > <!--table responsive class remove-->
                                <form id="orderForm" action="{{ route('admin.order.update', $order->id) }}"
                                      method="post">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="col px-3">
                                                <div class="heading mb-4">
                                                    <h4>Customer
                                                        : {{$order->rUser?->name.' '.$order->rUser?->last_name}}
                                                        
                                                    </h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-8 offset-lg-2 mt-3">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Item Type</h4>
                                                    {{-- <p>Select the item type that you are submitting to TGA</p> --}}
                                                </div>
                                                <div class="item_list">
                                                    @foreach (config('static_array.item_type') as $key => $item)
                                                        @if($key == $order->item_type)
                                                            <div class="form-check mb-2 p-0">
                                                                <input class="form-check-input d-none"
                                                                       value="{{ $loop->iteration }}" checked
                                                                       type="radio" name="item_type"
                                                                       id="item_{{ $key }}">
                                                                <label
                                                                    class="form-check-label p-3 rounded-4 border w-100"
                                                                    for="item_{{ $key }}">
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="icon me-3">
                                                                            <i class="fa fa-id-card"></i>
                                                                        </div>
                                                                        <div class="ml-2">
                                                                            <div class="name fw-semibold">{{ $item }}
                                                                            </div>
                                                                            <div class="info">Standard size, Pokémon,
                                                                                thicker
                                                                                cards up to 180pt, and mini size sports
                                                                                cards
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Submission Type</h4>
                                                </div>
                                                <div class="item_list">
                                                    @foreach (config('static_array.submission_type') as $key => $item)
                                                        @if($key == $order->submission_type)
                                                            <div class="form-check mb-2 p-0">
                                                                <input class="form-check-input d-none"
                                                                       value="{{ $loop->iteration }}" checked
                                                                       type="radio" name="submission_type"
                                                                       id="item_{{ $key }}">
                                                                <label
                                                                    class="form-check-label p-3 rounded-4 border w-100"
                                                                    for="item_{{ $key }}">
                                                                    <div class="">
                                                                        <div class="name fw-semibold">{{ $item }}</div>
                                                                        <div class="info">The standard authentication
                                                                            and
                                                                            grading service for raw cards
                                                                        </div>
                                                                    </div>
                                                                </label>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Service Level</h4>
                                                </div>
                                                <div class="item_list rounded-top-3 border overflow-hidden">
                                                    <table class="table">
                                                        <thead class="">
                                                            <tr>
                                                                <th class="text-secondary">Service Level</th>
                                                                <th class="text-secondary text-center">Est Turnaround</th>
                                                                <th class="text-secondary text-center">Unit Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($serviceLevels as $serviceLevel)
                                                                @if($serviceLevel->id == $order->service_level_id)
                                                                <tr>
                                                                    <td>
                                                                        {{ $serviceLevel->name }}
                                                                    </td>
                                                                    <td class="text-center">
                                                                        {{ $serviceLevel->estimated_days }}<br>
                                                                        <span class="text-center"
                                                                            style="font-size: 0.85rem"> Business Days
                                                                        </span>
                                                                    </td>

                                                                    <td class="text-center">
                                                                        {{ getDefaultCurrencySymbol() }} <span
                                                                            class="custom-price">{{ $serviceLevel->extra_price + $order->rPlan->price }}</span>
                                                                    </td>
                                                                </tr>
                                                                @endif
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div> --}}

                                    @include('admin.order.edit.include.item_entry')

                                    @include('admin.order.edit.include.common')

                                    <div class="row">
                                        <div class="col-12 text-center">
                                            <button form="orderForm" type="submit" class="btn btn-primary my-2">
                                                Update Order
                                            </button>
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
    </div>
@endsection


@push('script')

<script>
    $(document).ready(function () {
        $('#addRowBtn').on('click', function () {
            let newRow = `
                <div class="row mb-2 charge-row">
                    <div class="col-md-6 mt-1">
                        <input type="text" class="form-control" name="details[]" placeholder="Details">
                    </div>
                    <div class="col-md-5 mt-1">
                        <input type="number" class="form-control" name="price[]" placeholder="Amount" step="any">
                    </div>
                    <div class="col-md-1 mt-1 p-0">
                        <button type="button" class="btn btn-danger removeRowBtn" style="padding:8px;" >
                            <i class="fa-solid fa-minus py-1"></i>
                            </button>
                    </div>
                </div>`;
            $('#chargesContainer').append(newRow);
        });

        // Use event delegation for dynamically added elements
        $(document).on('click', '.removeRowBtn', function () {
            $(this).closest('.charge-row').remove();
        });
    });
</script>
@endpush

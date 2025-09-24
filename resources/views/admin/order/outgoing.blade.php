@extends('admin.layouts.master')
@section('orderDropdown', 'menu-open')
@section('outgoing-order', 'active')

@section('title') {{ $title ?? '' }} @endsection
@php
     $categories = config('static_array.categories');
     $item_type = config('static_array.item_type');
     $submission_type = config('static_array.submission_type');
     $status = config('static_array.status');
@endphp
@section('content')
    <div class="content-wrapper">

        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row d-flex justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <h4 class="card-title">{{ $title ?? '' }}</h4>
                                    </div>
                                    {{-- <div class="col-6 text-right">
                                        <a href="{{ route('admin.order.create') }}" class="btn btn-sm btn-primary btn-gradient">{{__('Add New')}}</a>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                 <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{__('Order Info')}}</th>
                                            <th>{{__('Transaction Date')}}</th>
                                            <th>{{__('Customer')}}</th>
                                            <th>{{__('Card Info')}}</th>

                                            <th>{{__('Payment Info')}}</th>
                                            <th>{{__('Status')}}</th>
                                            <th>{{__('Actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{__('Order Info')}}</th>
                                            <th>{{__('Transaction Date')}}</th>
                                            <th>{{__('Customer')}}</th>
                                            <th>{{__('Card Info')}}</th>

                                            <th>{{__('Payment Info')}}</th>
                                            <th>{{__('Status')}}</th>
                                            <th>{{__('Actions')}}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($rows as $row)
                                            <tr @if($row->plan_id == 15) style="background: #e3e3adde; box-shadow: 0 5px 16px rgba(0, 0, 0, 0.1);" @endif
                                                @if($row->plan_id == 16) style="background-color: #ff00002b; box-shadow: 0 5px 16px rgba(0, 0, 0, 0.1);" @endif>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <div>
                                                        <p class="mb-0">#{{ $row->order_number }}</p>
                                                        @if($row->item_type != 0)
                                                        <p class="mb-0">{{ $item_type[$row->item_type] }}</p>
                                                        @endif
                                                        @if($row->submission_type != 0)
                                                        <p class="mb-0">{{ $submission_type[$row->submission_type] }}</p>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td data-order="{{ $row->pay_date }}">
                                                    {{ date('d F, Y h:i A', strtotime($row->pay_date)) }}
                                                </td>
                                                <td>
                                                    <div>
                                                        <p class="mb-0">{{ $row->rUser->name }} {{ $row->rUser->last_name }}</p>
                                                        <p class="mb-0"><a href="mailto:{{ $row->rUser->email }}">{{ $row->rUser->email }}</a></p>
                                                        <p class="mb-0"><a href="tel:{{ $row->rUser->dial_code }}{{ $row->rUser->phone }}">{{ $row->rUser->dial_code }}{{ $row->rUser->phone }}</a></p>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <p class="mb-0">No. Card: {{ $row->details_sum_qty ?? 0  }} </p>
                                                        {{-- <p class="mb-0"> {{ $row->service_level_name }} </p> --}}
                                                        {{-- <p class="mb-0">Est Day: {{ $row->est_day }} Days </p> --}}
                                                    </div>
                                                </td>

                                                <td>
                                                    <div>
                                                        @if ( $row->payment_status == 1 )
                                                            <p class="mb-0 text-primary">Paid</p>
                                                        @else
                                                            <p class="mb-0 text-warning">Due</p>
                                                        @endif
                                                        @if($row->transaction)
                                                            <p class="mb-0">
                                                                <a href="">{{ $row->transaction->payment_method }} #{{ $row->transaction->transaction_number }}</a>
                                                            </p>
                                                        @endif
                                                        <p class="mb-0">
                                                        @if($row->is_custom_order == 1)
                                                        Custom Order
                                                        @else
                                                            {{ $row->plan_name ?? $row->rPlan?->name }}
                                                        @endif
                                                        - {{ getDefaultCurrencySymbol() }}@if($row->payment_status == 1) {{ number_format($row->transaction->amount,2) }} @else {{ number_format($row->total_order_qty * $row->getPlanPrice(), 2) }} @endif
                                                        </p>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($row->status == 35)
                                                        <strong class="text-info" style="text-transform: capitalize;">
                                                            @if($row->shipping_method != 'local_pickup')
                                                                Update with tracking number
                                                            @else
                                                                Local Pickup
                                                            @endif
                                                        </strong>
                                                    @else
                                                        <strong class="text-info" style="text-transform: capitalize;">
                                                            @if($row->shipping_method != 'local_pickup')
                                                                Order Shipped
                                                            @else
                                                                Local Pickup
                                                            @endif
                                                        </strong>
                                                    @endif
                                                    @if ($row->admin_tracking_id && $row->shipping_method != 'local_pickup')
                                                    <br>
                                                        <a href="{{ route('user.order.tracking',$row->id) }}">Track Order</a>
                                                    @elseif ($row->shipping_notes && $row->shipping_method == 'local_pickup')
                                                    <br>
                                                        <a href="{{ route('user.order.tracking',$row->id) }}">Track Order</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button" data-toggle="dropdown" aria-expanded="false">
                                                            {{__('messages.common.actions')}}
                                                        </button>

                                                        <div class="dropdown-menu">
                                                            <a href="{{ route('admin.outgoing.view',$row->id) }}" class="dropdown-item"><i class="fa fa-eye"></i> {{__('View Order')}}</a>
                                                            {{-- @if (Auth::user()->can('admin.order.edit'))
                                                                <a href="{{route('admin.order.edit', $row->id)}}" class="dropdown-item"><i class="fa fa-pencil"></i> {{__('Update Status')}}</a>
                                                            @endif --}}
                                                            <a href="{{route('admin.order.shipping.method',$row->id)}}" class="dropdown-item"><i class="fa-solid fa-truck-fast"></i>
                                                                @if($row->shipping_method != 'local_pickup')
                                                                    Shipping Information
                                                                @else
                                                                    Local Pickup
                                                                @endif
                                                            </a>
                                                            
                                                            @if (Auth::user()->can('admin.order.delete'))
                                                                <a href="{{route('admin.order.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('Delete')}}</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
    document.getElementById('orderStatusSelect').addEventListener('change', function () {
        const selectedValue = this.value;
        const url = new URL(window.location.href);
        if (selectedValue) {
            url.searchParams.set('status', selectedValue);
        } else {
            url.searchParams.delete('status');
        }
        window.location.href = url.toString();
    });
</script>
@endpush
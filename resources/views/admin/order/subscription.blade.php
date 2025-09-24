@extends('admin.layouts.master')

@section('subscriptions', 'menu-open active') 

@if(request()->status == 0 && !is_null(request()->status))
    @section('order-pending', 'active')
@elseif(request()->status == 10)
    @section('order-received', 'active')
@elseif(request()->status == 15)
    @section('grading-processing', 'active')
@elseif(request()->status == 20)
    @section('grading-complete', 'active')
@elseif(request()->status == 25)
    @section('encapsulation-processing', 'active')
@elseif(request()->status == 30)
    @section('encapsulation-complete', 'active')
@elseif(request()->status == 35)
    @section('order-shipping', 'active')
{{-- @elseif(request()->status == 40)
    @section('order-shipped', 'active') --}}
@endif









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
                                    <div class="col-md-3 text-md-right">
                                        <div class="row d-flex align-items-center">
                                            <div class="col-4">
                                                <span>Status :</span>
                                            </div>
                                            <div class="col-8">
                                                <select name="status" class="form-control form-select" id="orderStatusSelect">
                                                    <option value="" @selected(!isset(request()->status))>All</option>
                                                    @foreach (config('static_array.status') as $key => $row)
                                                        @if($key != 40)
                                                            <option value="{{ $key }}" @selected(isset(request()->status) && $key == request()->status)>{{ $row }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
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
                                            <th>{{__('Customer')}}</th>
                                            <th>{{__('Transaction Date')}}</th>
                                            <th>{{__('Card Info')}}</th>
                                            <th>{{__('Payment Info')}}</th>
                                            <th>{{__('Payment Status')}}</th>
                                            <th>{{__('Actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{__('Order Info')}}</th>
                                            <th>{{__('Customer')}}</th>
                                            <th>{{__('Transaction Date')}}</th>
                                            <th>{{__('Card Info')}}</th>
                                            <th>{{__('Payment Info')}}</th>
                                            <th>{{__('Payment Status')}}</th>
                                            <th>{{__('Actions')}}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach($rows as $row)
                                            <tr>
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
                                                <td>
                                                    <div>
                                                        <p class="mb-0">{{ $row->rUser->name }} {{ $row->rUser->last_name }}</p>
                                                        <p class="mb-0"><a href="mailto:{{ $row->rUser->email }}">{{ $row->rUser->email }}</a></p>
                                                        <p class="mb-0"><a href="tel:{{ $row->rUser->dial_code }}{{ $row->rUser->phone }}">{{ $row->rUser->dial_code }}{{ $row->rUser->phone }}</a></p>
                                                    </div>
                                                </td>
                                                <td data-order="{{ $row->pay_date }}">
                                                    {{ date('d F, Y h:i A', strtotime($row->pay_date)) }}
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
                                                        - {{getDefaultCurrencySymbol()}} {{ number_format($row->total_price,2) }}
                                                        </p>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if ( $row->payment_status == 1 )
                                                        <p class="mb-0 text-primary">Paid</p>
                                                    @else
                                                        <p class="mb-0 text-warning">Due</p>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button" data-toggle="dropdown" aria-expanded="false">
                                                            {{__('messages.common.actions')}}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            <a href="{{ route('admin.order.view',$row->id) }}" class="dropdown-item"><i class="fa fa-eye"></i> {{__('Invoice')}}</a>

                                                            @if($row->item_type != 0)
                                                                @if (Auth::user()->can('admin.order.edit'))
                                                                    <a href="{{route('admin.order.edit', $row->id)}}" class="dropdown-item"><i class="fa fa-pencil"></i> {{__('Update Status')}}</a>
                                                                @endif
                                                            
                                                            {{-- @if (Auth::user()->can('admin.order.delete'))
                                                                <a href="{{route('admin.order.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('Delete')}}</a>
                                                            @endif --}}
                                                                @if (Auth::user()->can('admin.order.edit'))
                                                                    <a href="{{route('admin.order.certificate.index',$row->id)}}" class="dropdown-item"><i class="fas fa-shopping-cart"></i>
                                                                        Grading @if($row->cards && count($row->cards)>0) <i class="fa fa-check"></i> @else <i class="fa fa-close"></i> @endif
                                                                    </a>
                                                                @endif
                                                                @if (Auth::user()->can('admin.order.edit') && $row->cards && count($row->cards)>0)
                                                                    <a href="{{route('admin.order.download',$row->id)}}" class="dropdown-item"><i class="fas fa-download"></i>
                                                                        Label
                                                                    </a>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            
                                            <!-- View Modal -->
                                            {{-- <div class="modal fade" id="view{{ $row->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary">
                                                            <h5 class="modal-title">View Orders List</h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="view_modal_content">
                                                                <label>Order Number : </label>
                                                                <span class="text-dark">#{{ $row->order_number  }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Customer Name : </label>
                                                                <span class="text-dark">{{ $row->rUser->name }} {{ $row->rUser->last_name }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Customer Email : </label>
                                                                <a href="mailto: {{ $row->rUser->email }}" class="text-info">{{ $row->rUser->email }}</a>
                                                            </div>
                                                            @if($row->item_type != 0)
                                                                <div class="view_modal_content">
                                                                    <label>Item Name : </label>
                                                                    <span class="text-dark">{{ $item_type[$row->item_type] }}</span>
                                                                </div>
                                                            @endif
                                                            @if($row->submission_type != 0)
                                                            <div class="view_modal_content">
                                                                <label>Submission Type : </label>
                                                                <span class="text-dark">{{ $submission_type[$row->submission_type] }}</span>
                                                            </div>
                                                            @endif

                                                            <div class="view_modal_content">
                                                                <label>Quantity : </label>
                                                                <span class="text-dark">{{ $row->details_sum_qty  }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Plan : </label>
                                                                <span class="text-dark">
                                                                    @if($row->is_custom_order == 1)
                                                                    Custom Order
                                                                    @else
                                                                     {{ $row->rPlan?->name }}
                                                                    @endif

                                                                </span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Contact Type : </label>
                                                                <span class="text-dark">
                                                                    @if($row->contact_type == 1)
                                                                        Contact Sale
                                                                    @else
                                                                        General
                                                                    @endif
                                                                </span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Date : </label>
                                                                <span class="text-dark"> {{ date('d M, Y h:i A', strtotime($row->created_at)) }} </span>
                                                            </div>

                                                            <div class="message_content">
                                                                <label>Message : </label>
                                                                <span class="text-dark"> {{ $row->note }} </span>
                                                            </div>
                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
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
{{--  
<script>
    $(document).ready(function () {
        $('#dataTables').DataTable({
            "ordering": true // Enable sorting
        });
    });
</script>  --}}

<script>
    $(document).ready(function () {
        if (!$.fn.DataTable.isDataTable('#dataTables')) {
            $('#dataTables').DataTable({
                "ordering": true
            });
        }
    });
</script>


@endpush
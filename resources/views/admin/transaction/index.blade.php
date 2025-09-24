@extends('admin.layouts.master')
@section('transaction', 'active')

@section('title') {{ $title ?? '' }} @endsection

@php
    $localLanguage = Session::get('languageName');
@endphp
@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        @if ($coupon_flag == true)
                            <span class="text-danger">Note: This coupon has been used {{$used_coupon_count}} times. If this number doesn’t match the total transactions, some orders using this coupon maybe unpaid or deleted </span>
                        @endif
                        <div class="card @if ($coupon_flag == true) mt-3 @endif">
                            <div class="card-header">
                                <h4 class="card-title">{{ $title ?? __('messages.transaction.all_transaction') }}</h4>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                 <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{ __('messages.transaction.trans_id') }}</th>
                                            <th>{{ __('messages.common.first_name') }}</th>
                                            <th>{{ __('messages.common.last_name') }}</th>
                                            <th>{{__('messages.transaction.plan_name')}}</th>
                                            <th>{{ __('messages.transaction.payment_gateway') }}</th>
                                            <th>{{ __('messages.common.amount') }}</th>
                                            <th>{{__('messages.transaction.transactions_date')}}</th>
                                            <th>{{ __('messages.common.status') }}</th>
                                            <th>{{ __('messages.common.actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{ __('messages.transaction.trans_id') }}</th>
                                            <th>{{ __('messages.common.first_name') }}</th>
                                            <th>{{ __('messages.common.last_name') }}</th>
                                            <th>{{__('messages.transaction.plan_name')}}</th>
                                            <th>{{ __('messages.transaction.payment_gateway') }}</th>
                                            <th>{{ __('messages.common.amount') }}</th>
                                            <th>{{__('messages.transaction.transactions_date')}}</th>
                                            <th>{{ __('messages.common.status') }}</th>
                                            <th>{{ __('messages.common.actions') }}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($transactions as $item)
                                            <tr>
                                                <td>{{$loop->iteration}}</td>
                                                <td>{{$item->transaction_number}}</td>
                                                <td>
                                                    @if (Auth::user()->can('admin.customer.view'))
                                                    <a class="text-capitalize" href="{{ route('admin.customer.index', ['openModal' => $item->user->id]) }}">{{$item->user->name}}</a>
                                                    @else
                                                    <a class="text-capitalize" href="javascript:void(0)">{{$item->user->name}}</a>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if (Auth::user()->can('admin.customer.view'))
                                                    <a class="text-capitalize" href="{{ route('admin.customer.index', ['openModal' => $item->user->id]) }}">{{$item->user->last_name}}</a>
                                                    @else
                                                    <a class="text-capitalize" href="javascript:void(0)">{{$item->user->last_name}}</a>
                                                    @endif
                                                </td>
                                                <td>{{ $item->plan->name ?? 'N/A'}}</td>
                                                <td>{{$item->payment_method}}</td>
                                                <td>{{getDefaultCurrencySymbol()}} {{ number_format($item->amount, 2) }}</td>
                                                <td>
                                                    {{ date('d F, Y', strtotime($item->order?->pay_date ?? $item->pay_date)) }}
                                                </td>
                                                <td>
                                                    @if ($item->status == '1')
                                                    <span class="text-success">{{__('messages.common.paid')}}</span>
                                                    @else
                                                    <span class="text-warning">{{__('messages.common.due')}}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.transaction.invoice', $item->id)}}"
                                                        class="btn btn-xs btn-primary btn-gradient">{{ __('messages.common.invoice') }}
                                                        <i class="fa fa-download"></i></a>
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

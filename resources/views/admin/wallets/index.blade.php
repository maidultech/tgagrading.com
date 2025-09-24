@extends('admin.layouts.master')
@section('wallet', 'active')

@section('title') Users Wallet @endsection

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">Manage User Wallet Balance</h3>
                                    </div>
                                    {{-- <div class="col-4">
                                        <form action="{{ route('admin.wallet.index') }}" method="GET" class="form-inline">
                                            <div class="form-group mr-2">
                                                <select name="customer_id" id="customer_id" class="form-control form-control-sm">
                                                    <option value="">All Customers</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>
                                                            {{ $customer->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group mr-2">
                                                <select name="type" id="type" class="form-control form-control-sm">
                                                    <option value="">All Types</option>
                                                    <option value="credit" @selected(request('type') == 'credit')>Credit</option>
                                                    <option value="debit" @selected(request('type') == 'debit')>Debit</option>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
                                                <a type="submit" href="{{ url()->current() }}" class="btn btn-danger btn-sm">Reset</a>
                                            </div>
                                        </form>
                                    </div> --}}
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Customer Name</th>
                                            <th>Customer Email</th>
                                            <th>Balance</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>Customer Name</th>
                                            <th>Customer Email</th>
                                            <th>Balance</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>

                                    <tbody>
                                        @foreach ($customers as $key => $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if (Auth::user()->can('admin.customer.view'))
                                                    <a class="text-capitalize" href="{{ route('admin.customer.index', ['openModal' => $row->id]) }}">
                                                        {{ $row->name ?? '' }}&nbsp;{{ $row->last_name ?? '' }}
                                                    </a>
                                                    @else
                                                    <a class="text-capitalize" href="javascript:void(0)">{{ $row->name ?? '' }}&nbsp;{{ $row->last_name ?? '' }}</a>
                                                    @endif
                                                </td>
                                                <td><a href="mailto:{{$row->email}}">{{ $row->email }}</a></td>
                                                <td>{{getDefaultCurrencySymbol()}} {{ number_format($row->wallet_balance, 2) }}</td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if (Auth::user()->can('admin.wallet.edit'))
                                                                <a href="{{ route('admin.wallet.balance', $row->id) }}" class="dropdown-item">
                                                                    <i class="fa fa-pencil"></i> Update Balance
                                                                </a>
                                                            @endif
                                                            @if (Auth::user()->can('admin.wallet.view') && $row->wallets_count > 0)
                                                                <a href="{{ route('admin.wallet.details', $row->id) }}" class="dropdown-item">
                                                                    <i class="fa fa-eye"></i> View Transaction
                                                                </a>
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
@endpush

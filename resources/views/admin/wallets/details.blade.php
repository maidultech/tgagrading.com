@extends('admin.layouts.master')
@section('wallet', 'active')

@section('title') Wallet Transactions @endsection

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
                                        <h3 class="card-title">Wallet Transactions of <b>{{ $customer->name }} {{ $customer->last_name }}</b></h3>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('admin.wallet.index') }}" class="btn btn-gradient btn-sm">
                                            <i class="fa fa-arrow-left"></i> Back
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Created By</th>
                                            {{-- <th>Updated By</th> --}}
                                            <th>Transaction Date</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>Amount</th>
                                            <th>Description</th>
                                            <th>Created By</th>
                                            {{-- <th>Updated By</th> --}}
                                            <th>Transaction Date</th>
                                        </tr>
                                    </tfoot>

                                    <tbody>
                                        @foreach ($rows as $key => $transaction)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    @if ($transaction->amount >= 0)
                                                        <span class="text-success">{{ getDefaultCurrencySymbol() }} {{ number_format($transaction->amount, 2) }}</span>
                                                    @else
                                                        <span class="text-danger">{{ getDefaultCurrencySymbol() }} {{ number_format($transaction->amount, 2) }}</span>
                                                    @endif
                                                </td>
                                                <td>{{ $transaction->description ?? 'N/A' }}</td>
                                                <td>{{ $transaction->createdBy->name ?? 'N/A' }}</td>
                                                {{-- <td>{{ $transaction->updatedBy->name ?? 'N/A' }}</td> --}}
                                                <td>{{ $transaction->created_at->format('Y-m-d H:i:s') }}</td>
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
@extends('admin.layouts.master')
@section('wallet', 'active')
@section('title'){{ $data['title'] ?? 'Create Wallet Transaction' }} @endsection

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
                                        <h3 class="card-title">Create Wallet Transaction</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.wallet.index') }}" class="btn btn-secondary btn-gradient btn-sm">Back</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-5">
                                        <form action="{{ route('admin.wallet.store') }}" method="POST">
                                            @csrf
        
                                            <div class="form-group">
                                                <label for="customer_id">Customer</label>
                                                <select name="customer_id" id="customer_id" class="form-control" required>
                                                    <option value="">Select Customer</option>
                                                    @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}" @selected(old('customer_id') == $customer->id)>
                                                            {{ $customer->name.' '.$customer->last_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
        
                                            <div class="form-group">
                                                <label for="amount">Amount</label>
                                                <input type="number" name="amount" id="amount" class="form-control" step="0.01" value="{{ old('amount') }}" required>
                                            </div>
        
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea name="description" id="description" class="form-control" rows="3" placeholder="Enter description">{{ old('description') }}</textarea>
                                            </div>
        
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-success btn-sm">Submit</button>
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
    </div>
@endsection

@push('script')
@endpush
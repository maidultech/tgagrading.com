@extends('admin.layouts.master')
@section('coupon', 'active')
@section('title'){{ $data['title'] ?? '' }} @endsection

@php
    $rows = $data['rows'];
@endphp

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
                                        <h3 class="card-title">Manage Coupons</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            @if (Auth::user()->can('admin.coupon.create'))
                                                <a href="{{ route('admin.coupon.create') }}" class="btn btn-primary btn-gradient btn-sm">Add New</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Discount</th>
                                            <th>Discount Code</th>
                                            <th>Expire</th>
                                            <th>No of redeem per user</th>
                                            <th>Total no of redeem</th>
                                            <th>Total Usages</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Type</th>
                                            <th>Discount</th>
                                            <th>Discount Code</th>
                                            <th>Expire</th>
                                            <th>No of redeem per user</th>
                                            <th>Total no of redeem</th>
                                            <th>Total Usages</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </tfoot>

                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $row->name }}</td>
                                                <td>{{ ucfirst($row->discount_type) }}</td>
                                                <td>{{ $row->discount_value }}</td>
                                                <td>{{ $row->discount_code }}</td>
                                                <td>{{ $row->expiration_date }}</td>
                                                <td>{{ $row->max_redemptions_per_user }}</td>
                                                <td>{{ $row->max_uses }}</td>
                                                <td>{{ $row->total_uses }}</td>
                                                <td>
                                                    @if ($row->status == 1)
                                                        <span class="text-success">Active</span>
                                                    @else
                                                        <span class="text-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                            Actions
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if (Auth::user()->can('admin.coupon.edit'))
                                                                <a href="{{ route('admin.coupon.edit', $row->id) }}" class="dropdown-item">
                                                                    <i class="fa fa-pencil"></i> Edit
                                                                </a>
                                                            @endif
                                                            
                                                            @if (Auth::user()->can('admin.transaction.view'))
                                                                <a href="{{ route('admin.transaction.index', ['coupon_id' => $row->id]) }}" class="dropdown-item">
                                                                    <i class="fa fa-dollar"></i> Transactions
                                                                </a>
                                                            @endif
                                                            
                                                            @if (Auth::user()->can('admin.coupon.delete'))
                                                                <a href="{{ route('admin.coupon.delete', $row->id) }}" id="deleteData" class="dropdown-item">
                                                                    <i class="fa fa-trash"></i> Delete
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
@extends('admin.layouts.master')
@section('coupon', 'active')
@section('title') Edit Coupon @endsection

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
                                        <h3 class="card-title">Edit Coupon</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.coupon.index') }}" class="btn btn-secondary btn-sm">Back</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <div class="col-md-9">
                                        <form action="{{ route('admin.coupon.update', $data['row']->id) }}" method="POST">
                                            @csrf
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="name">Name <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" id="name" class="form-control" 
                                                            placeholder="Enter Coupon Name" value="{{ old('name', $data['row']->name) }}" required>
                                                        @error('name')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="discount_type">Type <span class="text-danger">*</span></label>
                                                        <select name="discount_type" id="discount_type" class="form-control" required>
                                                            <option value="Percent" {{ old('discount_type', $data['row']->discount_type) == 'Percent' ? 'selected' : '' }}>Percent</option>
                                                            <option value="Fixed" {{ old('discount_type', $data['row']->discount_type) == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                                                        </select>
                                                        @error('discount_type')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="discount_value">Discount <span class="text-danger">*</span></label>
                                                        <input type="number" name="discount_value" id="discount_value" class="form-control" 
                                                            placeholder="Enter Discount Value" step="0.01" value="{{ old('discount_value', $data['row']->discount_value) }}" required>
                                                        @error('discount_value')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="discount_code">Discount use code <span class="text-danger">*</span></label>
                                                        <input type="text" name="discount_code" id="discount_code" class="form-control" 
                                                            placeholder="Enter Discount Code" value="{{ old('discount_code', $data['row']->discount_code) }}" required>
                                                        @error('discount_code')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="expiration_date">Expire <span class="text-danger">*</span></label>
                                                        <input type="datetime-local" name="expiration_date" id="expiration_date" 
                                                            class="form-control" value="{{ old('expiration_date', $data['row']->expiration_date) }}" required>
                                                        @error('expiration_date')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="max_redemptions_per_user">No of redeem per user <span class="text-danger">*</span></label>
                                                        <input type="number" name="max_redemptions_per_user" id="max_redemptions_per_user" class="form-control" required
                                                            placeholder="Enter No of Redeem Per User" value="{{ old('max_redemptions_per_user', $data['row']->max_redemptions_per_user) }}">
                                                        @error('max_redemptions_per_user')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="max_uses">Total no of redeem <span class="text-danger">*</span></label>
                                                        <input type="number" name="max_uses" id="max_uses" class="form-control" required
                                                            placeholder="Enter Total No of Redeem" value="{{ old('max_uses', $data['row']->max_uses) }}">
                                                        @error('max_uses')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
        
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="status">Status <span class="text-danger">*</span></label>
                                                        <select name="status" id="status" class="form-control" required>
                                                            <option value="1" {{ old('status', $data['row']->status) == '1' ? 'selected' : '' }}>Active</option>
                                                            <option value="0" {{ old('status', $data['row']->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                        @error('status')
                                                            <span class="text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
        
                                            <div class="form-group text-center">
                                                <button type="submit" class="btn btn-success">Update</button>
                                                <button type="reset" class="btn btn-secondary">Reset</button>
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
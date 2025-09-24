@extends('admin.layouts.master')
@section('item-brands', 'active')

@section('title')
    {{ __('Edit Brand') }}
@endsection

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title" style="line-height: 36px;">{{ __('Brand Info') }}</h3>
                            <a href="{{ route('admin.item-brand.index') }}"
                                class="btn bg-primary float-right d-flex align-items-center justify-content-center"><i
                                    class="fas fa-arrow-left"></i>&nbsp;{{ __('Back') }}</a>
                        </div>
                        <div class="row py-4 px-3 justify-content-center">
                            <div class="col-md-6">
                                <form method="POST" action="{{ route('admin.item-brand.update', $row->id) }}"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="up_name" class="form-label">Brand Name <span
                                                class="text-danger">*</span></label>
                                        <input name="name" id="up_name" placeholder="Write Brand Name"
                                            type="text" class="form-control" value="{{ $row->name }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status <span
                                                class="text-danger">*</span></label>
                                        <select class="form-control" name="status" id="status" required>
                                            <option selected="" value="1"
                                                @if ($row->status == 1) selected @endif>Active</option>
                                            <option value="0"
                                                @if ($row->status == 0) selected @endif>Inactive</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="order_id" class="form-label">Order Id </label>
                                        <input name="order_id" id="order_id" placeholder="Order Id Number"
                                            type="number" class="form-control" value="{{ $row->order_id }}">
                                    </div>

                                    <div>
                                        <button type="submit"
                                            class="btn btn-success">Update</button>
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
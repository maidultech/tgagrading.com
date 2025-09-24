@extends('admin.layouts.master')
@section('service-level', 'active')

@section('title') {{ $title ?? __('messages.plan.create_plan') }} @endsection
@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                        <h4 class="card-title">{{ $title ?? __('messages.plan.create_plan') }}</h4>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('admin.service.level.index') }}" class="btn btn-sm btn-primary btn-gradient">{{__('messages.common.back')}}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row justify-content-center">
                                    <form class="col-md-8" action="{{ route('admin.service.level.store') }}" method="POST">
                                        @csrf

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="name">Name <span class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="estimated_days">Estimated Days <span class="text-danger">*</span></label>
                                                <input type="number" min="0" name="estimated_days" id="estimated_days" class="form-control" value="{{ old('estimated_days', 0) }}" required>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6 input-group">
                                                <label for="extra_price" class="w-100">Extra Price <span class="text-danger">*</span></label>
                                                <input type="number" min="0" name="extra_price" id="extra_price" class="form-control" value="{{ old('extra_price', 0) }}" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text">
                                                        {{ getDefaultCurrencySymbol() }}
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group col-md-6">
                                                <label for="status">Status <span class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control" required>
                                                    <option value="1" {{ old('status',1) == '1' ? 'selected' : '' }}>Active</option>
                                                    <option value="0" {{ old('status',1) == '0' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-row">
                                            <div class="form-group col-md-6">
                                                <label for="order_id">Order ID</label>
                                                <input type="number" min="1" name="order_id" id="order_id" class="form-control" value="{{ old('order_id') }}">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-success">Save</button>
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
        $(document).ready(function() {

        });
    </script>
@endpush

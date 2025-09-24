@extends('admin.layouts.master')
@section('order', 'active')

@section('title') {{ $title ?? '' }} @endsection

@push('style')
@endpush
@section('content')
    <div class="content-wrapper">    
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title">{{ $title ?? '' }}</h4>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('admin.order.index') }}" class="btn btn-sm btn-primary btn-gradient">{{__('Back')}}</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body table-responsive">
                                <div class="container p-5">
                                    <div class="row">
                                        <div class="col-12 h5">Select Shipping method</div>
                                        <div class="col-12 my-3">
                                            <div class="checkout_wrapper">
                                                <div class="item_list row">
                                                    @foreach ($brands as $key => $item)
                                                        <div class="form-check mb-2 p-0 col-md-5 mr-2">
                                                            <input class="form-check-input d-none"
                                                                value="{{ $item->id }}"
                                                                type="radio" name="shipping_type"
                                                                id="shipping_{{ $item->id }}">
                                                            <label class="form-check-label p-3 rounded-4 border w-100"
                                                                for="shipping_{{ $item->id }}">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon mr-3"> 
                                                                        <img src="{{ getPhoto($item->image) }}" class="img img-fluid rounded" alt="{{ $item->name }}" style="width: 50px; height: 50px;">
                                                                    </div>
                                                                    <div class="name fw-semibold">{{ $item->name }}</div>
                                                                </div>
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row" id="shipping_info" style="display: none;">
                                        <form action="" method="post" class="col-12">
                                            @csrf
                                            <!-- Shipper Details -->
                                            <h5>Shipper Details</h5>

                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="shipper_name" class="form-label">Shipper Name <span class="text-danger">*</span></label>
                                                    <input name="shipper_name" value="{{ old('shipper_name') }}" type="text" class="form-control" id="shipper_name"
                                                        placeholder="Shipper Name" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="shipper_address" class="form-label">Shipper Address <span class="text-danger">*</span></label>
                                                    <input name="shipper_address" value="{{ old('shipper_address') }}" type="text" class="form-control"
                                                        id="shipper_address" placeholder="Shipper Address" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="shipper_city" class="form-label">Shipper City <span class="text-danger">*</span></label>
                                                    <input name="shipper_city" value="{{ old('shipper_city') }}" type="text" class="form-control" id="shipper_city"
                                                        placeholder="City" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="shipper_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                                    <input name="shipper_postal_code" value="{{ old('shipper_postal_code') }}" type="text" class="form-control"
                                                        id="shipper_postal_code" placeholder="Postal Code" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="shipper_country" class="form-label">Country Code <span class="text-danger">*</span></label>
                                                    <input name="shipper_country" value="{{ old('shipper_country') }}" type="text" class="form-control"
                                                        id="shipper_country" placeholder="Country Code" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="shipper_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                    <input name="shipper_phone" value="{{ old('shipper_phone') }}" type="text" class="form-control" id="shipper_phone"
                                                        placeholder="Phone Number" required>
                                                </div>
                                            </div>
                                        
                                            <!-- Ship To Details -->
                                            <h5>Ship To Details</h5>
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="recipient_name" class="form-label">Recipient Name <span class="text-danger">*</span></label>
                                                    <input name="recipient_name" value="{{ old('recipient_name') }}" type="text" class="form-control"
                                                        id="recipient_name" placeholder="Recipient Name" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="recipient_address" class="form-label">Recipient Address <span class="text-danger">*</span></label>
                                                    <input name="recipient_address" value="{{ old('recipient_address') }}" type="text" class="form-control"
                                                        id="recipient_address" placeholder="Recipient Address" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="recipient_city" class="form-label">City <span class="text-danger">*</span></label>
                                                    <input name="recipient_city" value="{{ old('recipient_city') }}" type="text" class="form-control"
                                                        id="recipient_city" placeholder="City" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="recipient_postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                                    <input name="recipient_postal_code" value="{{ old('recipient_postal_code') }}" type="text" class="form-control"
                                                        id="recipient_postal_code" placeholder="Postal Code" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="recipient_country" class="form-label">Country Code (e.g., US) <span class="text-danger">*</span></label>
                                                    <input name="recipient_country" value="{{ old('recipient_country') }}" type="text" class="form-control"
                                                        id="recipient_country" placeholder="Country Code" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="recipient_phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                                    <input name="recipient_phone" value="{{ old('recipient_phone') }}" type="text" class="form-control"
                                                        id="recipient_phone" placeholder="Phone Number" required>
                                                </div>
                                            </div>
                                        
                                            <!-- Package Details -->
                                            <h5>Package Details</h5>
                                            <div class="row">
                                                <div class="mb-3 col-md-6">
                                                    <label for="package_weight" class="form-label">Package Weight (lbs) <span class="text-danger">*</span></label>
                                                    <input name="package_weight" value="{{ old('package_weight') }}" type="number" step="0.01" class="form-control"
                                                        id="package_weight" placeholder="Weight" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="package_length" class="form-label">Package Length (in) <span class="text-danger">*</span></label>
                                                    <input name="package_length" value="{{ old('package_length') }}" type="number" step="0.01" class="form-control"
                                                        id="package_length" placeholder="Length" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="package_width" class="form-label">Package Width (in) <span class="text-danger">*</span></label>
                                                    <input name="package_width" value="{{ old('package_width') }}" type="number" step="0.01" class="form-control"
                                                        id="package_width" placeholder="Width" required>
                                                </div>
                                                <div class="mb-3 col-md-6">
                                                    <label for="package_height" class="form-label">Package Height (in) <span class="text-danger">*</span></label>
                                                    <input name="package_height" value="{{ old('package_height') }}" type="number" step="0.01" class="form-control"
                                                        id="package_height" placeholder="Height" required>
                                                </div>
                                            </div>
                                        
                                            <!-- Payment Information -->
                                            <h5>Payment Information</h5>
                                            <div class="row">
                                                <div class="mb-3 col-md-12">
                                                    <label for="payment_type" class="form-label">Payment Type <span class="text-danger">*</span></label>
                                                    <select name="payment_type" id="payment_type" class="form-control" required>
                                                        <option value="">Select Payment Type</option>
                                                        <option value="prepaid" {{ old('payment_type') == 'prepaid' ? 'selected' : '' }}>Prepaid</option>
                                                        <option value="freight_collect" {{ old('payment_type') == 'freight_collect' ? 'selected' : '' }}>Freight Collect</option>
                                                    </select>
                                                </div>
                                            </div>
                                        
                                            <button type="button" class="btn btn-primary">Create Shipment</button>
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
<script>
document.addEventListener("DOMContentLoaded", function () {
    const shippingTypeRadios = document.querySelectorAll('input[name="shipping_type"]');
    const shippingInfo = document.getElementById('shipping_info');

    shippingTypeRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            if (this.checked) {
                shippingInfo.style.display = "flex";
            }
        });
    });
});
</script>
@endpush




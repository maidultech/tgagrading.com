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
                                        <a href="{{ route('admin.outgoing.order') }}" class="btn btn-sm btn-primary btn-gradient">{{__('Back')}}</a>
                                    </div>
                                </div>
                            </div>
                            @php
                                $shippingRoute = route("admin.order.create.".($order->shipping_method == 'ups' ? 'ups' : 'canadaPost').".label", $order->id);
                            @endphp
                            {{-- @if ($order->admin_tracking_id)
                                <div class="row justify-content-center mt-2">
                                    <div class="col-11">
                                        <div class="alert alert-info">
                                            <strong>Info!</strong> Shipping label already generated for this order.
                                        </div>
                                    </div>
                                </div>
                                @php
                                    $shippingRoute = route("admin.order.address.update", $order->id);
                                @endphp
                            @endif --}}
                            @php($shippingRoute = route("admin.order.address.update", $order->id))
                            <form action="{{ $shippingRoute }}" method="POST">
                                @csrf
                                <div class="card-body">
                                    <!-- Recipient Information -->
                                    <div class="mb-4">
                                        <h5 class="text-secondary">RECIPIENT INFORMATION</h5>
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="name" class="form-label">Name:</label>
                                                <input type="text" class="form-control" id="name" value="{{  $order->transaction?->shipping_data['shippingName'] }}"
                                                    name="name" placeholder="Enter name" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="email" class="form-label">User E-mail Address:</label>
                                                <input type="email" class="form-control" id="email" value="{{  $order->rUser?->email }}"
                                                    name="email" placeholder="Enter email" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="address1" class="form-label">Address <span class="text-secondary">(Line1)</span>:</label>
                                                <input type="text" class="form-control" id="address1" value="{{  $order->transaction?->shipping_data['shippingAddress'] }}"
                                                    name="address1" placeholder="Enter address" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="address2" class="form-label">Address <span class="text-secondary">(Line2)</span>:</label>
                                                <input type="text" class="form-control" id="address2" value=""
                                                    name="address2" placeholder="Enter address">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="city" class="form-label">City:</label>
                                                <input type="text" class="form-control" id="city" value="{{  $order->transaction?->shipping_data['shippingCity'] }}"
                                                    name="city" placeholder="Enter city" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="state" class="form-label">State/Province:</label>
                                                <input type="text" class="form-control" id="state" value="{{  $order->transaction?->shipping_data['shippingState'] }}"
                                                    name="state" placeholder="Enter state/province" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="country" class="form-label">Country:</label>
                                                <input type="text" class="form-control" id="country" value="{{  $order->transaction?->shipping_data['shippingCountry'] }}"
                                                    name="country" placeholder="Enter country" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="zip" class="form-label">Postal/Zip Code:</label>
                                                <input type="text" class="form-control" id="zip" value="{{  $order->transaction?->shipping_data['shippingZip'] }}"
                                                    name="zip" placeholder="Enter postal/zip code" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="phone" class="form-label">Contact Phone:</label>
                                                <input type="text" class="form-control" id="phone"  value="@if(!empty($order->transaction?->shipping_data['dial_code'])) {{ '+'.$order->transaction?->shipping_data['dial_code'].' '.$order->transaction?->shipping_data['shippingPhone'] }} @else {{ $order->transaction?->shipping_data['shippingPhone'] }} @endif"
                                                    name="phone" placeholder="Enter contact phone" required>
                                            </div>
                                        </div>
                                    </div>
                            
                                    <!-- Shipment Information -->
                                    <div>
                                        <h5 class="text-secondary">SHIPMENT INFORMATION</h5>
                                        <div class="row g-3">
                                            @if($order->shipping_method != 'local_pickup')
                                                <div class="col-md-3">
                                                    <label for="user_id" class="form-label">Customer Account Number:</label>
                                                    <input type="text" class="form-control" id="user_id" readonly value="{{ $order->rUser->user_code }}" name="user_id" placeholder="Enter suite ID">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="suiteId" class="form-label">Shipping Method:</label>
                                                    <?php
                                                        $currentMethod = $order->shipping_method;
                                                        $currentCode = collect($order->shipping_method_service_code)->keys()->first();
                                                        $currentJson = json_encode($order->shipping_method_service_code);
                                                    ?>
                                                    <select class="form-control" name="suite_id" id="suiteId">
                                                        <option 
                                                            value='canada_post|{"DOM.EP":"Expedited Parcel"}' 
                                                            {{ $currentMethod === 'canada_post' && $currentJson === '{"DOM.EP":"Expedited Parcel"}' ? 'selected' : '' }}>
                                                            {{ str('canada_post')->headline() }} (Expedited Parcel)
                                                        </option>
                                                        <option 
                                                            value='ups|{"11":"UPS Standard"}' 
                                                            {{ $currentMethod === 'ups' && ($currentJson === '{"11":"UPS Standard"}' || $currentJson === '{"11":"UPS Ground"}') ? 'selected' : '' }}>
                                                            {{ str('ups')->headline() }} (UPS Standard)
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="weight" class="form-label">Chargeable Weight:</label>
                                                    <input type="text" class="form-control" id="weight" name="weight" value="{{ $order->cards->count()*0.08 }}" placeholder="Enter weight">
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="weight" class="form-label">Tracking Number:</label>
                                                    <input type="text" class="form-control" id="trackingID" name="trackingID" value="{{ $order->admin_tracking_id }}" placeholder="Tracking Number">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="domesticFee" class="form-label">Shipping Fee:</label>
                                                    <input type="text" class="form-control" id="domesticFee" value="{{ number_format($order->shipping_charge,2) }}" name="domestic_fee" placeholder="Enter fee">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="insuranceFee" class="form-label @if($order->insurance_amount > 0) text-danger @endif">Insurance Fee:</label>
                                                    <input type="text" class="form-control" id="insuranceFee" @if($order->insurance_amount > 0) style="border: 1px solid #ff0000;" @endif
                                                        value="{{ number_format($order->insurance_amount,2) }}" name="insurance_fee" placeholder="Enter fee">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="subtotal" class="form-label">Subtotal:</label>
                                                    <input type="text" class="form-control" value="{{ number_format($order->cards->count() * $order->net_unit_price,2) }}" id="subtotal" name="subtotal" placeholder="Enter subtotal">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="grandTotal" class="form-label">Grand Total:</label>
                                                    <input type="text" class="form-control" value="{{ number_format($trnx->amount,2) }}" id="grandTotal" name="grand_total" placeholder="Enter grand total">
                                                </div>
                                            @else 
                                                <div class="col-md-6">
                                                    <label for="user_id" class="form-label">Customer Account Number:</label>
                                                    <input type="text" class="form-control" id="user_id" readonly value="{{ $order->rUser->user_code }}" name="user_id" placeholder="Enter suite ID">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="suiteId" class="form-label">Shipping Method:</label>
                                                    <select class="form-control" name="suite_id" id="suiteId">
                                                        <option 
                                                            value='local_pickup|{"local.pickup":"Local Pickup"}' selected>
                                                            Local Pickup
                                                        </option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="weight" class="form-label">Chargeable Weight:</label>
                                                    <input type="text" class="form-control" id="weight" name="weight" value="{{ $order->cards->count()*0.08 }}" placeholder="Enter weight">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="insuranceFee" class="form-label @if($order->insurance_amount > 0) text-danger @endif">Insurance Fee:</label>
                                                    <input type="text" class="form-control" id="insuranceFee" @if($order->insurance_amount > 0) style="border: 1px solid #ff0000;" @endif
                                                        value="{{ number_format($order->insurance_amount,2) }}" name="insurance_fee" placeholder="Enter fee">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="subtotal" class="form-label">Subtotal:</label>
                                                    <input type="text" class="form-control" value="{{ number_format($order->cards->count() * $order->net_unit_price,2) }}" id="subtotal" name="subtotal" placeholder="Enter subtotal">
                                                </div>
                                                <div class="col-md-6">
                                                    <label for="grandTotal" class="form-label">Grand Total:</label>
                                                    <input type="text" class="form-control" value="{{ number_format($trnx->amount,2) }}" id="grandTotal" name="grand_total" placeholder="Enter grand total">
                                                </div>
                                                <div class="col-md-12">
                                                    <label for="grandTotal" class="form-label">Local Pickup Details:</label>
                                                    <textarea name="shipping_notes" id="shipping_notes" class="form-control" placeholder="Enter local pickup details" rows="4">{!! nl2br($order->shipping_notes) !!}</textarea>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-end">
                                    {{-- @unless ($order->admin_tracking_id)
                                        <button type="submit" class="btn btn-primary">Generate Shipping Label</button>
                                        @else
                                        <button type="submit" class="btn btn-primary">Update Shipping Info</button>
                                        @endunless --}}
                                        <button type="submit" class="btn btn-primary">Update Shipping Info</button>
                                    {{-- <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#assignITModal">Assign IT Number</button> --}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="modal fade" id="assignITModal" tabindex="-1" role="dialog" aria-labelledby="assignITModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assignITModalLabel">Assign IT Number</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="form-group">
                            <label for="itNumber">IT Number:</label>
                            <input type="text" class="form-control" id="itNumber" placeholder="Enter International Tracking Number">
                        </div>
                        <div class="form-group">
                            <label for="datePacked">Date Packed:</label>
                            <input type="date" class="form-control" id="datePacked">
                        </div>
                        <div class="form-group">
                            <label for="timePacked">Time Packed:</label>
                            <input type="time" class="form-control" id="timePacked">
                        </div>
                        <div class="form-group">
                            <label for="packedBy">Packed By:</label>
                            <input type="text" class="form-control" id="packedBy" placeholder="Enter Packed By">
                        </div>
                        <div class="form-group">
                            <label for="shipBy">Ship By:</label>
                            <select class="form-control" id="shipBy">
                                <option value="">-- Please Select --</option>
                                <option value="1">Option 1</option>
                                <option value="2">Option 2</option>
                                <option value="3">Option 3</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Assign ITN</button>
                </div>
            </div>
        </div>
    </div> --}}
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




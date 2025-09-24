<div class="container bg-light p-5 border border-gray-200 rounded mt-5">
    <div class="row">
        <div class="col-12 text-center h4 mb-4">Shipping and Other Details</div>
        {{-- <div class="col-12 my-3">
            <div class="checkout_wrapper">
                <div class="heading mb-4">
                    <h4>Shipping Method</h4>
                </div>
                <div class="item_list row">
                    @foreach ($brands as $key => $item)
                        <div class="form-check mb-2 p-0 col-md-5 mr-2">
                            <input class="form-check-input d-none"
                                value="{{ $item->id }}"
                                type="radio" name="submission_type"
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
        </div> --}}
        <div class="col-md-6">
            <div class="w-100 font-weight-bold">Admin Shipping Information</div>
            <div class="form-group w-100">
                <label for="">Tracking Number</label>
                <input type="text"
                    value="{{ $order->admin_tracking_id }}" class="form-control"
                    placeholder="Enter shipping tracking number" name="admin_tracking_id">
            </div>
            <div class="form-group w-100">
                <label for="">Tracking Note</label>
                <textarea class="form-control"
                    placeholder="Enter shipping tracking number" name="admin_tracking_note">{{ $order->admin_tracking_note }}</textarea>
            </div>
            <div class="form-group w-100">
                <label for="">Courier Company</label>
                {{-- <textarea class="form-control" name="admin_tracking_note"
                    placeholder="Write a note to send the customer, like tracking number or other information">{{ $order->admin_tracking_note }}</textarea> --}}
                <select class="form-control form-select" name="shipping_method">
                    <option value="canada_post"  @selected($order->shipping_method=='canada_post')>Canada Post</option>
                    <option value="ups" @selected($order->shipping_method=='ups')>UPS</option>
                    <option value="other" @selected(!in_array($order->shipping_method, [
                        'canada_post','ups'
                    ]))>Other</option>
                </select>
            </div>
        </div>
        <div class="col-md-6">
            <div class="w-100 font-weight-bold">Customer Shipping Information</div>
            <div class="form-group w-100">
                <label for="">Tracking Number</label>
                <input @readonly(true) type="text"
                    value="{{ $order->customer_tracking_url }}" class="form-control"
                    placeholder="Enter shipping tracking number" name="customer_tracking_url">
            </div>
            <div class="form-group w-100">
                <label for="">Courier Company</label>
                {{-- <textarea @readonly(true) class="form-control" name="customer_tracking_note"
                    placeholder="Write a note to send {{ $setting->site_name }}, like tracking number or other information">{{ $order->customer_tracking_note }}</textarea> --}}
                <select class="form-control form-select" name="customer_tracking_note" @readonly(true)>
                    <option value="Canada Post" {{ $order->customer_tracking_note == 'Canada Post' ? 'selected' : '' }}>Canada Post</option>
                    <option value="UPS" {{ $order->customer_tracking_note == 'UPS' ? 'selected' : '' }}>UPS</option>
                    <option value="Other" {{ $order->customer_tracking_note != 'Canada Post' && $order->customer_tracking_note != 'UPS' ? 'selected' : '' }}>Other</option>
                </select>
            </div>
        </div>
        
        <div class="mb-3 col-md-3 d-none">
            <label for="shippingName" class="form-label">Full Name</label>
            <input name="shippingName"
                value="{{ $order->transaction->shipping_data['shippingName'] ?? '' }}"
                type="text" class="form-control" id="shippingName"
                placeholder="Enter your full name" required>
        </div>
        <div class="mb-3 col-md-6 d-none">
            <label for="shippingAddress" class="form-label">Address</label>
            <input name="shippingAddress"
                value="{{ $order->transaction->shipping_data['shippingAddress'] ?? '' }}"
                type="text" class="form-control" id="shippingAddress"
                placeholder="Enter your address" required>
        </div>
        <div class="mb-3 col-md-3 d-none">
            <label for="shippingCity" class="form-label">City</label>
            <input name="shippingCity" value="{{ $order->transaction->shipping_data['shippingCity'] ?? '' }}" type="text"
                class="form-control" id="shippingCity" placeholder="Enter your city" required>
        </div>
        <div class="mb-3 col-md-3 d-none">
            <label for="shippingState" class="form-label">State/Province</label>
            <input name="shippingState" value="{{ $order->transaction->shipping_data['shippingState'] ?? '' }}" type="text"
                class="form-control" id="shippingState" placeholder="Enter your state" required>
        </div>
        <div class="mb-3 col-md-3 d-none">
            <label for="shippingZip" class="form-label">Postal Code</label>
            <input name="shippingZip" value="{{ $order->transaction->shipping_data['shippingZip'] ?? '' }}" type="text"
                class="form-control" id="shippingZip" placeholder="Enter your zip code" required>
        </div>
        <div class="mb-3 col-md-3 d-none">
            <label for="shippingCountry" class="form-label">Country</label>
            <input name="shippingCountry" value="{{ $order->transaction->shipping_data['shippingCountry'] ?? '' }}"
                type="text" class="form-control" id="shippingCountry"
                placeholder="Enter your country" required>
        </div>
        <div class="mb-3 col-md-3 d-none">
            <label for="phone" class="form-label">Phone Number</label> <br>
            <input name="shippingPhone"
                value="{{ $order->transaction->shipping_data['shippingPhone'] ?? '' }}"
                type="text" class="form-control w-100" id="shippingPhone"
                placeholder="Enter your phone number" required>
        </div>
        <div class="col-md-6 align-items-center row">
            <div class="form-group col-auto">
                <label for="">Order Status</label>
                <select name="order_status" class="form-control form-select">
                    @foreach (config('static_array.status') as $key => $status)
                        @if ($order->status == 35 && $order->payment_status == 1)
                            @if (in_array($key, [35, 40]))
                                <option @selected($key == $order->status) value="{{ $key }}">{{ $status }}</option>
                            @endif
                        @elseif ($order->status == 40 && $order->payment_status == 1)
                            @if ($key == 40)
                                <option @selected($key == $order->status) value="{{ $key }}">{{ $status }}</option>
                            @endif
                        @elseif ($order->status == 40)
                            @if (in_array($key, [35, 40]))
                                <option @selected($key == $order->status) value="{{ $key }}">{{ $status }}</option>
                            @endif
                        @elseif ($key != 50)
                            <option @selected($key == $order->status) value="{{ $key }}">{{ $status }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="form-group col-auto">
                <label for="">Payment Status</label>
                <select name="payment_status" class="form-control form-select">
                    {{-- @if($order->payment_status != 1) --}}
                    <option @selected($order->payment_status == 0) value="0">Due</option>
                    {{-- @endif --}}
                    <option @selected($order->payment_status == 1) value="1">Paid</option>
                </select>
            </div>
            @if($order->is_redeemed == 1)
            <div class="form-group mb-0 col-auto">
                <strong class="text-danger">
                    NB. User redeemed their available free card for this order
                </strong>
            </div>
            @endif
        </div>
    </div>
</div>
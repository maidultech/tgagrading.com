@php
    $total_amount = 0;
@endphp
<div class="row d-flex justify-content-center mt-5 {{ request()->routeIs('admin.*') ? 'm-0' : '' }}">
    <div class="col-xl-10">
        @if (session()->has('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Congratulations! Order Paid!</strong>
                @if ($trnx->order->shipping_method == 'local_pickup')
                    Thank you for your payment! Please contact us at <a href="mailto:{{ $setting->support_email }}">{{ $setting->support_email }}</a>
                    or call our office at <a href="tel:{{ $setting->phone_no }}">{{ $setting->phone_no }}</a> to arrange a pickup for your order.
                @else
                    Thank you for your payment! We will be updating your account with the tracking number in the next 1-2 business days.
                    Watch your inbox for an email.
                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="invoice_details border border-light border-light-subtle rounded-4">
            <div class="card-body p-4">
                <div class="row">
                    <div class="col-sm-6">
                        <p class="h3">
                            @php($logo = public_path($setting->site_logo))
                            <img src="data:{{ mime_content_type($logo) }};base64,{{ base64_encode(file_get_contents($logo)) }}"
                                alt="Site Logo" style="width: 150px;">
                        </p>
                        <address>
                            {!! nl2br($setting->office_address) !!}
                        </address>
                    </div>
                    <?php
                        $createdAt = strtotime($trnx->order?->created_at);
                        $payDate = strtotime($trnx->order?->pay_date ?? $trnx->pay_date);
                        $showDate = $createdAt > $payDate ? $payDate : $createdAt;
                    ?>
                    <div class="col-sm-6 {{ request()->routeIs('admin.*') ? 'text-sm-right' : 'text-sm-end' }}">
                        <span class="text-dark">
                            <strong>Date Created:</strong> {{ date('d M, Y  h:i A', $showDate) }}
                        </span>
                        <br>
                        <br>
                        <p class="h3">{{ $trnx->shipping_data['shippingName'] }}</p>
                        <address>
                            {{ $trnx->shipping_data['shippingAddress'] }},
                            {{ $trnx->shipping_data['shippingCity'] }}
                            <br>
                            {{ $trnx->shipping_data['shippingState'] }} -
                            {{ $trnx->shipping_data['shippingZip'] }},
                            {{ $trnx->shipping_data['shippingCountry'] }} <br>
                            @if (!empty($trnx->shipping_data['dial_code']))
                                {{ '+' . $trnx->shipping_data['dial_code'] . ' ' . $trnx->shipping_data['shippingPhone'] }}
                            @else
                                {{ $trnx->shipping_data['shippingPhone'] }}
                            @endif
                        </address>
                        <div>                        
                            @if($trnx->order->payment_status == 1)
                                {{ date('d M, Y  h:i A', $payDate) }}
                            @endif
                        </div>
                        <div><strong>Payment Status:</strong> {{ $trnx->order->payment_status ? 'Paid' : 'Due' }}</div>
                    </div>
                    <div class="col-12 my-2">
                        <h5>Invoice #{{ $trnx->order->order_number }}</h5>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table m-0 min_width ">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 1%"></th>
                                {{-- <th class="text-left">Service</th> --}}
                                <th class="text-left">Card</th>
                                <th class="text-left"></th>
                                <th class="text-center" style="width: 1%">Qty</th>
                                <th class="text-end" style="width: 1%">Price</th>
                                <th class="text-end" style="width: 1%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trnx->order->details as $row)
                                {{-- @if ($row->gradedCards->count() > 0) --}}
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $row->year . ' ' . $row->brand_name . ' ' . $row->card . ' ' . $row->card_name }}
                                        </td>
                                        <td></td>
                                        {{-- <td>{{ $row->gradedCards->count() > 0 ? $row->gradedCards->count() : '' }}</td> --}}
                                        <td>{{ $row->cards->count() > 0 ? $row->cards->count() : '' }}</td>
                                        <td>
                                            @if ($trnx->order->net_unit_price > 0)
                                                {{ getDefaultCurrencySymbol() }}{{ $trnx->order->net_unit_price }}
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            {{-- @if ($trnx->order->net_unit_price * $row->gradedCards->count() > 0)
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->net_unit_price * $row->gradedCards->count(), 2) }}
                                            @endif --}}
                                            {{-- @if ($trnx->order->net_unit_price * $row->cards->count() > 0)
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->net_unit_price * $row->cards->count(), 2) }}
                                            @endif --}}

                                            {{-- @if($trnx->order?->rUser?->is_subscriber && $trnx->order?->rUser?->subscription_start < now()
                                                && $trnx->order?->rUser?->subscription_end > now() && $trnx->order?->is_redeemed == 1 && $row->line_price == 0) --}}
                                            @if($trnx->order?->is_redeemed == 1 && $row->line_price == 0)
                                                Free
                                            @elseif($row->line_price)
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($row->line_price,2) }}
                                            @else
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->net_unit_price,2) }}
                                            @endif
                                        </td>
                                    </tr>
                                {{-- @endif --}}
                                <?php
                                // $total_amount = $trnx->order->unit_price * $row->gradedCards->count();
                                // $total_amount += $trnx->order->unit_price * $row->cards->count();
                                    $order = $trnx->order;
                                    if ($order->is_redeemed != 1) {
                                        $details = $order->details;

                                        if ($details->where('line_price', 0.00)->isNotEmpty()) {
                                            $total_amount = $details->sum(function ($detail) use ($order) {
                                                return $detail->line_price > 0 ? $detail->line_price : $order->net_unit_price;
                                            });
                                        } else {
                                            $total_amount = $details->sum('line_price');
                                        }
                                    } else {
                                        $total_amount = $order->details->sum('line_price');
                                    }
                                ?>
                            @endforeach
                            <tr>
                                <td colspan="5" class="strong text-end">Subtotal   &nbsp; &nbsp; &nbsp; <strong class="fw-bold"> [{{ $trnx->order->details->sum('qty') }} cards]</td>
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() }}{{ number_format($total_amount, 2) }}</td>
                            </tr>
                            {{-- @if($order->payment_status == 1) --}}
                                @foreach($order->additionalCosts as $cost)
                                    <tr>
                                        <td colspan="5" class="strong text-end">{{ $cost->details }}</td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}{{number_format($cost->price,2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            {{-- @endif --}}
                            <tr>
                                <td colspan="5" class="strong text-end">Shipping Charge
                                    ({{ str($order->shipping_method)->headline()->upper() }})</td>
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() }}{{ number_format($order->shipping_charge, 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">GST ({{ $order->gst_tax }}%)</td>
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() }}
                                    {{ number_format(max(0, ($order->gst_tax * ((($total_amount + $order->additionalCosts->sum('price')) - $trnx->order->coupon_discount - $trnx->order->used_wallet_amount) + $order->shipping_charge)) / 100), 2) }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">PST ({{ $order->pst_tax }}%)</td>
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() }}
                                    {{ number_format(max(0, ($order->pst_tax * (($total_amount + $order->additionalCosts->sum('price')) - $trnx->order->coupon_discount - $trnx->order->used_wallet_amount)) / 100), 2) }}
                                </td>
                            </tr>
                            @if ($trnx->order->has_insurance)
                                <tr>
                                    <td colspan="5" class="strong text-end">Insurance
                                        ({{ getDefaultCurrencySymbol() . $trnx->order->insurance_value }})</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(max(0,$trnx->order->insurance_amount), 2) }}
                                    </td>
                                </tr>
                            @endif
                            @if ($trnx->order->used_wallet > 0)
                                <tr>
                                    <td colspan="5" class="strong text-end">Wallet Amount Used </td>
                                    <td class="text-end">
                                        -{{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->used_wallet_amount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            @if ($trnx->order->coupon_discount > 0)
                                <tr>
                                    <td colspan="5" class="strong text-end">Discount
                                        {{ $trnx->order?->coupon?->discount_code ? '('.$trnx->order?->coupon?->discount_code.')' : '' }}</td>
                                    <td class="text-end">
                                        -{{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->coupon_discount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td colspan="5" class="font-weight-bold text-uppercase text-end">
                                    <strong>Total {{ $trnx->order->payment_status == 0 ? 'Due' : '' }}</strong>
                                </td>
                                <td class="font-weight-bold text-end">
                                    <strong>{{ getDefaultCurrencySymbol() }}{{ number_format(max(0,$trnx->amount), 2) }}</strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <h6 class="text-secondary text-center mt-5 text-sm text-dark">{{ $setting->invoice_footer }} </h6>
            </div>
        </div>
    </div>
</div>

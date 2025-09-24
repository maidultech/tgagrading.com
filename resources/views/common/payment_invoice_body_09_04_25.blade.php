@php
    $total_amount = 0;
@endphp
<div class="row d-flex justify-content-center mt-5 {{ request()->routeIs('admin.*') ? 'm-0' : '' }}">
    <div class="col-xl-10">
        @if (session()->has('msg'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Congratulations! Order Paid!</strong> Thank you for your payment! We will be updating your
                account with the tracking number in the next 1-2 business days. Watch your inbox for an email.
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
                                alt="" style="width: 150px;">
                        </p>
                        <address>
                            {!! nl2br($setting->office_address) !!}
                        </address>
                    </div>

                    <div class="col-sm-6 {{ request()->routeIs('admin.*') ? 'text-sm-right' : 'text-sm-end' }}">
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
                        <div>{{ date('d M, Y  h:i A', strtotime($order->created_at)) }}</div>
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
                                            @if ($trnx->order->net_unit_price * $row->cards->count() > 0)
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->net_unit_price * $row->cards->count(), 2) }}
                                            @endif
                                        </td>
                                    </tr>
                                {{-- @endif --}}
                                <?php
                                // $total_amount = $trnx->order->unit_price * $row->gradedCards->count();
                                $total_amount += $trnx->order->unit_price * $row->cards->count();
                                ?>
                            @endforeach
                            <tr>
                                <td colspan="5" class="strong text-end">Subtotal</td>
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() }}{{ number_format($total_amount, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">GST ({{ $setting->gst_tax }}%)</td>
                                @if ($trnx->order->used_wallet > 0)
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->gst_tax * ($total_amount + $order->shipping_charge - $trnx->order->used_wallet_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                                    </td>
                                @else
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->gst_tax * ($total_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">PST ({{ $setting->pst_tax }}%)</td>
                                @if ($trnx->order->used_wallet > 0)
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->pst_tax * ($total_amount + $order->shipping_charge - $trnx->order->used_wallet_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                                    </td>
                                @else
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->pst_tax * ($total_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                                    </td>
                                @endif
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">Shipping Charge
                                    ({{ str($order->shipping_method)->headline()->upper() }})</td>
                                <td class="text-end">
                                    {{ getDefaultCurrencySymbol() }}{{ number_format($order->shipping_charge, 2) }}
                                </td>
                            </tr>
                            @if ($trnx->order->has_insurance)
                                <tr>
                                    <td colspan="5" class="strong text-end">Insurance
                                        ({{ getDefaultCurrencySymbol() . $trnx->order->insurance_value }})</td>
                                    <td class="text-end">
                                        {{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order->insurance_amount, 2) }}
                                    </td>
                                </tr>
                            @endif
                            @if ($trnx->order->used_wallet > 0)
                                <tr>
                                    <td colspan="5" class="strong text-end">Wallet Balance </td>
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
                                    <strong>{{ getDefaultCurrencySymbol() }}{{ number_format($trnx->amount, 2) }}</strong>
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

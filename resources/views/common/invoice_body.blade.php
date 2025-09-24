@php
    $total_amount = 0;
@endphp
<div class="row d-flex justify-content-center mt-5 {{ request()->routeIs('admin.*') ? 'm-0' : '' }}">
    <div class="col-xl-10">
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
                        $showDate = ($createdAt > $payDate && $trnx->order->payment_status == 1) ? $payDate : $createdAt;
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
                            @if(!empty($trnx->shipping_data['dial_code']))
                                {{ '+'.$trnx->shipping_data['dial_code'].' '.$trnx->shipping_data['shippingPhone'] }}
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
                                <th class="text-center" style="width: 1%">#</th>
                                <th class="text-left">Card</th>
                                <th class="text-left"></th>
                                <th class="text-center" style="width: 1%">Qty</th>
                                <th class="text-end" style="width: 1%">Price</th>
                                <th class="text-end" style="width: 1%">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trnx->order->details as $row)
                            <tr>
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="">{{ $row->year .' '. $row->brand_name .' '. $row->card .' '. $row->card_name }}</td>
                                <td class=""></td>
                                <td class="text-center">{{ $row->qty > 0 ? $row->qty : '' }}</td>
                                <td class="text-end">
                                    @if($trnx->order->net_unit_price > 0)
                                        {{ getDefaultCurrencySymbol() }}{{ $trnx->order->net_unit_price }}
                                    @endif
                                </td>
                                <td class="text-end">
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
                            @endforeach
                            @if($trnx->order?->rUser?->is_subscriber && $trnx->order?->rUser?->subscription_start < now()
                                && $trnx->order?->rUser?->subscription_end > now() && $trnx->order?->rPlan?->type == 'subscription')
                            <tr>
                                <td colspan="5" class="font-weight-bold text-uppercase text-end">
                                    <strong>Total</strong>
                                </td>
                                <td class="font-weight-bold text-end"><strong>Free</strong></td>
                            </tr>
                            @else
                            <?php
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
                                
                                $gtotalAmount = $total_amount;
                                if ($trnx->order->used_wallet > 0) {
                                    $gtotalAmount -= $trnx->order->used_wallet_amount;
                                }
                            ?>
                            {{-- @if (!request()->routeIs('admin.*')) --}}
                                <tr>
                                    <td colspan="5" class="strong text-end">Subtotal   &nbsp; &nbsp; &nbsp; <strong class="fw-bold"> [{{ $trnx->order->details->sum('qty') }} cards]</strong>  </td>
                                    <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format($total_amount,2) }}</td>
                                </tr>
                            {{-- @endif --}}
                            {{-- @if($trnx->order->payment_status == 1) --}}
                                @foreach($trnx->order->additionalCosts as $cost)
                                    <?php $gtotalAmount += $cost->price; ?>
                                    <tr>
                                        <td colspan="5" class="strong text-end">{{ $cost->details }}</td>
                                        <td class="text-end">
                                            {{ getDefaultCurrencySymbol() }}{{number_format($cost->price,2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            {{-- @endif --}}
                            <tr>
                                <td colspan="5" class="strong text-end">Shipping Charge ({{ str($order->shipping_method)->headline()->upper() }})</td>
                                <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($order->shipping_charge),2) }}</td>
                                <?php
                                    $gtotalAmount += $order->shipping_charge;
                                ?>
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">GST ({{ $order->gst_tax }}%)</td>
                                <td class="text-end">{{ getDefaultCurrencySymbol() }}
                                    {{ number_format(max(0,($order->gst_tax*((($total_amount + $trnx->order->additionalCosts->sum('price')) - $trnx->order->coupon_discount - $trnx->order->used_wallet_amount) + $order->shipping_charge)/100)),2) }}</td>
                                <?php
                                    $gtotalAmount += max(0,($order->gst_tax*((($total_amount + $trnx->order->additionalCosts->sum('price')) - $trnx->order->coupon_discount - $trnx->order->used_wallet_amount) + $order->shipping_charge)/100));
                                ?>
                            </tr>
                            <tr>
                                <td colspan="5" class="strong text-end">PST ({{ $order->pst_tax }}%)</td>
                                {{-- @dd($total_amount, $trnx->order->coupon_discount, ($total_amount-$trnx->order->coupon_discount)) --}}
                                <td class="text-end">{{ getDefaultCurrencySymbol() }}
                                    {{ number_format(max(0,($order->pst_tax*(($total_amount + $trnx->order->additionalCosts->sum('price'))-$trnx->order->coupon_discount - $trnx->order->used_wallet_amount)/100)),2) }}</td>
                                <?php
                                    $gtotalAmount += max(0,($order->pst_tax*(($total_amount + $trnx->order->additionalCosts->sum('price'))-$trnx->order->coupon_discount - $trnx->order->used_wallet_amount)/100));
                                ?>
                            </tr>
                            @if ($trnx->order->has_insurance)
                            <td colspan="5" class="strong text-end">Insurance ({{ getDefaultCurrencySymbol().$trnx->order->insurance_value }})</td>
                            <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(max(0,($trnx->order->insurance_amount)),2) }}</td>
                            <?php
                                $gtotalAmount += $trnx->order->insurance_amount;
                            ?>
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
                                <td colspan="5" class="strong text-end">Discount {{ $trnx->order?->coupon?->discount_code ? '('.$trnx->order?->coupon?->discount_code.')' : '' }}</td>
                                <td class="text-end">
                                    -{{ getDefaultCurrencySymbol() }}{{ number_format(($trnx->order->coupon_discount),2) }}
                                </td>
                                <?php
                                    $gtotalAmount -= $trnx->order->coupon_discount;
                                ?>
                            @endif
                            <tr>
                                <td colspan="5" class="font-weight-bold text-uppercase text-end">
                                    <strong>Total  {{ $trnx->order->payment_status==0 ? 'Due' : '' }}</strong>
                                </td>
                                <td class="font-weight-bold text-end">
                                    <strong>{{ getDefaultCurrencySymbol() }}{{ number_format(max(0,$gtotalAmount),2) }}</strong>
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                @if (Route::currentRouteName() != 'admin.order.view')
                    <div class="progress-barr-wrapper mt-5">
                        <div class="status-bar" style="width: 80%;">
                            <div class="current-status"
                                style="width: {{ $trnx->order?->status ? (($trnx->order->status - 15) / 5 + 1) * 16.5 : 0 }}%;">
                            </div>
                        </div>
                        <ul class="progress-barr">
                            @foreach (config('static_array.status') as $key => $status)
                                @if ($key >= 10 && $key <= 40)
                                    <li class="section
                                        @if($key == $trnx->order?->status) visited current  @elseif($trnx->order?->status > $key) visited @endif">
                                        {{ $status }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @endif
                <h6 class="text-secondary text-center mt-5 text-sm text-dark">{{ $setting->invoice_footer }} </h6>
            </div>
        </div>
    </div>
</div>

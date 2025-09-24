<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

    <style>
        body {
            font-family: Arial, 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            width: 95%;
            margin: 0 auto;
            padding: 20px;
        }

        .header,
        .footer {
            margin-bottom: 50px;
        }

        .header h1 {
            margin: 0;
            font-size: 2em;
        }

        .header img {
            max-width: 150px;
        }

        .address,
        .invoice-details {
            display: inline-block;
            vertical-align: top;
            width: 48%; /* Slightly less than 50% */
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 50px; /* Adjusted to provide space */
            font-size: 12px;
        }

        table th,
        table td {
            padding: 10px;
            text-align: left;
        }

        table th {
            background-color: #d2cbc36e;
            color: #0d0b0b;
            text-transform: uppercase;
        }

        .totals {
            width: 270px;
            float: right;
            text-align: right;
            padding-left: 20px;
            padding-right: 40px;
        }

        .footer {
            margin-top: 150px;
            font-weight: 600;
            font-size: 25px;
        }

        .company {
            padding-bottom: 5px;
            padding-left: 1px;
            padding-right: 10px;
            font-size: 14px;
        }
    </style>
</head>
@php
    $total_amount = 0;
@endphp
<body>

    <div class="container">
        <div class="header">
            @php($logo = public_path($setting->site_logo))
            <img style="width: 150px; image-rendering: pixelated; float: left;"
                src="data:{{ mime_content_type($logo) }};base64,{{ base64_encode(file_get_contents($logo)) }}"
                class="img-fluid" alt="Logo">
        </div>
        <br>
        <div class="clearfix">
            <div class="address">
                <address>
                    {!! nl2br($setting->office_address) !!}
                </address>

                <strong>Email:</strong> {{ $setting->support_email }}<br>
                <strong>Phone:</strong> {{ $setting->phone_no }}
            </div>

            <div class="invoice-details">
                <div>
                    <div><strong>To</strong></div>
                    {{ $trnx->shipping_data['shippingName'] }}<br>
                    <address>
                        {{ $trnx->shipping_data['shippingAddress'] }},
                        {{ $trnx->shipping_data['shippingCity'] }}<br>
                        {{ $trnx->shipping_data['shippingState'] }} -
                        {{ $trnx->shipping_data['shippingZip'] }},
                        {{ $trnx->shipping_data['shippingCountry'] }} <br>
                        @if(!empty($trnx->shipping_data['dial_code']))
                                {{ '+'.$trnx->shipping_data['dial_code'].' '.$trnx->shipping_data['shippingPhone'] }}
                            @else
                                {{ $trnx->shipping_data['shippingPhone'] }}
                            @endif
                    </address>
                </div>
                <p><strong>Date:</strong> {{ date('d.m.Y', strtotime($trnx->pay_date)) }}</p>
                <p><strong>Payment Status:</strong> {{ $trnx->order->payment_status ? 'Paid' : 'Due' }}</p>
            </div>
        </div>

        <table class="table m-0 min_width ">
            <thead>
                @if($trnx->order?->rPlan?->type == 'subscription' && $trnx->order?->total_order_qty == 0)
                <tr>
                    <th class="text-center" style="width: 1%"></th>
                    <th class="text-left">Plan</th>
                    <th class="text-left"></th>
                    <th class="text-center" style="width: 1%"></th>
                    <th class="text-end" style="width: 1%"></th>
                    <th class="text-end" style="width: 1%">Amount</th>
                </tr>
                @else
                <tr>
                    <th class="text-center" style="width: 1%">#</th>
                    <th class="text-left">Card</th>
                    <th class="text-left"></th>
                    <th class="text-center" style="width: 1%">Qty</th>
                    <th class="text-end" style="width: 1%">Price</th>
                    <th class="text-end" style="width: 1%">Amount</th>
                </tr>
                @endif
            </thead>
            <tbody>
                @if($trnx->order?->rPlan?->type == 'subscription' && $trnx->order?->total_order_qty == 0)
                    <tr>
                        <td></td>
                        <td colspan="4" class="fw-bold">Plan - {{ $trnx->order?->plan_name ?? $trnx->order?->rPlan?->name }}</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format($trnx->order?->rPlan?->price,2) }}</td>
                    </tr>
                    <?php $total_amount = $trnx->order?->rPlan?->price; ?>
                    <tr>
                        <td colspan="5" class="strong text-end">Subtotal</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format($total_amount,2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="strong text-end">GST ({{ $setting->gst_tax }}%)</td>
                        @if ($trnx->order->used_wallet > 0)
                            <td class="text-end">
                                {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->gst_tax * ($total_amount - $trnx->order->used_wallet_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                            </td>
                        @else
                            <td class="text-end">
                                {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->gst_tax * ($total_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                            </td>
                        @endif
                        {{-- <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($setting->gst_tax*($total_amount-$trnx->order->coupon_discount)/100),2) }}</td> --}}
                    </tr>
                    <tr>
                        <td colspan="5" class="strong text-end">PST ({{ $setting->pst_tax }}%)</td>
                        @if ($trnx->order->used_wallet > 0)
                            <td class="text-end">
                                {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->pst_tax * ($total_amount - $trnx->order->used_wallet_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                            </td>
                        @else
                            <td class="text-end">
                                {{ getDefaultCurrencySymbol() }}{{ number_format(($setting->pst_tax * ($total_amount - $trnx->order->coupon_discount)) / 100, 2) }}
                            </td>
                        @endif
                        {{-- <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($setting->pst_tax*($total_amount-$trnx->order->coupon_discount)/100),2) }}</td> --}}
                    </tr>
                    @if ($trnx->order->coupon_discount > 0)
                    <tr>
                        <td colspan="5" class="strong text-end">Discount {{ $trnx->order?->coupon?->discount_code ? '('.$trnx->order?->coupon?->discount_code.')' : '' }}</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($trnx->order->coupon_discount),2) }}</td>
                    </tr>
                    <?php
                        $gtotalAmount -= $trnx->order->coupon_discount;
                    ?>
                    @endif
                    @if ($trnx->order->coupon_discount > 0)
                    <tr>
                        <td colspan="5" class="strong text-end">Discount {{ $trnx->order?->coupon?->discount_code ? '('.$trnx->order?->coupon?->discount_code.')' : '' }}</td>
                        <td class="text-end">-{{ getDefaultCurrencySymbol() }}{{ number_format(($trnx->order->coupon_discount),2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="5" class="font-weight-bold text-uppercase text-end">
                            <strong>Total {{ $trnx->order->payment_status==0 ? 'Due' : '' }}</strong>
                        </td>
                        <td class="font-weight-bold text-end"><strong>{{ getDefaultCurrencySymbol() }}{{ number_format($trnx->amount,2) }}</strong></td>
                    </tr>
                @else
                    @foreach ($trnx->order->details as $row)    
                    <tr>
                        <td class="text-center">{{ $loop->iteration }}</td>
                        {{-- <td class="">                                       
                            <div class="text-secondary">{{ $trnx->order->service_level_name }}</div>
                        </td> --}}
                        <td class="">{{ $row->year .' '. $row->brand_name .' '. $row->card .' '. $row->card_name }}</td>
                        <td class=""></td>
                        <td class="text-center">{{ $row->qty > 0 ? $row->qty : '' }}</td>
                        <td class="text-end">
                            @if($trnx->order->net_unit_price > 0)
                                {{ getDefaultCurrencySymbol() }}{{ $trnx->order->net_unit_price }}
                            @endif
                        </td>
                        <td class="text-end">
                            @if($row->line_price)
                                {{ getDefaultCurrencySymbol() }}{{ number_format($row->line_price,2) }}
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
                        $total_amount = $trnx->order->details->sum('line_price');
                        $gtotalAmount = $total_amount;
                    ?>
                    <tr>
                        <td colspan="5" class="strong text-end">Subtotal</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format($total_amount,2) }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="strong text-end">Shipping Charge ({{ str($trnx->order->shipping_method)->headline()->upper() }})</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($trnx->order->shipping_charge),2) }}</td>
                        <?php
                            $gtotalAmount += $trnx->order->shipping_charge;
                        ?>
                    </tr>
                    <tr>
                        <td colspan="5" class="strong text-end">GST ({{ $setting->gst_tax }}%)</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($setting->gst_tax*($total_amount-$trnx->order->coupon_discount)/100),2) }}</td>
                        <?php
                            $gtotalAmount += ($setting->gst_tax*($total_amount-$trnx->order->coupon_discount)/100);
                        ?>
                    </tr>
                    <tr>
                        <td colspan="5" class="strong text-end">PST ({{ $setting->pst_tax }}%)</td>
                        {{-- @dd($total_amount, $trnx->order->coupon_discount, ($total_amount-$trnx->order->coupon_discount)) --}}
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($setting->pst_tax*($total_amount-$trnx->order->coupon_discount)/100),2) }}</td>
                        <?php
                            $gtotalAmount += ($setting->pst_tax*($total_amount-$trnx->order->coupon_discount)/100);
                        ?>
                    </tr>
                    @if ($trnx->order->has_insurance)
                    <td colspan="5" class="strong text-end">Insurance ({{ getDefaultCurrencySymbol().$trnx->order->insurance_value }})</td>
                    <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($trnx->order->insurance_amount),2) }}</td>
                    <?php
                        $gtotalAmount += $trnx->order->insurance_amount;
                    ?>
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
                        <td colspan="5" class="strong text-end">Discount {{ $trnx->order?->coupon?->discount_code ? '('.$trnx->order?->coupon?->discount_code.')' : '' }}</td>
                        <td class="text-end">{{ getDefaultCurrencySymbol() }}{{ number_format(($trnx->order->coupon_discount),2) }}</td>
                    </tr>
                    <?php
                        $gtotalAmount -= $trnx->order->coupon_discount;
                    ?>
                    @endif
                    <tr>
                        <td colspan="5" class="font-weight-bold text-uppercase text-end">
                            <strong>Total {{ $trnx->order->payment_status==0 ? 'Due' : '' }}</strong>
                        </td>
                        <td class="font-weight-bold text-end"><strong>{{ getDefaultCurrencySymbol() }}{{ number_format($gtotalAmount,2) }}</strong></td>
                    </tr>
                    @endif
                @endif
            </tbody>
        </table>
        <div class="footer">{{ $setting->invoice_footer }}</div>
    </div>
</body>

</html>

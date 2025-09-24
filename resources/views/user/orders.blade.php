@extends('frontend.layouts.app')

@section('title')
    {{ 'My Orders' }}
@endsection



@push('style')
    <style>
    .btn-small {
        padding: 5px 7px !important;
        font-size: 12px !important;
        border-radius: 5px;
    }

    /* Hide regular table on mobile */
    @media (max-width: 991px) {
        .orders_table {
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
        }

        .orders_table table {
            display: none;
        }

        .order-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            margin-bottom: 15px;
            padding: 15px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .order-card-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .order-card-label {
            font-weight: 600;
            color: #555;
        }

        .order-card-actions {
            margin-top: 15px;
        }

        .order-card-actions ul {
            padding-left: 0;
            list-style: none;
            margin-bottom: 0;
        }

        .order-card-actions li {
            margin-bottom: 2px;
        }

        .order-card-actions li:last-child {
            margin-bottom: 0;
        }

        .order-card-actions .btn {
            width: 100%;
        }

        .order-cards-container .order-card:last-child {
            margin-bottom: 0 !important;
        }
    }

    /* Show regular table on desktop */
    @media (min-width: 992px) {
        .orders_table th:first-child {
            border-radius: 0px;
        }

        .orders_table th:last-child {
            border-radius: 0px;
        }
    }


    .plans-card {
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 15px;
        padding: 15px;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .plans-card-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
    }

    .plans-card-label {
        font-weight: 600;
        color: #555;
    }

    .plans-card-actions {
        margin-top: 15px;
    }

    @media (max-width: 991px) {
        #plansTable {
            display: none;
        }
    }

    @media (min-width: 992px) {
        .plans-cards-container {
            display: none;
        }
    }
    </style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User') }}</li>
    <li class="breadcrumb-item">{{ __('My Orders') }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->

<div class="account_seciton pb-3 pb-lg-5 pt-3">
    <div class="container">
        <div class="section_heading mb-4 d-none d-lg-block">
            <h1>My Orders</h1>
        </div>
        <div class="row gy-4 gy-lg-0">
            <div class="col-lg-3">
                @section('user_orders', 'active')
                @include('user.sidebar')
            </div>
            <div class="col-lg-9">
                <div class="user_dashboard p-0 p-xl-4 rounded border border-light">
                    @if ($orders->where('rPlan.type', 'subscription')->where('status', 50)->count() > 0)
                        <div class="header mb-4 d-none d-lg-block">
                            <div class="title">
                                <h4>My Subscriptions</h4>
                            </div>
                        </div>
                        <div class="orders_table mb-4">
                            <table id="plansTable" class="table m-0 align-middle dt-responsive nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Plan</th>
                                        <th>Price</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orders->where('rPlan.type', 'subscription')->where('total_order_qty', 0)->where('status', 50) as $order)
                                        <tr>
                                            <td><a
                                                    href="{{ route('user.order.invoice', $order->id) }}">#{{ $order->order_number }}</a>
                                            </td>
                                            <td>{{ $order->plan_name ?? $order->rPlan->name }}</td>
                                            <td>${{ number_format($order->transaction->amount, 2) }}</td>
                                            <td class="text-{{ $order->status == 0 ? 'danger' : 'success' }}">
                                                {{ $order->status == 0 ? 'Rejected' : 'Paid' }}
                                            </td>
                                            <td>{{ now()->parse($order->created_at)->format('d M, Y') }}</td>
                                            <td>
                                                <a href="{{ route('user.order.invoice', $order->id) }}"
                                                    class="btn btn-small btn-primary" title="Invoice">
                                                    View Invoice
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <!-- Card view (shown on mobile) -->
                            <div class="plans-cards-container d-lg-none">
                                @foreach ($orders->where('rPlan.type', 'subscription')->where('total_order_qty', 0)->where('status', 50) as $order)
                                <div class="plans-card">
                                    <div class="plans-card-row">
                                        <span class="plans-card-label">Order ID:</span>
                                        <span><a href="{{ route('user.order.invoice', $order->id) }}">#{{ $order->order_number }}</a></span>
                                    </div>
                                    <div class="plans-card-row">
                                        <span class="plans-card-label">Plan:</span>
                                        <span>{{ $order->plan_name ?? $order->rPlan->name }}</span>
                                    </div>
                                    <div class="plans-card-row">
                                        <span class="plans-card-label">Total:</span>
                                        <span>${{ number_format($order->transaction->amount, 2) }}</span>
                                    </div>
                                    <div class="plans-card-row">
                                        <span class="plans-card-label">Status:</span>
                                        <span class="text-{{ $order->status == 0 ? 'danger' : 'success' }}">
                                            {{ $order->status == 0 ? 'Rejected' : 'Paid' }}
                                        </span>
                                    </div>
                                    <div class="plans-card-row">
                                        <span class="plans-card-label">Date:</span>
                                        <span>{{ now()->parse($order->created_at)->format('d M, Y') }}</span>
                                    </div>

                                    <div class="plans-card-actions">
                                        <a href="{{ route('user.order.invoice', $order->id) }}"
                                            class="btn btn-small btn-primary w-100" title="Invoice">
                                            View Invoice
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="header mb-4 d-none d-lg-block">
                        <div class="title">
                            <h4>My Orders</h4>
                        </div>
                    </div>
                    <div class="orders_table">
                        <!-- Regular table (shown on desktop) -->
                        <table id="ordersTable" class="table m-0 align-middle dt-responsive nowrap" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($orders->where('total_order_qty', '!=', '0') as $order)
                                    <tr>
                                        <td><a
                                                href="{{ route('user.order.invoice', $order->id) }}">#{{ $order->order_number }}</a>
                                        </td>
                                        <td>{{ $order->details_sum_qty }}</td>
                                        {{-- <td>${{ number_format($order->transaction->amount, 2) }}</td> --}}
                                        <td>
                                            @if ($order->details_sum_qty >= 100 && $order->details_sum_qty <= 499)
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($order->details_sum_qty * $setting->min_bulk_grading_cost, 2) }}
                                            @elseif ($order->details_sum_qty >= 500) 
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($order->details_sum_qty * $setting->max_bulk_grading_cost, 2) }}
                                            @else
                                                {{ getDefaultCurrencySymbol() }}{{ number_format($order->details_sum_qty * $order->getPlanPrice(), 2) }}
                                            @endif
                                        </td>
                                        <td>{{ now()->parse($order->created_at)->format('d M, Y') }}</td>
                                        <td style="width: 30%; text-align: left;">
                                            <ul>
                                                @if ($order->status == 35 && $order->payment_status == 0)
                                                    {{-- @if ($order->cards->count() > 0 && $order->cards->where('final_grading', '>', 0)->count() > 0) --}}
                                                    @if ($order->cards->count() > 0 && $order->cards->count() > 0)
                                                        <li>
                                                            <a href="{{ route('user.order.billing', $order->id) }}"
                                                                class="btn btn-small btn-primary my-1 me-1"
                                                                title="Order Payment">
                                                                {{-- <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                                <g fill="none"><g clip-path="url(#grommetIconsSamsungPay0)">
                                                                    <path fill="#2f89e3" fill-rule="evenodd" d="M21.33 2.688c1.395 1.48 2.192 3.502 2.477 5.723c.284 2.135.17 3.587.17 3.587s.086 1.452-.199 3.587c-.284 2.22-1.082 4.243-2.477 5.723c-1.48 1.395-3.502 2.192-5.722 2.477c-2.136.256-3.588.2-3.588.2s-1.451.084-3.587-.2c-2.22-.285-4.242-1.082-5.722-2.477c-1.395-1.48-2.193-3.502-2.477-5.723c-.257-2.135-.2-3.587-.2-3.587s-.057-1.452.228-3.587C.518 6.19 1.315 4.169 2.71 2.688C4.19 1.293 6.212.496 8.433.211C10.568-.045 12.02.012 12.02.012s1.452-.085 3.587.2c2.22.284 4.242 1.081 5.723 2.476M6.582 8.496H4.447v6.292h1.167v-1.793h.968c.342 0 .655-.058.911-.172s.513-.284.712-.483a2.1 2.1 0 0 0 .484-.712c.114-.285.17-.57.17-.883a2.3 2.3 0 0 0-.17-.882a2.1 2.1 0 0 0-.484-.712a2.1 2.1 0 0 0-.712-.484a2.4 2.4 0 0 0-.91-.17Zm-.996 3.388V9.55h.91c.172 0 .342.028.485.085a1.1 1.1 0 0 1 .37.256c.085.114.17.228.228.37c.057.143.085.285.085.456c0 .17-.028.313-.085.456a1.08 1.08 0 0 1-.598.626a1.3 1.3 0 0 1-.484.085zm5.067 2.733c.314.114.598.171.912.171c.341 0 .626-.057.91-.171c.285-.142.542-.313.712-.541v.712h1.168V9.72h-1.168v.655c-.199-.2-.427-.37-.711-.484a2.3 2.3 0 0 0-.912-.17c-.341 0-.626.056-.939.17c-.285.114-.57.256-.797.484a2.7 2.7 0 0 0-.57.797c-.142.313-.199.684-.199 1.082c0 .399.086.769.228 1.082s.342.57.57.797c.227.2.512.37.796.484m1.709-.996c-.171.085-.37.114-.598.114a1.9 1.9 0 0 1-.598-.114l-.004-.002c-.17-.085-.339-.17-.48-.311a3 3 0 0 1-.313-.484a1.3 1.3 0 0 1-.114-.57c0-.199.028-.398.114-.569c.057-.17.17-.313.313-.456a1.4 1.4 0 0 1 .484-.313c.2-.085.399-.114.598-.114c.2 0 .398.029.598.143c.199.085.341.17.484.313c.142.114.227.285.313.456c.085.17.114.37.114.569s-.029.398-.114.57a1.6 1.6 0 0 1-.313.455c-.143.142-.313.228-.484.313m5.039-.427l-1.424-3.445h-1.224l2.05 4.812l-1.053 2.533h1.195l2.99-7.345H18.71z" clip-rule="evenodd"/></g><defs><clipPath id="grommetIconsSamsungPay0"><path fill="#fff" d="M0 0h24v24H0z"/>
                                                                    </clipPath></defs>
                                                                </g>
                                                            </svg> --}}
                                                                <i class="fas fa-dollar-sign"></i> Pay and Ship Order
                                                                Now
                                                            </a>
                                                        </li>
                                                    @endif
                                                @endif

                                                @if ($order->status >= 35)
                                                <li>
                                                    <a href="{{ route('user.cards', $order->id) }}"
                                                        class="btn btn-small btn-dark-gold my-1 me-1" title="View Order">
                                                        {{-- <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24">
                                                        <path fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2" d="M22 12c0-3.771 0-5.657-1.172-6.828S17.771 4 14 4h-4C6.229 4 4.343 4 3.172 5.172S2 8.229 2 12s0 5.657 1.172 6.828S6.229 20 10 20h4c3.771 0 5.657 0 6.828-1.172c.654-.653.943-1.528 1.07-2.828M10 16H6m8 0h-1.5M2 10h5m15 0H11"/>
                                                    </svg> --}}
                                                        <i class="fas fa-cart-shopping"></i> View Order and Grades
                                                    </a>
                                                </li>
                                                @endif


                                                <li>
                                                    <a href="{{ route('user.order.invoice', $order->id) }}"
                                                        class="btn btn-small btn-default border-secondary my-1"
                                                        title="Invoice">
                                                        <i class="fas fa-file-invoice"></i> View Order Status and
                                                        Invoice
                                                    </a>
                                                </li>

                                                @if ($order->status == 0)
                                                <li>
                                                    <a href="{{ route('user.order.edit', $order->id) }}"
                                                        class="btn btn-small btn-primary my-1 me-1" title="Edit Order">
                                                        <i class="fas fa-edit"></i> Edit order
                                                    </a>
                                                </li>
                                                @endif

                                                @if ($order->status >= 35 && $order->payment_status == 1 && (!empty($order->admin_tracking_id) || ($order->shipping_notes && $order->shipping_method == 'local_pickup')))
                                                    <li>
                                                        <a href="{{ route('user.order.tracking', $order->id) }}"
                                                            class="btn btn-small btn-success my-1 me-1"
                                                            title="View Order">
                                                            <i class="fas fa-map-pin"></i> Track Shipment
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Card view (shown on mobile) -->
                        <div class="order-cards-container d-lg-none">
                            @foreach ($orders->where('total_order_qty', '!=', '0') as $order)
                            <div class="order-card">
                                <div class="order-card-row">
                                    <span class="order-card-label">Order ID:</span>
                                    <span><a href="{{ route('user.order.invoice', $order->id) }}">#{{ $order->order_number }}</a></span>
                                </div>
                                <div class="order-card-row">
                                    <span class="order-card-label">Quantity:</span>
                                    <span>{{ $order->details_sum_qty }}</span>
                                </div>
                                <div class="order-card-row">
                                    <span class="order-card-label">Total:</span>
                                    <span>${{ number_format($order->details_sum_qty * $order->getPlanPrice(), 2) }}</span>
                                </div>
                                <div class="order-card-row">
                                    <span class="order-card-label">Date:</span>
                                    <span>{{ now()->parse($order->created_at)->format('d M, Y') }}</span>
                                </div>

                                <div class="order-card-actions">
                                    <ul>
                                        @if ($order->status == 35 && $order->payment_status == 0)
                                            @if ($order->cards->count() > 0 && $order->cards->count() > 0)
                                                <li>
                                                    <a href="{{ route('user.order.billing', $order->id) }}"
                                                        class="btn btn-small btn-primary my-1"
                                                        title="Order Payment">
                                                        <i class="fas fa-dollar-sign"></i> Pay and Ship Order Now
                                                    </a>
                                                </li>
                                            @endif
                                        @endif

                                        @if ($order->status >= 35)
                                        <li>
                                            <a href="{{ route('user.cards', $order->id) }}"
                                                class="btn btn-small btn-dark-gold my-1" title="View Order">
                                                <i class="fas fa-cart-shopping"></i> View Order and Grades
                                            </a>
                                        </li>
                                        @endif

                                        <li>
                                            <a href="{{ route('user.order.invoice', $order->id) }}"
                                                class="btn btn-small btn-default border-secondary my-1"
                                                title="Invoice">
                                                <i class="fas fa-file-invoice"></i> View Order Status and Invoice
                                            </a>
                                        </li>

                                        @if ($order->status == 0)
                                        <li>
                                            <a href="{{ route('user.order.edit', $order->id) }}"
                                                class="btn btn-small btn-primary my-1" title="Edit Order">
                                                <i class="fas fa-edit"></i> Edit order
                                            </a>
                                        </li>
                                        @endif

                                        @if ($order->status >= 35 && $order->payment_status == 1 && !empty($order->admin_tracking_id))
                                            <li>
                                                <a href="{{ route('user.order.tracking', $order->id) }}"
                                                    class="btn btn-small btn-success my-1"
                                                    title="View Order">
                                                    <i class="fas fa-map-pin"></i> Track Shipment
                                                </a>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            @endforeach
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
        $(document).ready(function () {
            $('#ordersTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: '', // No control
                    }
                }
            });
        });
        $(document).ready(function () {
            $('#plansTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: false,
                responsive: {
                    details: {
                        display: $.fn.dataTable.Responsive.display.childRowImmediate,
                        type: '', // No control
                    }
                }
            });
        });
    </script>
@endpush

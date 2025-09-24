@extends('frontend.layouts.app')

@section('title')
    {{ 'Checkout' }}
@endsection



@push('style')

@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('Checkout') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!-- ======================= checkout start  ============================ -->
    <div class="checkout-sec pb-5">
        <div class="container">
            <div class="mb-3 mb-lg-5">
                <div class="checkout_steps text-center">
                    @include('checkout.steps')
                </div>
            </div>
            <form action="{{ route('checkout.submission.type.store', $plan->id) }}" method="post">
                @csrf
                <div class="row gy-4 gy-lg-0">
                    <div class="col-lg-4 order-lg-2">
                        <div class="checkout_sidebar rounded  rounded-4 border-light border-light-subtle card border-1 p-4 sticky-top"
                             style="top:1rem;">
                            <div class="heading mb-4">
                                <h4>Summary</h4>
                            </div>
                            <div class="sidebar_box">
                                <table class="table table-bordered">
                                    <tr>
                                        <td>Item Type</td>
                                        <td class="text-end">Trading Cards</td>
                                    </tr>
                                    <tr>
                                        <td>Submission Type</td>
                                        <td class="text-end">Grading</td>
                                    </tr>
                                    {{-- @if($total_cards > 0)
                                        @php
                                            $subscription = $user->subscriptions->where('year_start', '<', now())->where('year_end', '>', now())->first();
                                            $available_card_limit = $subscription ? $subscription->subscription_card_peryear - $subscription->order_card_peryear : 0;
                                        @endphp
                                        @if($plan->type == 'subscription')
                                        @elseif($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now() && $available_card_limit > 0)
                                        @else
                                        <tr>
                                            <td>Items x <span
                                                    class="item-count">{{ session('checkout.items') ? $total_cards : 0 }}</span>
                                            </td>
                                            <td class="text-end">$ <span
                                                    class="totalprice">{{ number_format($total_cards * session('checkout.total_unit_price'),2,'.','') }}</span>
                                            </td>
                                        </tr>
                                        @endif
                                    @endif --}}
                                </table>

                                <div class="flip_preview text-center">
                                    <small class="d-block mb-2 text-sm text-muted">SAMPLE</small>
                                    <img src="{{ getPhoto($setting->flip_preview) }}" class="img-fluid" alt="Card Grade"
                                    style="max-width: 365px; max-height: 110px; width: 100%;">
                                </div>

                                <div>
                                    <button class="btn w-100 btn-primary py-3 mt-4">
                                        Select & Continue
                                    </button>
                                    <a href="{{ route('checkout.item.type', $plan->id) }}" class="btn btn-light py-3 w-100 mt-3">Back to Item Type</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 order-lg-1">
                        <div class="checkout_wrapper px-xl-5">
                            <div class="heading mb-4">
                                <h4>Select Submission Type</h4>
                                {{-- <p>Only one type of submission at one service level is permitted per submission form</p> --}}
                            </div>
                            <div class="item_list">
                                @foreach (config('static_array.submission_type') as $key => $item)
                                <div class="form-check mb-2 p-0">
                                    <input class="form-check-input d-none" value="{{ $loop->iteration }}" @checked($loop->index==0) type="radio" name="submission_type"
                                           id="item_{{ $key }}">
                                    <label class="form-check-label p-3 rounded-4 border w-100" for="item_{{ $key }}">
                                        <div class="content-width-2">
                                            <div class="name fw-semibold">{{ $item }}</div>
                                            <div class="info">The standard authentication and grading service for raw cards
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                @endforeach
                                <!--
                                <div class="form-check mb-2 p-0">
                                    <input class="form-check-input d-none" type="radio" name="submission_type"
                                        id="item_2">
                                    <label class="form-check-label p-3 rounded-4 border w-100" for="item_2">
                                        <div class="">
                                            <div class="name fw-semibold">Review</div>
                                            <div class="info">Items previously graded by PSA that may be worthy of a
                                                higher
                                                grade</div>
                                        </div>
                                    </label>
                                </div>
                                <div class="form-check mb-2 p-0">
                                    <input class="form-check-input d-none" type="radio" name="submission_type"
                                        id="item_3">
                                    <label class="form-check-label p-3 rounded-4 border w-100" for="item_3">
                                        <div class="">
                                            <div class="name fw-semibold">Crossover</div>
                                            <div class="info">Items graded by other companies that you want regraded and
                                                into a PSA holder</div>
                                        </div>
                                    </label>
                                </div>
                                -->
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!--======================= checkout end ============================ -->

@endsection

@push('script')
@endpush

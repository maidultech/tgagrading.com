@extends('frontend.layouts.app')

@section('title')
    {{ 'Checkout' }}
@endsection



@push('style')
<style>
    .checkout_wrapper .form-check-input:checked~.form-check-label .name {
        color: inherit !important;
    }
    .checkout_wrapper .form-check-label:before {
    position: absolute;
    content: "\f111";
    left: 0;
    font-family: 'Font Awesome 5 Free';
    font-weight: 400;
    top: 0;
}
.checkout_wrapper .form-check-input:checked~.form-check-label {
    border-color: inherit !important;
    background:  inherit !important;
}
.checkout_wrapper .form-check-input:checked~.form-check-label:before {
    color: var(--primary);
}
</style>
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
            <form action="{{ route('checkout.service.level.store',$plan->id) }}" method="post">
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
                                    <tr>
                                        <td>Service Level</td>
                                        <td class="text-end user-selected-service-level">{{ $serviceLevels->first()->name }}</td>
                                    </tr>
                                    <tr>
                                        <td>Price</td>
                                        <td class="text-end user-selected-price">{{ getDefaultCurrencySymbol() }}{{ $serviceLevels->first()->extra_price + $plan->price }}/item
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td>Max Decl. Value</td>
                                        <td class="text-end user-selected-max-decl-value">{{ getDefaultCurrencySymbol() }}{{ $serviceLevels->first()->max_declare_value }}/item
                                        </td>
                                    </tr> --}}
                                </table>

                                {{-- <div class="card p-2">
                                    <h6>What's the Max Declared Value?</h6>
                                    <small>The maximum amount per item you may claim as compensation in the event of loss or damage. 
                                        Actual compensation depends on the nature and extent of loss or damage and market value, in accordance with the TGA Terms and Conditions.
                                    </small>
                                </div> --}}

                                <div>
                                    <button type="submit" class="btn w-100 btn-primary py-3 mt-4">
                                        Select & Continue
                                    </button>
                                    <a href="{{ route('checkout.submission.type', $plan->id) }}" class="btn btn-light py-3 w-100 mt-3">Back to Submission Type</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-8 order-lg-1">
                        <div class="checkout_wrapper px-xl-5">
                            <div class="heading mb-4">
                                <h4>Select Service Level</h4>
                                <p>Only one type of submission at one service level is permitted per submission form</p>
                            </div>
                            <div class="item_list rounded-top-3 border overflow-hidden">
                                <table class="table">
                                    <thead class="">
                                        <tr>
                                            <th class="text-secondary text-xs">Service Level</th>
                                            <th class="text-secondary text-center">Est Turnaround</th>
                                            {{-- <th class="text-secondary text-center">Max Decl. Value</th> --}}
                                            <th class="text-secondary text-center">Unit Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($serviceLevels as $serviceLevel)
                                        <tr>
                                            <td>
                                                <div class="form-check p-0">
                                                    <input data-service-level="{{ $serviceLevel->name }}" data-price="{{ $serviceLevel->extra_price + $plan->price }}" data-max-decl-value="{{ $serviceLevel->max_declare_value }}"
                                                    class="form-check-input service-level-checkbox d-none" @checked($loop->first) type="radio" name="service_level"
                                                        id="item_{{ $serviceLevel->id }}" value="{{ $serviceLevel->id }}">
                                                    <label class="form-check-label w-100" for="item_{{ $serviceLevel->id }}">
                                                        <div class="ms-4">
                                                            <div class="name fw-semibold">{{ $serviceLevel->name }}</div>                                                           
                                                        </div>
                                                    </label>
                                                </div>
                                            </td>
                                            <td class="text-center">{{ $serviceLevel->estimated_days }}<br>
                                                <span class="text-center" style="font-size: 0.85rem">
                                                    Business Days
                                                </span></td>
                                           
                                            <td class="text-center">{{ getDefaultCurrencySymbol() }}{{ $serviceLevel->extra_price + $plan->price }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
<script type="text/javascript">
    $(document).on('change', '.service-level-checkbox', function(event) {
        $('.user-selected-service-level').text($(this).data('service-level')+'')
        $('.user-selected-price').text('{{ getDefaultCurrencySymbol() }}'+$(this).data('price')+'/item')
        $('.user-selected-max-decl-value').text('{{ getDefaultCurrencySymbol() }}'+$(this).data('max-decl-value')+'/item')
    });
    @if (session('checkout.submission_level'))
        $('.service-level-checkbox#item_{{ session("checkout.submission_level") }}').click()
    @endif
</script>
@endpush

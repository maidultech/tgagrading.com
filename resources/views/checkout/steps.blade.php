
@php
    $routesList = [
        "item.type" => "Item Type",
        "submission.type" => "Submission Type",
        // "service.level" => "Service Level",
        "item.entry" => "Item Entry",
        "shipping.billing" => "Shipping & Billing",
        "confirmation" => "Confirmation"
];
@endphp
@push('style')
    <style>
        /* .checkout_steps li,.checkout_steps a {
            color: #fbcc02 !important
        } */
        .checkout_steps ul li .text-primary{
            color: #fbcc02 !important;
        }
    </style>
@endpush
<ul>
    @foreach ($routesList as $rk => $route)
    @php
        $isActive = false;
        $search = array_search(str(request()->route()->getName())->after('.'), array_keys($routesList));
    @endphp
    @if( $search >= $loop->iteration)
        @php($isActive = true)
    @endif

    <li @class([
        'text-primary' => $isActive,
        'disable' => !$isActive,
    ])>
        <a

        @if ($isActive == true && $search != 4)
            href="{{ route('checkout.'.$rk,$plan->id) }}"
        @endif


        @if ($search==$loop->index)
            class="fw-bold text-primary"
        @endif

        >{{ $route }}</a> @unless ($loop->last)
        <i class="fa fa-angle-right"></i>
        @endunless
    </li>
    @endforeach
</ul>

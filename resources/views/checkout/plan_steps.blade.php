
@php
    $routesList = [
        "plan" => "Shipping & Billing",
        "plan.confirmation" => "Confirmation"
];
@endphp
<ul>
    @foreach ($routesList as $rk => $route)
    @php
        $isActive = false;
        $search = array_search(str(request()->route()->getName())->after('.'), array_keys($routesList));
    @endphp
    @if(
        $search
        >=
        $loop->iteration
        )
        @php($isActive = true)
    @endif

    <li @class([
        'text-primary' => $isActive,
        'disable' => !$isActive,
    ])>
        <a 
        
        @if ($isActive == true)
            href="{{ route('checkout.'.$rk,$plan->id) }}"
        @endif


        @if ($search==$loop->index)
            class="fw-bold"
        @endif
        
        >{{ $route }}</a> @unless ($loop->last)
        <i class="fa fa-angle-right"></i>
        @endunless
    </li>
    @endforeach
</ul>

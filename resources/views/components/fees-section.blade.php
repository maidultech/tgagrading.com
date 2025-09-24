@if ($attributes->has('plans'))

@foreach($attributes->get('plans') as $plan)
<x-fees-block :plan="$plan" />
@endforeach

{{-- @foreach($attributes->get('plans')->where('type','general') as $plan)
<x-fees-block :plan="$plan" />
@endforeach

@foreach($attributes->get('plans')->where('type','bulk') as $plan)
<x-fees-block :plan="$plan" />
@endforeach --}}

@endif
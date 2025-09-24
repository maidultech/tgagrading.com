@if ($attributes->has('plan'))
@php($plan = $attributes->get('plan'))

@if(Auth::check() && auth()->user()->is_subscriber && auth()->user()->subscription_start < now() && auth()->user()->subscription_end > now())
<?php
    $user = auth()->user();
    $available_card_limit = $user->getAvailableCardLimit();
?>
@endif
<div class="col-xl-4 col-md-6">
    <div class="pricing_item card active h-100 position-relative overflow-hidden"
        @if($plan->type == 'subscription' && isset($available_card_limit) && $available_card_limit <= 0) style="background: #67645abf;" @endif>
        @if($plan->is_badge == 'popular')
            <div class="popular-badge">
                Most Popular
            </div>
        @elseif($plan->is_badge == 'custom')
            <div class="popular-badge">
                {{$plan->custom_text}}
            </div>
        @else
            {{-- None --}}
        @endif
        <div class="pricing-head mb-4 text-center">
            <h3 class="mb-3 text-white">{{ $plan->name }}</h3>
            <p class="text-white">
                @if($plan->price > 0)
                    <span class="dollar-icon">{{ getDefaultCurrencySymbol() }}</span>{{ $plan->price }}@if($plan->type != 'subscription')<span class="month">/card</span>@endif
                @else
                    Bulk Pricing
                @endif
            </p>
        </div>
        <ul class="mb-4">
            <li>
                @if($plan->type == 'single')
                    <i class="fa fa-circle-check"></i>No Minimum
                @elseif($plan->type == 'subscription')
                    {{-- nothing --}}
                @elseif($plan->type == 'bulk')
                    <i class="fa fa-circle-check"></i>Large volume required
                @else
                    <i class="fa fa-circle-check"></i>{{$plan->minimum_card != 0 ? $plan->minimum_card.' card minimum' : 'No Minimum'}}
                @endif
            </li>
            @if($plan->features)
                @foreach($plan->features as $feature)
                    <li><i class="fa fa-circle-check"></i>{{ $feature->feature_name }}</li>
                @endforeach
            @endif
        </ul>

        @if($plan->type == 'bulk')
            <a href="{{ route('frontend.programs') }}" class="btn btn-primary mt-auto w-100">
                Contact Us
            </a>
        @else

        @if($plan->type == 'subscription')
            @if(Auth::check() && auth()->user()->is_subscriber && auth()->user()->subscription_start < now() && auth()->user()->subscription_end > now())
                @if($available_card_limit > 0)
                    <a href="{{ route('checkout.item.type', $plan->id) }}" class="btn btn-primary mt-auto w-100">
                        Submit {{$available_card_limit}} Free Cards Now
                    </a>
                @else
                    <a href="javascript:void(0)" class="btn btn-primary mt-auto w-100 disabled">
                       {{$plan->subscription_peryear_card}} Free Cards Available on: {{$user->getNextYearSubscriptionInfo()->year_start}}
                    </a>
                @endif
            @else
                <a href="{{ route('checkout.plan', $plan->id) }}" class="btn btn-primary mt-auto w-100">
                    Choose Package
                </a>
            @endif
        @else
            <a href="{{ route('checkout.item.type', $plan->id) }}" class="btn btn-primary mt-auto w-100">
                Choose Package
            </a>
        @endif


        @endif
    </div>
</div>
@endif

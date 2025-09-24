{{-- breadcrumb --}}

@if (request()->routeIs('frontend.cardGradingDetails'))
    <div class="breadcrumb_sec pt-3 pb-3">
        <div class="container">
            <nav class="m-0">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a style="color: gold" href="{{route('frontend.index')}}">{{__('messages.nav.home')}}</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
    </div>
@else
    <div class="breadcrumb_sec pt-3 pb-3">
        <div class="container">
            <nav class="m-0">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a style="color: gold" href="{{route('frontend.index')}}">{{__('messages.nav.home')}}</a></li>
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
    </div>
@endif


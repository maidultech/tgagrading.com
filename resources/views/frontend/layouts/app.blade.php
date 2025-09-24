<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta name="facebook-domain-verification" content="3hzf98nt5441eyo1cdyjxn8gr6sjw3" />

    <!-- New Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-W6DGG7DV');</script>
<!-- End Google Tag Manager -->


    <!-- Google Tag Manager -->
    <script>
        (function(w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-NQ62ZHNC');
    </script>
    <!-- End Google Tag Manager -->


  
    @if (View::hasSection('title'))
    <title>@yield('title')</title>
    @else
        <title>{{ $setting->site_name }}</title>
    @endif
    {{-- meta info --}}
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="Md. Shakib Hossain Rijon, Md. Rabin Mia, Sajjel Hossain">
    <meta http-equiv='refresh' content='{{ config('session.lifetime') * 60 }}'>
    <meta property="fb:app_id" content="741240134949263" />
    @if ($setting->app_mode == 'local')
        <meta name="robots" content="noindex,nofollow">
    @else
        <meta name="robots" content="index,follow">
    @endif
    <meta name="Developed By" content="Arobil Limited" />
    <meta name="Developer" content="Md. Mokaddes Hosain, Yasir Arafat, Md. Shakib Hossain Rijon" />
    <meta property="og:image:width" content="700" />
    <meta property="og:image:height" content="400" />
    <meta property="og:site_name" content="{{ $setting->site_name ?? '' }}" />
    <meta property="og:url" content="{{ url()->current() }}" />
    <meta property="og:type" content="website" />
    <meta name="csrf-token" id="csrf-token" content="{{ csrf_token() }}">
    @if (View::hasSection('meta'))
        @yield('meta')
    @else
        <meta property="og:title" content="{{ $setting->site_name ?? config('app.name') }} - @yield('title')" />
        <meta property="og:description" content="Welcome to Tga grading" />
        <meta property="og:image" content="{{ getIcon($setting->seo_image) }}" />
    @endif
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-2S9HMPMRYT"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());
        gtag('config', 'G-2S9HMPMRYT');
    </script>
    {{-- style  --}}
    @include('frontend.layouts.style')

    {{-- toastr style  --}}
    <link rel="stylesheet" href="{{ asset('massage/toastr/toastr.css') }}">
    @stack('style')

    <style>
        .footer_sec {
            border-top: 1px solid #EEE;
            background-image: url('{{ getIcon($setting->seo_image) }}');
            background-position: right;
            position: relative;
            background-repeat: no-repeat;
        }

        @media (max-width: 768px) {
            .footer_sec {
                background-size: contain;
            }
        }
    </style>

</head>

<body>
    <!-- New Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-W6DGG7DV"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NQ62ZHNC" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->


    {{-- header section  --}}
    @include('frontend.layouts.header')

    @if (!request()->routeIs('frontend.index'))
        @include('frontend.layouts.breadcrumb')
    @endif
    {{-- content section  --}}
    @yield('content')

    {{-- footer section  --}}
    @include('frontend.layouts.footer')

    {{-- javascript  --}}
    @include('frontend.layouts.script')

    <script src="{{ asset('massage/toastr/toastr.js') }}"></script>
    {!! Toastr::message() !!}
    <script>
        @if ($errors->any())
            toastr.error('{{ $errors->first() }}', 'Error', {
                closeButton: true,
                progressBar: true,
            });
        @endif
    </script>
    <script>
        $(document).on('click', '.subscribed', function() {
            toastr.warning('You are already in a package.');
        });

        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("submit_order");
            const submitButton = document.getElementById("submit_order_btn");
            const backButton = document.getElementById("order_back");
            if (form == null) {
                return;
            }
            form.addEventListener("submit", function(event) {
                if (submitButton.disabled) return;

                submitButton.innerHTML = `
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Processing...
            `;
                submitButton.disabled = true;

                backButton.classList.add('disabled');
                backButton.style.pointerEvents = 'none';
                backButton.style.opacity = '0.65';
            });
        });
    </script>
    {{-- custom js area  --}}

    @stack('script')
    @if (View::hasSection('schema'))
        @yield('schema')
    @else
        {!! $setting->schema_markup !!}
    @endif
    @if (session('verified_user'))
        <script>
            window.dataLayer = window.dataLayer || [];
            dataLayer.push({
                event: 'registrationComplete',
                user_id: '{{ session('verified_user')['id'] }}',
                email: '{{ session('verified_user')['email'] }}',
                registration_date: '{{ session('verified_user')['registration_date'] }}',
                form_data: {
                    username: '{{ session('verified_user')['username'] }}',
                }
            });
        </script>
        @php
            session()->forget('verified_user');
        @endphp
    @endif

</body>

</html>

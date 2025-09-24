@extends('frontend.layouts.app')

@section('title')
{{ $title ?? '' }}
@endsection

@section('meta')
    <meta property="og:title" content="{{ $seo->title ?? $og_title }}" />
    <meta property="og:description" content="{{ $seo->description ?? $og_description }}" />
    <meta property="og:image" content="{{ asset($seo->image ?? $og_image) }}" />
    <meta name="description" content="{{$seo->meta_description ?? $og_description}}">
    <meta name="keywords" content="{{$seo->keywords ?? $meta_keywords}}">
@endsection

@push('style')
{{-- <style>
    iframe {
        width: 100% !important;
        height: 350px !important;
    }
</style> --}}
<style>
    .info-text {
        color: #646464; 
        font-size: 14px;
    }
    .spinner-border {
        margin-right: 5px;
    }
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('messages.nav.contact') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <!--======================= contact start ============================ -->
    <div class="contact_section pt-4 pb-5">
        <div class="container">
            <div class="row gy-5 gy-lg-0">
                <div class="col-lg-6 order-lg-2">
                    <div class="contact_info">
                        <h1>Contact Us Anytime</h1>
                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon me-3">
                                    <img src="{{ asset('frontend/assets/images/icons/location.svg') }}" alt="Location">
                                </div>
                                <div class="info">
                                    {!! nl2br($setting->office_address) !!}
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-4">
                                <div class="icon me-3">
                                    <img src="{{ asset('frontend/assets/images/icons/email.svg') }}" alt="Email">
                                </div>
                                <div class="info">
                                    {{ $setting->support_email ??  $setting->email }}
                                    <br>
                                    <span class="info-text">24 hours a day, 7 days a week, 365 days a year <br> Please email us for the fastest response</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="icon me-3">
                                    <img src="{{ asset('frontend/assets/images/icons/phone.svg') }}" alt="Phone">
                                </div>
                                <div class="info">
                                    {{ $setting->phone_no }}
                                    <br>
                                    <span class="info-text">Monday - Friday 10am-4pm PST <br> Excludes holidays</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 order-lg-1">
                    <form action="{{ route('frontend.contact.submit') }}" class="row" method="post" onsubmit="handleSubmit(this)">
                        @csrf
                        <input type="hidden" name="contact_type" value="0">
                        <div class="col-md-6 mb-4">
                            <label for="name" class="form-label">Name*</label>
                            <input id="name" value="{{ auth()->user()?->name }}" required placeholder="Full Name*" type="text" name="name"
                                   class="form-control" @readonly(
                                    auth()->user()?->name
                                   )>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="email" class="form-label">Email Address*</label>
                            <input id="email" required placeholder="Email Address*" value="{{ auth()->user()?->email }}" type="email" name="email"
                                   class="form-control" @readonly(
                                    auth()->user()?->name
                                   )>
                        </div>
                        <div class="col-sm-12 mb-4">
                            <label for="message" class="form-label">How can we help you?*</label>
                            <textarea id="message" cols="30" rows="5"
                                      placeholder="How can we help you? Feel free to get in touch!" name="message"
                                      class="form-control" required></textarea>
                        </div>
                        <!-- Hidden reCAPTCHA Field -->
                        @if ($setting->google_recaptcha == '1')
                        <div>
                            <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                        </div>
                        @endif
                        <div class="col-sm-12">
                            <button type="submit" id="submitBtn" class="btn btn-warning py-3 px-5" style="background: #ffcc00;">
                                <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                <span id="btnText">Send a Message</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <!--======================= contact end ============================ -->
@endsection

@push('script')
    @if ($setting->google_recaptcha == '1')
        <script src="https://www.google.com/recaptcha/api.js?render={{$setting->recaptcha_site_key}}"></script>
        <script>
            grecaptcha.ready(function() {
                console.log("reCAPTCHA is ready");
                grecaptcha.execute('{{$setting->recaptcha_site_key}}', { action: 'submit' }).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                }).catch(function(error) {
                    console.error("reCAPTCHA execution error:", error);
                });
            });
        </script>
    @endif
    <script>
        function handleSubmit(form) {
            const btn = form.querySelector('#submitBtn');
            const text = form.querySelector('#btnText');
            const spinner = form.querySelector('#btnSpinner');

            btn.disabled = true;
            text.classList.add('d-none');
            spinner.classList.remove('d-none');
        }
    </script>
@endpush

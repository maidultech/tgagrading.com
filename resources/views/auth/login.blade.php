@extends('frontend.layouts.app')

@section('title')
    {{ $data['title'] ?? __('auth.sign_in') }}
@endsection


@section('meta')
    <meta property="og:title" content="Sign In - {{$setting->site_name}}" />
    <meta property="og:description" content="{{ $setting->seo_meta_description }}" />
    <meta property="og:image" content="{{ asset($setting->site_logo) }}" />
    <meta name="description" content="{{ $setting->seo_meta_description }}">
    <meta name="keywords" content="{{ $setting->seo_keywords }}">
@endsection

@push('style')
<style>
.login_form h1 {
    margin: 0;
    font-size: 22px;
    font-weight: 600;
    color: #505050;
}
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">Login</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= login start  ============================ -->
<div class="login-page pb-5 pt-lg-5 pt-2">
    <div class="container">
        <div class="login_form">
            <div class="section_heading text-center mb-3">
                <h1>Log In To Your Account</h1>
            </div>
            <div class="card px-2 px-lg-4  border-0">
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="card-body p-0">
                        <div class="mb-4">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="User">
                                </span>
                                <input type="email" name="email" tabindex="1" autofocus=""
                                    value="{{ old('email') }}" class="form-control border-start-0"
                                    placeholder="Enter your email" required="">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label d-block w-100">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/key.svg') }}" alt="User">
                                </span>
                                <input type="password" name="password" tabindex="2" id="password-field"
                                    class="form-control border-start-0 border-end-0" placeholder="Enter your password"
                                    required="">
                                <span class="input-group-text bg-transparent border-start-0">
                                    <a href="#" class="link-secondary fa fa-fw fa-eye field-icon toggle-password"
                                        toggle="#password-field">
                                    </a>
                                </span>
                            </div>
                        </div>
                        <div class="">
                            <span class="form-label-description d-block">
                                <a href="{{ route('password.request') }}">Forgot your password?</a>
                            </span>
                        </div>
                        <div class="form-footer mt-4">
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Sign in
                            </button>
                        </div>

                        <div class="divider_option mb-3 text-center">
                            <span>OR</span>
                        </div>

                        <div class="row gy-2 gx-sm-2">
                            <div class="">
                                <div class="social_login me-0 me-sm-2">
                                    <a href="{{ route('social.login', 'google') }}" class="w-100 text-center btn btn-google">
                                        <img src="{{ asset('frontend/assets/images/google.png') }}" class="me-2"
                                             width="25" alt="Login with Google">
                                        <span>Login with Google</span>
                                    </a>
                                </div>
                            </div>
                            {{--<div class="col-sm-6">
                                <div class="social_login">
                                    <a href="#" class="w-100 text-center btn btn-facebook">
                                        <img src="{{ asset('frontend/assets/images/facebook.png') }}" class="me-2"
                                            width="25" alt="Login with facebook">
                                        <span>Login with Facebook</span>
                                    </a>
                                </div>
                            </div>--}}
                        </div>
                    </div>
                    @if ($setting->google_recaptcha == '1')
                    <div>
                        <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                    </div>
                    @endif
                </form>
                <div class="text-center mt-3 form-label-description">
                    Don’t have an account? <a href="{{ route('register') }}">Register</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--======================= login end ============================ -->

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
@endpush

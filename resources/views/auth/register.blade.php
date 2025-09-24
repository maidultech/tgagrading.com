@extends('frontend.layouts.app')

@section('title')
    {{ $data['title'] ?? __('auth.sign_up') }}
@endsection

@php
    $categories = config('static_array.categories');
@endphp

@section('meta')
    <meta property="og:title" content="Sign Up - {{$setting->site_name}}" />
    <meta property="og:description" content="{{ $setting->seo_meta_description }}" />
    <meta property="og:image" content="{{ asset($setting->site_logo) }}" />
    <meta name="description" content="{{ $setting->seo_meta_description }}">
    <meta name="keywords" content="{{ $setting->seo_keywords }}">
@endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .password-requirements span{display: inline-block;}
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
    <li class="breadcrumb-item">Register</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->

<!-- ======================= register start  ============================ -->
<div class="login-page pb-4">
    <div class="container">
        <div class="login_form" style="max-width:38rem;">
            <div class="section_heading text-center mb-3">
                <h1>Create a New Account</h1>
            </div>
            <div class="card px-2 px-lg-4  border-0">
                <form action="{{ route('register') }}" method="POST" id="registerForm">
                    @csrf
                    <div class="card-body p-0 row gx-md-2">
                        <div class="mb-3 col-md-6">
                            <label class="form-label">First Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="User">
                                </span>
                                <input type="text" name="first_name" tabindex="1" autofocus=""
                                    class="form-control border-start-0" placeholder="Enter first name"
                                    value="{{ old('first_name') }}" required="">
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Last Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/user.svg') }}" alt="User">
                                </span>
                                <input type="text" name="last_name" tabindex="1"
                                    class="form-control border-start-0" placeholder="Enter last name" required=""
                                    value="{{ old('last_name') }}">
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/calendar.svg') }}" alt="Calendar">
                                </span>
                                <input type="text" name="dob" tabindex="1" placeholder="Select Date"
                                    class="form-control date border-start-0" required=""
                                    value="{{ old('dob') }}">
                            </div>
                        </div>
                        <div class="mb-3 col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/email2.svg') }}" alt="User">
                                </span>
                                <input type="email" name="email" tabindex="1" class="form-control border-start-0"
                                    placeholder="Enter your email" required="" value="{{ old('email') }}">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label d-block w-100">
                                Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/key.svg') }}" alt="Key">
                                </span>
                                <input type="password" name="password" tabindex="2" id="password-field"
                                    class="form-control border-start-0 border-end-0" placeholder="Enter your password"
                                    required="">
                                <span class="input-group-text bg-transparent border-start-0">
                                    <a href="#" class="link-secondary fa fa-fw fa-eye field-icon toggle-password"
                                        toggle="#password-field"></a>
                                </span>
                            </div>
                            <div id="password-requirements" class="mt-3 d-none">
                                <span id="upper-case" class="text-danger">✖ Uppercase letter</span>
                                <span id="lower-case" class="text-danger">✖ Lowercase letter</span>
                                <span id="number-char" class="text-danger">✖ Include Number</span>
                                <span id="special-char" class="text-danger">✖ Special character</span>
                                <span id="length" class="text-danger">✖ Minimum 8 characters</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label d-block w-100">
                                Confirm Password <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text pe-0 bg-transparent border-end-0">
                                    <img src="{{ asset('frontend/assets/images/icons/key.svg') }}" alt="Key">
                                </span>
                                <input type="password" name="password_confirmation" tabindex="2"
                                    id="confirm-toggle-password" class="form-control border-start-0 border-end-0"
                                    placeholder="Confirm your password" required="">
                                <span class="input-group-text bg-transparent border-start-0">
                                    <a href="#" class="link-secondary fa fa-fw fa-eye field-icon toggle-password"
                                        toggle="#confirm-toggle-password"></a>
                                </span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="category_id">Favorite Category <span
                                    class="text-danger">*</span></label>
                            <select name="category_id" id="category_id" class="form-control form-select" required>
                                <option value="" class="d-none">Select</option>
                                @foreach ($categories as $key => $category)
                                    <option value="{{ $key }}">{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-footer mt-4">
                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Register
                            </button>
                        </div>
                        <div class="divider_option text-center">
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
                    Alredy have an account? <a href="{{ route('login') }}">Login</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!--======================= register end ============================ -->
@endsection

@push('script')
<script>
$(document).ready(function() {
    $(document).on('input change','#password-field', function() {
        var password = $(this).val();
        checkPasswordComplexity(password,'#registerForm');
    });
});


</script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    $('.date').flatpickr({
        altInput: true,
        altFormat: "F j, Y",
        dateFormat: "Y-m-d",
        placeholder: '',
        maxDate: 'today',
    })
</script>

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

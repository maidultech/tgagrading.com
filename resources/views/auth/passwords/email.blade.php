
@extends('frontend.layouts.app')

@section('title')
{{ $data['title'] ?? __('auth.forget_pass') }}
@endsection


@section('meta')
    <meta property="og:title" content="Forgot Password - {{$setting->site_name}}" />
    <meta property="og:description" content="{{$setting->seo_meta_description}}" />
    <meta property="og:image" content="{{ asset($setting->site_logo) }}" />
    <meta name="description" content="{{$setting->seo_meta_description}}">
    <meta name="keywords" content="{{$setting->seo_keywords}}">
@endsection

@push('style')
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{__('auth.forget_pass')}}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <!-- ======================= forgot password start  ============================ -->
    <div class="login-page pb-5 pt-5">
        <div class="container">
            <div class="login_form">
                <div class="section_heading text-center mb-3">
                    <h2>Reset Your Password</h2>
                </div>
                <div class="card px-2 px-lg-4  border-0">
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        <div class="card-body p-0">
                            <div class="mb-4">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text pe-0 bg-transparent border-end-0">
                                        <img src="{{ asset('frontend/assets/images/icons/email2.svg') }}" alt="User">
                                    </span>
                                    <input type="email" name="email" tabindex="1" autofocus=""
                                           class="form-control border-start-0" placeholder="Enter your email" required="">
                                </div>
                            </div>
                            <div class="form-footer mt-4">
                                <button type="submit" class="btn btn-primary w-100 mb-3">
                                    {{-- Restore Password --}}
                                    Reset Password
                                </button>
                            </div>
                        </div>
                    </form>
                    <div class="text-center mt-3 form-label-description">
                        Back to <a href="{{ route('login') }}">Login</a> Page
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--======================= forgot password end ============================ -->
@endsection

@push('script')
@endpush

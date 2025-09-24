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
    .card-header{
        padding-top: 0.8rem !important;
        padding-bottom: 0.8rem !important;
    }
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">Email</li>
    <li class="breadcrumb-item">Verification</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= login start  ============================ -->
<div class="login-page pb-5 pt-lg-5 pt-2">

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header py-2 bg-primary text-white">{{ __('Verify Your Email Address') }}</div>

                    <div class="card-body">
                        @if (session('resent'))
                            <div class="alert alert-success" role="alert">
                                {{ __('A fresh verification link has been sent to your email address.') }}
                            </div>
                        @endif
                        <div class="w-100 text-center">
                            <img src="{{ asset('assets/images/icons/confetti.png') }}" width="110" alt="Image">
                            <br>
                            <div class="my-2">
                                {{ __('Before proceeding, please check your email for a verification link.') }}
                            {{ __('If you did not receive the email') }},
                            </div>
                            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                @csrf
                                <button type="submit"
                                    class="btn btn-primary text-center">{{ __('click here to request another') }}</button>.
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--======================= login end ============================ -->

@endsection

@push('script')
@endpush

@extends('frontend.layouts.app')

@section('title')
    {{ $row->title ?? 'How To Order' }}
@endsection

@section('meta')
    @include('frontend.layouts._meta')
@endsection
@push('style')
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ $row->title ?? 'How To Order' }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= custom page start  ============================ -->
<div class="order_section pt-3 pb-3 mb-4">
    <div class="container">
        {!! $row->body ?? '' !!}

        {{-- <!-- Step 1: Create or log in to your account -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6">
                <div class="step_img mb-3 mb-lg-0 text-lg-end">
                    <img src="{{ asset('frontend/assets/images/order/signin.png') }}" class="img-fluid" width="250"
                        alt="Create or log in to your account">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content">
                    <p><strong>Step 1:</strong> <a href="login.html">Create</a> or <a href="login.html">log in</a>
                        to your account</p>
                </div>
            </div>
        </div>

        <div class="text-end d-none d-lg-block mt-5 mb-5" style="padding-left: 318px; transform: rotate(-20deg);">
            <svg width="100%" fill="none" data-reveal="in-fade"><path d="M595 1c-5.312 32.44-14.005 32.911-28.493 54.17-14.488 22.676-40.566 37.32-86.927 53.383-16.359 9.183-58.29 17.919-77.329 20.941-27.961 4.438-57.257 4.44-85.565 4.82-22.571.303-45.071.327-67.53-2.002-48.556-5.035-97.306-19.13-146.184-19.13-21.265 0-43.185 6.042-63.875 10.529C21.527 127.522 16.22 128.888 1 138" stroke="#B5CAF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 8 4 8"></path></svg>
        </div>

        <!-- Step 2: Go to the Submit Now Section -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6 order-lg-2">
                <div class="step_img mb-3 mb-lg-0 text-lg-start">
                    <img src="{{ asset('frontend/assets/images/order/submit.png') }}" class="img-fluid" width="250"
                        alt="Submit Now Section">
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 text-lg-end">
                <div class="content">
                    <p><strong>Step 2:</strong> Go to the <a href="{{ route('frontend.pricing') }}">“Pricing and Fees”</a> Section</p>
                </div>
            </div>
        </div>

        <div class="text-center d-none d-lg-block mb-5 mt-5" style="transform: rotate(30deg);margin-top:-20px">
            <svg width="432" height="125" fill="none" stroke="red" data-reveal="in-fade">
                <path
                    d="M1.633 1.29c.308 12.988-3.497 38 10.01 54.328 14.885 19.842 32.162 24.39 52.725 28.521 36.088 7.25 72.202 8.779 109.27 1.774 11.033-2.085 21.968-8.278 32.723-12.617 11.066-4.465 22.127-8.914 33.321-12.404 23.822-7.426 47.443-7.882 71.079-5.229 20.514 2.303 41.669 2.608 61.157 11.122 30.981 6.832 49.838 47.155 58.34 57.199"
                    stroke="#F0B9DD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    stroke-dasharray="4 8 4 8"></path>
            </svg>
        </div>

        <!-- Step 3: Fill out the online submission form -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6">
                <div class="step_img mb-3 mb-lg-0 text-lg-end">
                    <img src="{{ asset('frontend/assets/images/order/online-exam.png') }}" class="img-fluid"
                        width="250" alt="Fill out the online submission form">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content">
                    <p><strong>Step 3:</strong> Fill out the online submission form</p>
                </div>
            </div>
        </div>

        <div class="text-end d-none d-lg-block mt-5 mb-5" style="padding-left: 318px; transform: rotate(-20deg);">
            <svg width="100%" fill="none" data-reveal="in-fade"><path d="M595 1c-5.312 32.44-14.005 32.911-28.493 54.17-14.488 22.676-40.566 37.32-86.927 53.383-16.359 9.183-58.29 17.919-77.329 20.941-27.961 4.438-57.257 4.44-85.565 4.82-22.571.303-45.071.327-67.53-2.002-48.556-5.035-97.306-19.13-146.184-19.13-21.265 0-43.185 6.042-63.875 10.529C21.527 127.522 16.22 128.888 1 138" stroke="#B5CAF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 8 4 8"></path></svg>
        </div>

        <!-- Step 4: Print off the completed form and package cards -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6 order-lg-2">
                <div class="step_img mb-3 mb-lg-0 text-lg-start">
                    <img src="{{ asset('frontend/assets/images/order/box.png') }}" class="img-fluid" width="250" alt="Print completed form and package your cards">
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 text-lg-end">
                <div class="content">
                    <p><strong>Step 4:</strong> Print off the completed form and package your cards securely for
                        transport</p>
                </div>
            </div>
        </div>

        <div class="text-center d-none d-lg-block mb-4" style="transform: rotate(30deg);">
            <svg width="432" height="125" fill="none"
                data-reveal="in-fade">
                <path
                    d="M1.633 1.29c.308 12.988-3.497 38 10.01 54.328 14.885 19.842 32.162 24.39 52.725 28.521 36.088 7.25 72.202 8.779 109.27 1.774 11.033-2.085 21.968-8.278 32.723-12.617 11.066-4.465 22.127-8.914 33.321-12.404 23.822-7.426 47.443-7.882 71.079-5.229 20.514 2.303 41.669 2.608 61.157 11.122 30.981 6.832 49.838 47.155 58.34 57.199"
                    stroke="#F0B9DD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    stroke-dasharray="4 8 4 8"></path>
            </svg>
        </div>

        <!-- Step 5: Ship with tracking -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6">
                <div class="step_img mb-3 mb-lg-0 text-lg-end">
                    <img src="{{ asset('frontend/assets/images/order/technical-support.png') }}" class="img-fluid"
                        width="250" alt="Ship with tracking">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content">
                    <p><strong>Step 5:</strong> Ship your submission preferably with a tracking number for peace of
                        mind</p>
                </div>
            </div>
        </div>

        <div class="text-end d-none d-lg-block mt-5 mb-5" style="padding-left: 318px; transform: rotate(-18deg);">
            <svg width="100%" fill="none" data-reveal="in-fade"><path d="M595 1c-5.312 32.44-14.005 32.911-28.493 54.17-14.488 22.676-40.566 37.32-86.927 53.383-16.359 9.183-58.29 17.919-77.329 20.941-27.961 4.438-57.257 4.44-85.565 4.82-22.571.303-45.071.327-67.53-2.002-48.556-5.035-97.306-19.13-146.184-19.13-21.265 0-43.185 6.042-63.875 10.529C21.527 127.522 16.22 128.888 1 138" stroke="#B5CAF9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" stroke-dasharray="4 8 4 8"></path></svg>
        </div>

        <!-- Step 6: Update tracking in your account -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6 order-lg-2">
                <div class="step_img mb-3 mb-lg-0 text-lg-start">
                    <img src="{{ asset('frontend/assets/images/order/classroom.png') }}" class="img-fluid"
                        width="250" alt="Update tracking in your account">
                </div>
            </div>
            <div class="col-lg-6 order-lg-1 order-lg-end">
                <div class="content">
                    <p><strong>Step 6:</strong> Update your submission in your account with your tracking number for
                        a more seamless submission (optional)</p>
                </div>
            </div>
        </div>

        <div class="text-center d-none d-lg-block mb-4" style="transform: rotate(30deg);">
            <svg width="432" height="125" fill="none"
                data-reveal="in-fade">
                <path
                    d="M1.633 1.29c.308 12.988-3.497 38 10.01 54.328 14.885 19.842 32.162 24.39 52.725 28.521 36.088 7.25 72.202 8.779 109.27 1.774 11.033-2.085 21.968-8.278 32.723-12.617 11.066-4.465 22.127-8.914 33.321-12.404 23.822-7.426 47.443-7.882 71.079-5.229 20.514 2.303 41.669 2.608 61.157 11.122 30.981 6.832 49.838 47.155 58.34 57.199"
                    stroke="#F0B9DD" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    stroke-dasharray="4 8 4 8"></path>
            </svg>
        </div>

        <!-- Step 7: Check emails for updates -->
        <div class="row gx-xl-5 d-flex align-items-center text-center text-lg-start mb-5">
            <div class="col-lg-6">
                <div class="step_img mb-3 mb-lg-0 text-lg-end">
                    <img src="{{ asset('frontend/assets/images/order/exchange-mails.png') }}" class="img-fluid"
                        width="250" alt="Check emails for updates">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="content">
                    <p><strong>Step 7:</strong> Check your email for real time updates on your order while it goes through the grading process!</p>
                </div>
            </div>
        </div>

        <div class="text-center d-none d-lg-block mb-4"  style="transform: rotate(97deg);margin-left: 40%;margin-top: -4%;margin-bottom: 3% !important;">
            <svg xmlns="http://www.w3.org/2000/svg" width="492.793" height="324.849" viewBox="0 0 492.793 324.849"><script xmlns="" id="eppiocemhmnlbhjplcgkofciiegomcon"/><script xmlns=""/><script xmlns=""/>
                <path id="Path_22881" data-name="Path 22881" d="M-19054.676-8812.465s36.613,276.049,268.613,190.338,222.074,134.334,222.074,134.334" transform="translate(19055.668 8812.597)" fill="none" stroke="#d1a3b1" stroke-width="2" stroke-dasharray="10 10"/>
              </svg>
        </div>

        <!-- Step 8: Log in and make payment -->
        <div class="text-center pb-5 mt-5">
            <div class="step_img mb-3 mb-lg-0">
                <img src="{{ asset('frontend/assets/images/order/e-payment.png') }}" class="img-fluid" width="250"
                    alt="Log in and make payment">
            </div>
            <div class="content text-sm">
                <p>
                    Once your order is updated to complete, you can log in and make
                    payment. You will be given a tracking number once your submission has been shipped back to
                    your home.
                </p>
            </div>
        </div>
        --}}

    </div>
</div>
<!-- ======================= custom page end  ============================ -->
@endsection

@push('script')
@endpush

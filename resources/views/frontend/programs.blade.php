@extends('frontend.layouts.app')

@section('title')
    {{ $title }}
@endsection

@section('meta')
    <meta property="og:title" content="{{ $seo->title ?? $og_title }}" />
    <meta property="og:description" content="{{ $seo->description ?? $og_description }}" />
    <meta property="og:image" content="{{ asset($seo->image ?? $og_image) }}" />
    <meta name="description" content="{{ $seo->meta_description ?? $og_description }}">
    <meta name="keywords" content="{{ $seo->keywords ?? $meta_keywords }}">
@endsection

@push('style')
<style>
.section_heading h1 {
    font-weight: 800;
    color: #505050;
    font-size: 28px;
}
</style>
@endpush

@section('content')
@section('breadcrumb')
    <li class="breadcrumb-item">Programs</li>
@endsection
    <div class="programs_steps pt-3 pb-5">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h1>TGA Sales</h1>
                <p>Looking to partner with us?</p>
            </div>
            <div class="row gy-5 gy-lg-0">
                @foreach($partners as $partner)
                    <div class="col-sm-6 col-lg-4">
                        <div class="card h-100 border-0 text-center">
                            <div class="program_card">
                                <div class="icon mb-3">
                                    <img src="{{ asset($partner->image) }}" width="100" class="img-fluid" alt="Image">
                                </div>
                                <div class="content">
                                    <h5 class="card-title m-0">{{ $partner->title }}</h5>
                                    <p class="card-text">{{ $partner->description }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>



    <div class="sales_form pb-5 pt-5">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h2>Contact Sales</h2>
                <p>Please fill out the form below to get in touch with one of our sales representatives</p>
            </div>
            <div class="">
                <form action="{{ route('frontend.contact.submit') }}" method="post">
                    @csrf
                    <input type="hidden" name="contact_type" value="1">
                    <div class="row d-flex justify-content-center">
                        <div class="col-lg-7 col-xl-6">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control" required
                                        placeholder="Name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" name="email" id="email" class="form-control" required
                                        placeholder="Email Address">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" id="phone" class="form-control" required
                                        placeholder="Phone">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="customer_number" class="form-label">TGA Customer Number (if applicable)</label>
                                    <input type="text" name="customer_number" id="customer_number" class="form-control"
                                        placeholder="TGA Customer Number">
                                </div>
                                <div class="col-12 mb-3">
                                    <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                    <textarea name="message" id="message" cols="30" rows="5" class="form-control" required placeholder="Write your message"></textarea>
                                </div>
                                @if ($setting->google_recaptcha == '1')
                                <div>
                                    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">
                                </div>
                                @endif
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary py-2 px-4">Send</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
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

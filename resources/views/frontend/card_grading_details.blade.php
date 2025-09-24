@extends('frontend.layouts.app')

@section('title')
    {{ $service->meta_title ?? null }}
@endsection
@section('meta')
    <meta property="og:title" content="{{ $service->meta_title ?? $og_title }}" />
    <meta property="og:description" content="{{ $service->meta_description ?? $og_description }}" />
    {{--  <meta property="og:image" content="{{ asset($service->thumb ?? $og_image) }}" />  --}}
    <meta property="og:image" content="{{ asset($service->top_bg ?? $og_image) }}" />
    <meta name="description" content="{{ $service->meta_description ?? $og_description }}">
    <meta name="keywords" content="{{ $service->meta_key ?? $meta_keywords }}">
@endsection

@push('style')
    <style>
        .section_heading h1 {
            font-size: 28px;
            font-weight: 800;
            color: #212121;
            margin-bottom: 5px;
        }

        .section_heading h2 {
            color: #212121 !important;
        }

        .footer-link {
            text-decoration: none;
            color: #212529;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding-bottom: 2px;
            border-bottom: 2px solid #212529;
        }

        .footer-text {
            text-decoration: none;
            color: #212529;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding-bottom: 2px;
            border-bottom: 2px solid #212529;
            margin-right: 6px;

        }

        .footer-link:hover {
            border-bottom: 2px solid transparent;
        }

        .footer-text:hover {
            border-bottom: 2px solid transparent;
        }

        /* Our Graded Pokémon Card */
        .slab-image {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }

        .slab-image img {
            max-width: 100%;
            height: auto;
        }

        .tab-content .tab-pane {
            padding-top: 1rem;
        }

        .img-tab-content {
            min-height: 500px;
            background-color: #000;
            padding: 2rem;
            border-radius: 1rem;
        }

        .our-graded-pokemon-card .nav-pills .nav-link {
            color: #212529;
        }

        .our-graded-pokemon-card .nav-pills .nav-link.active {
            color: #f8f9fa;
            background-color: #212529;
        }

        .our-graded-pokemon-card .tab-content ul li {
            font-size: 18px;
            position: relative;
            margin-bottom: 15px;
        }

        /* .our-graded-pokemon-card .tab-content ul li::before {
                                                                    content: "\f00c";
                                                                    font-family: "Font Awesome 6 Free";
                                                                    font-weight: 900;
                                                                    margin-right: 6px;
                                                                } */

        .card-tips-guides .card {
            transform: translateY(0);
            transition: 0.4s all ease-in-out;
        }

        .card-tips-guides .card:hover {
            transform: translateY(-3px);
        }

        .card-tips-guides .card img {
            min-height: 250px;
            width: 100%;
            height: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .card-tips-guides .card .card-title {
            display: -webkit-box;
            -webkit-line-clamp: 1;
            overflow: hidden;
            text-overflow: ellipsis;
            -webkit-box-orient: vertical;

            height: 28px;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .card-tips-guides .card .card-text {
            line-height: 32px;

            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Service Slider */
        /* Custom Carousel Styles */
        .serviceSwiper {
            padding: 5px;
            /* Create space for box-shadow */
            margin: -5px;
            /* Compensate for padding */
        }

        .serviceSwiper .card-title {
            color: #323232;
            height: 26px;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .serviceSwiper .card-text {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .swiper-slide {
            height: auto;
            transition: transform 0.3s ease;
        }

        .swiper-slide .card-img-top {
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .hover-shadow {
            transition: box-shadow 0.3s ease;
        }

        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .object-fit-contain {
            object-fit: contain;
        }

        /* Custom navigation buttons */
        .swiper-button-prev,
        .swiper-button-next {
            width: 48px;
            height: 48px;
            color: #505050;
            background: white;
            border: 1px solid #dee2e6;
        }

        .swiper-button-prev::after,
        .swiper-button-next::after {
            display: none;
        }
    </style>
@endpush

@section('content')

@section('breadcrumb')
    <li class="breadcrumb-item">{{ $service->title }}</li>
@endsection

<!-- ========================= banner start  ============================ -->
<div class="py-5" style="background: linear-gradient(to right, #000000, #333333);">
    <div class="container">
        <div class="banner_section p-5 rounded-4">

            <div class="row align-items-center g-5">
                <!-- Text Section -->
                <div class="col-lg-6">
                    <div class="text-lg-start text-center text-white">
                        <h1 class="fw-bold mb-3">{{ $service->title }}</h1>
                        <p class="mb-4" style="font-size: 1.125rem;">
                            {{ $service->sub_title }}
                        </p>
                        <div class="d-flex flex-wrap justify-content-lg-start justify-content-center gap-3">
                            @if ($service->top_links)
                                <a href="{{ $service->top_links }}"
                                    class="btn btn-outline-light btn-lg px-4 py-2 custom-hover">Learn More</a>
                            @endif
                            <a href="{{ route('frontend.pricing') }}"
                                class="btn btn-outline-light btn-lg px-4 py-2 custom-hover">Get Started</a>
                        </div>
                    </div>
                </div>

                <!-- Image Section -->
                <div class="col-lg-6 text-center">
                    <div>
                        {{--  @dd($service->top_bg);   --}}
                        <img src="{{ asset($service->top_bg) }}" alt="" class="img-fluid rounded-4 shadow"
                            style="max-height: 400px; object-fit: cover; width: 100%;">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- ======================= banner end  ============================ -->

<!-- ======================= Why Grade start  ============================ -->
@if ($service->sa_img)
    <div class="how_it_work_section custom-text-bullet">
        <div class="container">
            <div class="row gy-5 mt-3 align-items-center">

                <!-- Image on the Left -->
                <div class="col-md-5">
                    <img src="{{ asset($service->sa_img) }}" class="img-fluid rounded shadow" alt="">
                </div>

                <!-- Text on the Right -->
                <div class="col-md-7">
                    <div class="ps-xl-5 text-md-start text-center">
                        <h2 class="mb-3">{{ $service->sa_title }}</h2>
                        <p class="mb-4">
                            {!! nl2br($service->sa_details ?? '') !!}
                        </p>
                        <a href="{{ route('frontend.pricing') }}" class="btn btn-dark p-2 px-4">Submit for
                            Grading</a>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endif

<!-- ======================= Why Grade end  ============================ -->

<!-- ======================= TGA Grading start  ============================ -->
<div class="how_it_work_section">
    <div class="container">
        <div class="section_heading text-center mb-5">
            <h2>{{ $home->why_title ?? '' }}</h2>
            <p>{{ $home->why_subtitle ?? '' }}</p>
        </div>
        <div class="row gy-5 gy-lg-0 text-center position-relative">

            <div class="row g-3">
                @foreach ($why_tgas as $why)
                    <div class="col-md-3">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title"> {{ $why->title ?? '' }} </h3>
                                <p class="card-text text-muted mb-0"> {!! nl2br($why->destails ?? '') !!} </p>
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>
    </div>
</div>
<!-- ======================= TGA Grading end  ============================ -->

<!-- ======================= pricing start  ============================ -->
<div class="pricing_section pt-5 pb-5">
    <div class="container-fluid">
        <div class="section_heading text-center mb-5">
            <h3> {{ $service->pricing_heading ?? '' }}</h3>
            <p>
                {{ $service->pricing_sub_heading ?? '' }}
            </p>
        </div>
        <div class="row gy-4 justify-content-center">
            <x-fees-section :plans="$plans" />
        </div>
    </div>
</div>
<!-- ======================= pricing end  ============================ -->

<!-- ======================= Our Graded Pokémon Card - Start  ============================ -->
@if ($service->sb_img || $service->sc_img || $service->sd_img || $service->se_img)
    <div class="our-graded-pokemon-card py-5">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-start">
                <div class="col-lg-5 d-none d-lg-inline-block">
                    <div class="tab-content img-tab-content" id="slabImageTab">
                        @if ($service->sb_img)
                            <div class="tab-pane fade show active" id="img-front" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset($service->sb_img) }}" alt="">
                                </div>
                            </div>
                        @endif
                        @if ($service->sc_img)
                            <div class="tab-pane fade" id="img-top" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset($service->sc_img) }}" alt="">
                                </div>
                            </div>
                        @endif
                        @if ($service->sd_img)
                            <div class="tab-pane fade" id="img-back" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset($service->sd_img) }}" alt="">
                                </div>
                            </div>
                        @endif
                        @if ($service->se_img)
                            <div class="tab-pane fade" id="img-spine" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset($service->se_img) }}" alt="">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-lg-7">
                    <div class="py-lg-3 py-xl-5">
                        <h2 class="mb-4"> {{ $service->sb_main_title }} </h2>

                        <ul class="nav nav-pills mb-3 border p-2 rounded-pill" id="slabTab" role="tablist"
                            style="display: inline-flex">
                            @if ($service->sb_title)
                                <li class="nav-item me-2" role="presentation">
                                    <button class="nav-link active rounded-pill px-3 px-xl-4 py-1"
                                        data-bs-toggle="pill" data-bs-target="#front" type="button" role="tab">
                                        {{ $service->sb_title }}
                                    </button>
                                </li>
                            @endif
                            @if ($service->sc_title)
                                <li class="nav-item me-2" role="presentation">
                                    <button class="nav-link rounded-pill px-3 px-xl-4 py-1" data-bs-toggle="pill"
                                        data-bs-target="#top" type="button" role="tab">
                                        {{ $service->sc_title }}
                                    </button>
                                </li>
                            @endif
                            @if ($service->sd_title)
                                <li class="nav-item me-2" role="presentation">
                                    <button class="nav-link rounded-pill px-3 px-xl-4 py-1" data-bs-toggle="pill"
                                        data-bs-target="#back" type="button" role="tab">
                                        {{ $service->sd_title }}
                                    </button>
                                </li>
                            @endif
                            @if ($service->se_title)
                                <li class="nav-item me-2" role="presentation">
                                    <button class="nav-link rounded-pill px-3 px-xl-4 py-1" data-bs-toggle="pill"
                                        data-bs-target="#spine" type="button" role="tab">
                                        {{ $service->se_title }}
                                    </button>
                                </li>
                            @endif
                        </ul>

                        {{-- <div class="col-lg-5 d-lg-none my-3">
                        <div class="tab-content img-tab-content" id="slabImageTab">
                            <div class="tab-pane fade show active" id="img-front" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset('assets/images/tab-img-1.webp') }}"
                                        alt="Front">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="img-top" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset('assets/images/tab-img-2.webp') }}"
                                        alt="Top">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="img-back" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset('assets/images/tab-img-3.webp') }}"
                                        alt="Back">
                                </div>
                            </div>
                            <div class="tab-pane fade" id="img-spine" role="tabpanel">
                                <div class="slab-image">
                                    <img class="img-fluid" src="{{ asset('assets/images/tab-img-4.webp') }}"
                                        alt="Spine">
                                </div>
                            </div>
                        </div>
                    </div> --}}

                        <div class="tab-content custom-text-bullet" id="slabTabContent">
                            <div class="tab-pane fade show active" id="front" role="tabpanel"
                                data-bs-target="#img-front">
                                {!! nl2br($service->sb_details ?? '') !!}
                            </div>
                            <div class="tab-pane fade" id="top" role="tabpanel" data-bs-target="#img-top">
                                {!! nl2br($service->sc_details ?? '') !!}
                            </div>
                            <div class="tab-pane fade" id="back" role="tabpanel" data-bs-target="#img-back">
                                {!! nl2br($service->sd_details ?? '') !!}
                            </div>
                            <div class="tab-pane fade" id="spine" role="tabpanel" data-bs-target="#img-spine">
                                {!! nl2br($service->se_details ?? '') !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- ======================= Our Graded Pokémon Card - End  ============================ -->

<!-- ======================= Unlock Your Pokémon Cards? - Start  ============================ -->
<div class="py-5 my-5 bg-light text-center">
    <div class="container">
        <h2 class="mb-3 mb-lg-4 fw-semibold">{{ $service->asking_title }}</h2>
        <div class="d-flex justify-content-center align-items-center gap-3 flex-wrap">
            <a href="{{ $service->asking_link }}" class="btn btn-dark px-4 py-2 fw-semibold">
                Start Submission
            </a>
            <a href="{{ route('frontend.pricing') }}" class="text-decoration-underline text-dark fw-medium">
                Check pricing
            </a>
        </div>
    </div>
</div>
<!-- ======================= Unlock Your Pokémon Cards? - End  ============================ -->

@if ($services->count() > 3)
    <!-- ======================= additional carousel section ============================ -->
    <div class="services-carousel py-5 bg-light">
        <div class="container">
            <div class="section-heading text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">{{ $home->service_title ?? '' }}</h2>
                <p class="lead text-muted mx-auto" style="max-width: 700px;">{{ $home->service_subtitle ?? '' }}</p>
            </div>

            <div class="position-relative px-lg-5">
                <div class="swiper serviceSwiper">
                    <div class="swiper-wrapper pb-4">
                        @foreach ($services as $item)
                            <div class="swiper-slide h-auto">
                                <div
                                    class="card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow p-3">
                                    <div class="card-img-top overflow-hidden">
                                        <a href="{{ route('frontend.cardGradingDetails', $item->slug) }}">

                                            <img src="{{ asset($item->thumb) }}" class="img-fluid object-fit-contain"
                                                alt="{{ $item->title }}">
                                        </a>
                                    </div>
                                    <div class="card-body">
                                        <a href="{{ route('frontend.cardGradingDetails', $item->slug) }}">
                                            <h5 class="card-title fw-bold">{{ $item->title }}</h5>
                                        </a>
                                        <a href="{{ route('frontend.cardGradingDetails', $item->slug) }}">

                                            <p class="card-text text-secondary line-clamp-3">
                                                {{ $item->sub_title }}
                                            </p>
                                        </a>
                                    </div>
                                    <div class="card-footer bg-transparent border-top-0 p-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="{{ route('frontend.cardGradingDetails', $item->slug) }}"
                                                class="btn btn-link text-decoration-none px-0 text-dark border-bottom border-2 rounded-0 py-0">Read
                                                More</a>
                                            <a href="{{ route('frontend.pricing') }}"
                                                class="btn btn-sm btn btn-primary px-3 py-2">
                                                Submit <i class="fas fa-arrow-right ms-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Navigation -->
                <button
                    class="swiper-button-prev position-absolute top-50 start-0 translate-middle-y bg-white rounded-circle shadow-sm p-3 d-none d-lg-flex">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button
                    class="swiper-button-next position-absolute top-50 end-0 translate-middle-y bg-white rounded-circle shadow-sm p-3 d-none d-lg-flex">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
    <!-- ======================= End additional carousel section ============================ -->
@endif

<!-- ======================= how it work start  ============================ -->
<div class="how_it_work_section py-5 mb-5">
    <div class="container">
        <div class="section_heading text-center mb-5">
            <h2>{{ $home->hw_heading ?? '' }}</h2>
            <p>{{ $home->hw_sub_heading ?? '' }}</p>
        </div>
        <div class="row gy-5 gy-lg-0 text-center position-relative">
            <div class="process__line d-none d-lg-block">
                <img src="{{ asset('frontend/assets/images/icons/process-line.png') }}" alt="Process Line">
            </div>
            @if (isset($steps) && $steps->count() > 0)
                @foreach ($steps as $step)
                    <div class="col-lg-4">
                        <div class="work_process">
                            <div class="step mb-4">
                                @if (isset($step['image']) && file_exists($step['image']))
                                    <img src="{{ asset($step['image']) }}" alt="image" height="50">
                                @endif
                            </div>
                            <div class="content mt-auto">
                                <h5 class="mb-2">{{ $step->title ?? '' }}</h5>
                                <p>
                                    {{ $step->description ?? '' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
<!-- ======================= how it work end  ============================ -->

{{-- <!-- ======================= TGA Grading start  ============================ -->
    <div class="how_it_work_section">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h2>Why Grade With TGA Grading</h2>
            </div>
            <div class="row gy-5 gy-lg-0 text-center position-relative">

                <div class="row">
                    <div class="col-md-3">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"> Increase Authenticity</h5>
                                <p class="card-text text-muted">
                                    PSA has established recognized and respected universal grading standard for trading card collectors.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">  Increase Protection</h5>
                                <p class="card-text text-muted">
                                    PSA has established recognized and respected universal grading standard for trading card collectors.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"> Increase Value</h5>
                                <p class="card-text text-muted">
                                    PSA has established recognized and respected universal grading standard for trading card collectors.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card h-100 p-3 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"> Grading Report Access</h5>
                                <p class="card-text text-muted">
                                    PSA has established recognized and respected universal grading standard for trading card collectors.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ======================= TGA Grading end  ============================ -->

    <!-- ======================= cert verification start  ============================ -->
    <div class="cert_verification py-5 my-5">
        <div class="container">
            <div class="verify_certification text-center">
                <h3>{{ $home->verification_heading ?? 'Certificate' }}</h3>
                <p>
                    {{ $home->verification_sub_heading ?? 'Verify the validity of TGA certification numbers.' }}
                </p>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6 col-lg-5 col-xl-4">
                        <form action="{{ route('frontend.certification') }}" method="get">
                            <div class="mb-4">
                                <input type="number" name="number" class="form-control rounded-3 py-3 px-3"
                                    placeholder="TGA cert number" required>
                            </div>
                            <button type="submit" class="btn btn-primary py-3 px-4 rounded-3 w-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-badge-check">
                                    <path
                                        d="M3.85 8.62a4 4 0 0 1 4.78-4.77 4 4 0 0 1 6.74 0 4 4 0 0 1 4.78 4.78 4 4 0 0 1 0 6.74 4 4 0 0 1-4.77 4.78 4 4 0 0 1-6.75 0 4 4 0 0 1-4.78-4.77 4 4 0 0 1 0-6.76Z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                                Verify
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ======================= cert verification end  ============================ --> --}}

<!-- ======================= TGA Grading start  ============================ -->
<div class="how_it_work_section bg-light py-5">
    <div class="container">
        <div class="section_heading text-center my-4">
            <h2 class="my-0">FAQs About {{ $service->title }} </h2>
        </div>
        <div class="faq-body mb-4">
            <div class="col-xl-9 mx-auto">
                <div class="accordion" id="accordionExample">
                    {{-- @foreach ($faqs as $k => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header p-0">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $faq->id }}"
                                    aria-expanded="{{ $k === 0 }}" aria-controls="collapse{{ $faq->id }}">
                                    {{ $faq->title }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">

                                    {!! nl2br($faq->body) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach --}}
                    @forelse ($faqs as $k => $faq)
                        <div class="accordion-item">
                            <h2 class="accordion-header p-0">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapse{{ $faq->id }}"
                                    aria-expanded="{{ $k === 0 }}" aria-controls="collapse{{ $faq->id }}">
                                    {{ $faq->title }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">

                                    {!! nl2br($faq->body) !!}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center">No data found</div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</div>
<!-- ======================= TGA Grading end  ============================ -->

<!-- ======================= Card Grading Tips & Guides - Start  ============================ -->
<div class="card-tips-guides py-5">
    <div class="container">
        <div class="section_heading text-center my-4">
            <h2 class="my-0">Card Grading Tips & Guides</h2>
        </div>
        <div class="row g-4">
            @forelse ($blogs as $blog)
                <div class="col-sm-6 col-md-4">
                    <a href="{{ route('frontend.blogs.details', $blog->slug) }}"
                        class="d-block text-decoration-none">
                        <div class="card rounded-3 overflow-hidden">
                            <img src="{{ getPhoto($blog->image) }}" class="card-img-top img-fluid"
                                alt="{{ $blog->title }}">
                            <div class="card-body">
                                <h4 class="card-title"> {{ $blog->title }} </h4>
                                <h6 class="card-text mb-0 pb-0 fs-5 fw-normal"> {!! \Illuminate\Support\Str::limit(strip_tags($blog->details), 100, '...') !!} </h6>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <p class="text-center">No Data found</p>
            @endforelse

        </div>
    </div>
</div>
<!-- ======================= Card Grading Tips & Guides - Snd  ============================ -->

@endsection
@if (!empty($service->schema_markup))
@section('schema')
    {!! $service->schema_markup !!}
@endsection
@endif
@push('script')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        new Swiper(".serviceSwiper", {
            slidesPerView: 1,
            spaceBetween: 20,
            loop: true,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            breakpoints: {
                576: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25
                },
                992: {
                    slidesPerView: 3,
                    spaceBetween: 30
                },
                1200: {
                    slidesPerView: 4,
                    spaceBetween: 30
                }
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: true,
            },
        });
    });
</script>
@endpush

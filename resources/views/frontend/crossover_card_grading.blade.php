@extends('frontend.layouts.app')

@section('title')
    {{ $service->meta_title ?? null }}
@endsection

@section('meta')
    <meta property="og:title" content="{{ $service->meta_title ?? $og_title }}" />
    <meta property="og:description" content="{{ $service->meta_description ?? $og_description }}" />
    <meta property="og:image" content="{{ asset($service->thumb ?? $og_image) }}" />
    <meta name="description" content="{{ $service->meta_description ?? $og_description }}">
    <meta name="keywords" content="{{ $service->meta_key ?? $meta_keywords }}">
@endsection

@php
    $serviceExtra = $service->serviceExtra ?? null;
@endphp

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

        .carousel-img {
            height: 400px;
            /* or any fixed height */
            object-fit: contain;
            object-position: center;
            background-color: #f8f9fa;
            /* Optional: fills empty space around image */
        }

        .custom_swiper_section {
            /* padding: 80px 0px; */
        }

        .swiper-slide {
            display: flex;
            height: auto;
        }

        .swiper-button-next,
        .swiper-button-prev {
            width: 40px;
            height: 40px;
            background-color: #ffffff;
            /* Bootstrap primary blue */
            color: #212529;
            border-radius: 19px;
            border: 1px solid;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 1;
        }

        .swiper-button-next::after,
        .swiper-button-prev::after {
            font-size: 16px;
            font-weight: bold;
        }

        .swiper-button-next {
            right: 10px;
        }

        .swiper-button-prev {
            left: 10px;
        }

        .swiper-button-next:hover,
        .swiper-button-prev:hover {
            background-color: #212529;
            /* Hover background color */
            color: #ffffff;
            /* Hover icon color */
            border: 1px solid #212529;
        }

        .card_img {
            text-align: center !important;
            display: flex;
            -ms-flex-pack: center;
            justify-content: center;
            -ms-flex-align: center;
            align-items: center;
            height: 400px;
            overflow: hidden;
            border-radius: 12px 12px 0 0 !important;
        }

        .card_img img {
            width: 85%;
            height: 85%;
            max-height: 400px;
            display: block;
            margin-left: auto;
            margin-right: auto;
            overflow: hidden;
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

        .submit-icon {
            background-color: #fff;
            border-radius: 50%;
            padding: 6px 6px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #212529;
            transition: background-color 0.3s ease;
        }

        .submit-icon:hover {
            background-color: #212529;
        }

        .submit-icon:hover i {
            color: #fff;
        }

        .submit-icon i {
            color: #212529;
            font-size: 14px;
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

    <style>
        .choose-tga-grading-services .card .icon {
            height: 60px;
            width: 60px;
        }
    </style>

    <style>
        .choose-tga-grading-services .card .icon {
            height: 60px;
            width: 60px;
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
                            {!! nl2br($service->details) !!}
                        </p>
                        <div class="d-flex flex-wrap justify-content-lg-start justify-content-center gap-3">
                            @if ($service->top_links)
                                <a href="{{ $service->top_links }}"
                                    class="btn btn-outline-light btn-lg px-4 py-2 custom-hover">Submit card
                                    grading</a>
                            @endif
                            <a href="{{ route('frontend.pricing') }}"
                                class="btn btn-outline-light btn-lg px-4 py-2 custom-hover">Check Pricing</a>
                        </div>
                    </div>
                </div>

                <!-- Image Section -->
                <div class="col-lg-6 text-center">
                    <div>
                        <img src="{{ asset(path: $service->top_bg) }}" alt="Banner Image"
                            class="img-fluid rounded-4 shadow"
                            style="max-height: 400px; object-fit: cover; width: 100%;">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- ======================= banner end  ============================ -->


@if (@$serviceExtra->sf_img)
    <div class="how_it_work_section custom-text-bullet py-5">
        <div class="container mt-3">
            <div class="row gy-5 align-items-center mb-4">
                <div class="col-md-7">
                    <div class="pe-xl-5">
                        <div class="section_heading mt-4 mb-3" style="margin-top: -30px">
                            <h2 class="mb-0"> {{ $serviceExtra->sf_title }}</h2>
                        </div>

                        <p class="mb-3"> {!! nl2br($serviceExtra->sf_details) !!} </p>
                        {{-- <a href="{{ $serviceExtra->sf_link }}" class="btn btn-primary px-3 py-2">Submit card grading</a> --}}
                    </div>
                </div>
                <div class="col-md-5">
                    {{-- <h6 class="fw-semibold text-center mb-2"> Recently Graded </h6> --}}

                    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            <div class="carousel-item active" data-bs-interval="10000">
                                <img src="{{ asset($serviceExtra->sf_img) }}" class="d-block w-100 carousel-img"
                                    alt="...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if (@$serviceExtra->sg_img)
    <div class="how_it_work_section custom-text-bullet py-5">
        <div class="container mt-3">
            <div class="row gy-5 align-items-center mb-4">

                <div class="col-md-5">
                    {{-- <h6 class="fw-semibold text-center mb-2"> Recently Graded </h6> --}}

                    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            <div class="carousel-item active" data-bs-interval="10000">
                                <img src="{{ asset($serviceExtra->sg_img) }}" class="d-block w-100 carousel-img"
                                    alt="...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="pe-xl-5">
                        <div class="section_heading mt-4 mb-3" style="margin-top: -30px">
                            <h2 class="mb-0"> {{ $serviceExtra->sg_title }}</h2>
                        </div>

                        <p class="mb-3"> {!! nl2br($serviceExtra->sg_details) !!} </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="container custom-text-bullet">
    <div class="section_heading text-center mb-2">
        <h2> {{ $service->sb_main_title ?? null }} </h2>
        <p>
            {!! nl2br($service->sa_details ?? null) !!}
        </p>
    </div>

</div>

@if ($service->sb_img)
    <div class="how_it_work_section custom-text-bullet pb-5">
        <div class="container mt-3">
            <div class="row gy-5 align-items-center mb-4">
                <div class="col-md-7">
                    <div class="pe-xl-5">
                        <div class="section_heading mt-4 mb-3" style="margin-top: -30px">
                            <h2 class="mb-0"> {{ $service->sb_title }}</h2>
                        </div>

                        <p class="mb-3"> {!! nl2br($service->sb_details) !!} </p>
                    </div>
                </div>
                <div class="col-md-5">
                    {{-- <h6 class="fw-semibold text-center mb-2"> Recently Graded </h6> --}}

                    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            <div class="carousel-item active" data-bs-interval="10000">
                                <img src="{{ asset($service->sb_img) }}" class="d-block w-100 carousel-img"
                                    alt="...">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($service->sc_img)
    <div class="how_it_work_section custom-text-bullet py-5">
        <div class="container mt-3">
            <div class="row gy-5 align-items-center mb-4">

                <div class="col-md-5">
                    {{-- <h6 class="fw-semibold text-center mb-2"> Recently Graded </h6> --}}

                    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">

                        <div class="carousel-inner">
                            <div class="carousel-item active" data-bs-interval="10000">
                                <img src="{{ asset($service->sc_img) }}" class="d-block w-100 carousel-img"
                                    alt="...">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="pe-xl-5">
                        <div class="section_heading mt-4 mb-3" style="margin-top: -30px">
                            <h2 class="mb-0"> {{ $service->sc_title }}</h2>
                        </div>

                        <p class="mb-3"> {!! nl2br($service->sc_details) !!} </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif


<!-- ======================= pricing start  ============================ -->
<div class="pricing_section pt-5 pb-5">
    <div class="container-fluid">
        <div class="section_heading text-center mb-5">
            <h2> {{ $service->pricing_heading ?? 'Pricing & Turnaround Time' }} </h2>
            <p>
                {{ $service->pricing_sub_heading ?? 'Explore transparent pricing plans to elevate your card collection. Choose the perfect plan for seamless card grading services' }}
            </p>
        </div>
        <div class="row gy-4 justify-content-center mb-5">
            <x-fees-section :plans="$plans" />
        </div>
    </div>
</div>
<!-- ======================= pricing end  ============================ -->



<!-- ======================= Our Graded Pokémon Card - Start  ============================ -->
@if ($service->sd_img || $service->se_img)
    <div class="our-graded-pokemon-card py-5">
        <div class="container">
            <div class="row g-4 g-lg-5 align-items-start">
                <div class="col-lg-5 d-none d-lg-inline-block">
                    <div class="tab-content img-tab-content" id="slabImageTab">
                        @if ($service->sd_img)
                            <div class="tab-pane fade show active" id="img-back" role="tabpanel">
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
                        <h2 class="mb-4"> Regular vs Express Crossover </h2>

                        <ul class="nav nav-pills mb-3 border p-2 rounded-pill" id="slabTab" role="tablist"
                            style="display: inline-flex">
                            @if ($service->sd_title)
                                <li class="nav-item me-2" role="presentation">
                                    <button class="nav-link active rounded-pill px-3 px-xl-4 py-1"
                                        data-bs-toggle="pill" data-bs-target="#back" type="button" role="tab">
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
                            <div class="tab-pane fade show active" id="back" role="tabpanel"
                                data-bs-target="#img-back">
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


<!-- ======================= Why Grade - Start  ============================ -->
{{-- <div class="choose-tga-grading-services bg-light py-4 py-lg-5">
    <div class="container">
        <div class="section_heading text-center my-4">
            <h2> Why Choose TGA Grading Services? </h2>
        </div>

        <div class="row mb-4 mb-lg-5">
            <div class="col-sm-6 col-md-4">
                <div class="card p-4">
                    <div class="card-body">
                        <div
                            class="icon bg-light d-flex justify-content-center align-items-center p-3 rounded-circle mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-trophy-icon lucide-trophy">
                                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
                                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
                                <path d="M4 22h16" />
                                <path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22" />
                                <path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22" />
                                <path d="M18 2H6v7a6 6 0 0 0 12 0V2Z" />
                            </svg>
                        </div>
                        <h5 class="card-title mb-0"> Increase Collectability </h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the card’s content.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card p-4">
                    <div class="card-body">
                        <div
                            class="icon bg-light d-flex justify-content-center align-items-center p-3 rounded-circle mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-umbrella-icon lucide-umbrella">
                                <path d="M22 12a10.06 10.06 1 0 0-20 0Z" />
                                <path d="M12 12v8a2 2 0 0 0 4 0" />
                                <path d="M12 2v1" />
                            </svg>
                        </div>
                        <h5 class="card-title mb-0"> Increase Protection </h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the card’s content.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-md-4">
                <div class="card p-4">
                    <div class="card-body">
                        <div
                            class="icon bg-light d-flex justify-content-center align-items-center p-3 rounded-circle mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1"
                                stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-gem-icon lucide-gem">
                                <path d="M6 3h12l4 6-10 13L2 9Z" />
                                <path d="M11 3 8 9l4 13 4-13-3-6" />
                                <path d="M2 9h20" />
                            </svg>
                        </div>
                        <h5 class="card-title mb-0"> Increase Value </h5>
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the card’s content.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> --}}
<!-- ======================= Why Grade - End  ============================ -->
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

<!-- ======================= How it work start  ============================ -->
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
<!-- ======================= How it work end  ============================ -->

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
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-badge-check">
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
<!-- ======================= cert verification end  ============================ -->

<!-- ======================= Faq start  ============================ -->
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
<!-- ======================= Faq end  ============================ -->

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

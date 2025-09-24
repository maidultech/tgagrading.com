@extends('frontend.layouts.app')

{{-- @section('title')
    {{ $title }}
@endsection --}}

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
            font-size: 28px;
            font-weight: 800;
            color: #212121;
            margin-bottom: 5px;
        }

        .section_heading h2 {
            color: #212121 !important;
        }

        /*
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
                            text-decoration: underline;
                            text-decoration-thickness: 2px;
                        }

                        /* .submit-icon {
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
                        } */

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
    <!-- ========================= banner start  ============================ -->

    <div class="banner_section">

        @if ($setting->app_mode == 'live')
            <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    @foreach ($banners as $key => $banner)
                        <button type="button" data-bs-target="#carouselExampleSlidesOnly"
                            data-bs-slide-to="{{ $key }}" {{ $key == 0 ? 'class=active' : '' }}
                            {{ $key == 0 ? 'aria-current=true' : '' }} aria-label="Slide {{ $key + 1 }}"></button>
                    @endforeach
                </div>
                <div class="carousel-inner">
                    @foreach ($banners as $key => $banner)
                        <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                            <a href="{{ route('frontend.pricing') }}">
                                <img src="{{ asset($banner->image) }}" class="img-fluid" alt="Banner Image">
                            </a>
                            @if ($banner->info_active == 1)
                                <div class="carousel-caption">
                                    <h1 class="banner_title mb-3 {{ $key == 0 ? 'text-white' : '' }} ">
                                        {{ $banner->title }}</h1>
                                    <ul class="{{ $key == 0 ? 'text-white' : 'text-black' }} ">
                                        {!! $banner->description ?? '' !!}
                                    </ul>
                                    @auth
                                        <div class="mt-3">
                                            <a href="{{ route('frontend.pricing') }}" class="btn btn-primary py-2 px-3">Submit
                                                Your
                                                Cards Now</a>
                                        </div>
                                    @endauth
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div>
                <img src="{{ asset('assets/tga_banner.jpg') }}" class="img-fluid" alt="Banner Image" style="width: 100%">
            </div>
        @endif

    </div>

    <!-- ======================= banner end  ============================ -->

    <!-- ======================= TGA Grading start  ============================ -->
    <div class="how_it_work_section">
        <div class="container">
            <div class="row gy-5 mt-3 align-items-center">
                <div class="col-md-7">
                    <div class="pe-xl-5">
                        <h1 class="mb-3">{{ $home->sb_title ?? '' }}</h1>
                        <p class="mb-3">{!! nl2br($home->sb_details ?? '') !!}
                        </p> <a href="{{ $home->sb_button_links ?? '' }}"
                            class="btn btn-primary px-4 py-2">{{ $home->sb_button_text ?? '' }}</a>
                    </div>
                </div>
                <div class="col-md-5">
                    <div id="carouselExampleDark" class="carousel carousel-dark slide" data-bs-ride="carousel">
                        <div class="carousel-indicators">
                            @foreach ($image_contents as $key => $img_btn)
                                <button type="button" data-bs-target="#carouselExampleDark"
                                    data-bs-slide-to="{{ $key ?? 0 }}" class="{{ $loop->first ? 'active' : '' }}"
                                    aria-current="true" aria-label="Slide 1"></button>
                            @endforeach
                        </div>
                        <div class="carousel-inner">
                            @foreach ($image_contents as $img_cont)
                                <a href="{{ $img_cont->link }}">
                                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-bs-interval="10000">
                                        <div class="row justify-content-center">
                                            <img src="{{ asset($img_cont->image) }}" style="width: 320px"
                                                class="d-block carousel-img" alt="{{ $img_cont->name }}">
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ======================= TGA Grading end  ============================ -->

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
                            @foreach ($services as $service)
                                <div class="swiper-slide h-auto">
                                    <div
                                        class="card h-100 border-0 shadow-sm overflow-hidden transition-all hover-shadow p-3">
                                        <div class="card-img-top overflow-hidden">
                                            <a href="{{ route('frontend.cardGradingDetails', $service->slug) }}">

                                                <img src="{{ asset($service->thumb) }}"
                                                    class="img-fluid object-fit-contain" alt="{{ $service->title }}">
                                            </a>
                                        </div>
                                        <div class="card-body">
                                            <a href="{{ route('frontend.cardGradingDetails', $service->slug) }}">
                                                <h5 class="card-title fw-bold">{{ $service->title }}</h5>
                                            </a>
                                            <a href="{{ route('frontend.cardGradingDetails', $service->slug) }}">

                                                <p class="card-text text-secondary line-clamp-3">
                                                    {{ $service->sub_title }}
                                                </p>
                                            </a>
                                        </div>
                                        <div class="card-footer bg-transparent border-top-0 p-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('frontend.cardGradingDetails', $service->slug) }}"
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
    <div class="how_it_work_section">
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
                                    <h3 class="mb-2">{{ $step->title ?? '' }}</h3>
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

    <!-- ======================= pricing start  ============================ -->
    <div class="pricing_section pt-5 pb-5">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h2>{{ $home->pricing_heading ?? 'True Grade Pricing' }}</h2>
                <p>
                    {{ $home->pricing_sub_heading ?? 'Explore transparent pricing plans to elevate your card collection. Choose the perfect plan for seamless card grading services' }}
                </p>
            </div>
            <div class="row g-4">
                <x-fees-section :plans="$plans" />
            </div>
        </div>
    </div>
    <!-- ======================= pricing end  ============================ -->

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
                                    <h5 class="card-title"> {{ $why->title ?? '' }} </h5>
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

    <!-- ======================= TGA Grading start  ============================ -->
    <div class="how_it_work_section bg-light">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h2>FAQs About Card Grading</h2>
            </div>
            <div class="faq-body mb-4">
                <div class="col-xl-9 mx-auto">
                    <div class="accordion" id="accordionExample">
                        @foreach ($faqs as $k => $faq)
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
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- ======================= TGA Grading end  ============================ -->

@endsection

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

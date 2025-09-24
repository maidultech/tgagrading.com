@php
    $footerServices = DB::table('services')->where('status', 1)->where('type', 2)->inRandomOrder()->get();

    $brands = DB::table('brands')->where('status', 1)->orderBy('order_id', 'asc')->get();
@endphp

<!-- ======================= footer start  ============================ -->
<footer class="footer-section bg-white pt-5 pb-4 border-top text-center text-md-start">
    <div class="container">
        <div class="row g-4">
            <!-- Company Info -->
            <div class="col-lg-6 col-xl-3">
                <div class="footer-widget mb-4">
                    <div class="widget-header mb-3">
                        <a href="{{ route('frontend.index') }}">
                            <img src="{{ file_exists(public_path($setting->site_logo)) ? asset($setting->site_logo) : asset('assets/default.png') }}"
                                class="img-fluid" style="max-width: 200px; max-height: 100px;"
                                alt="{{ config('app.name') }}">
                        </a>
                    </div>
                    <div class="widget-content mb-3 text-secondary">
                        {!! nl2br($setting->office_address) !!}
                    </div>
                    <div class="social-icons">
                        <h6 class="text-dark mb-0 mb-md-2 fw-bold">Follow Us</h6>
                        <div class="d-flex justify-content-center justify-content-md-start gap-3">
                            @if ($setting->facebook_url)
                                <a href="{{ $setting->facebook_url }}" class="text-dark" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            @endif
                            @if ($setting->twitter_url)
                                <a href="{{ $setting->twitter_url }}" class="text-dark" aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            @endif
                            @if ($setting->linkedin_url)
                                <a href="{{ $setting->linkedin_url }}" class="text-dark" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            @endif
                            @if ($setting->instagram_url)
                                <a href="{{ $setting->instagram_url }}" class="text-dark" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            @endif
                            @if ($setting->whatsapp_number)
                                <a href="https://wa.me/{{ $setting->whatsapp_number }}" class="text-dark"
                                    aria-label="WhatsApp">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Support & Blog -->
            <div class="col-lg-6 col-xl-2">
                <div class="footer-widget">
                    <h5 class="text-dark mb-0 mb-md-2 fw-bold">Support</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('frontend.about') }}"
                                class="text-secondary small text-decoration-none hover-primary">About Us</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.contact') }}"
                                class="text-secondary small text-decoration-none hover-primary">Contact Us</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.sitemap') }}"
                                class="text-secondary small text-decoration-none hover-primary">Sitemap</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.privacy') }}"
                                class="text-secondary small text-decoration-none hover-primary">Privacy Policy</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.terms') }}"
                                class="text-secondary small text-decoration-none hover-primary">Terms & Conditions</a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-6 col-xl-2">
                <div class="footer-widget">
                    <h5 class="text-dark mb-0 mb-md-2 fw-bold">Quick Links</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="{{ route('frontend.faq') }}"
                                class="text-secondary small text-decoration-none hover-primary">FAQs</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.blogs') }}"
                                class="text-secondary small text-decoration-none hover-primary">Blogs</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.pricing') }}"
                                class="text-secondary small text-decoration-none hover-primary">Pricing</a></li>
                        <li class="mb-2"><a href="{{ route('frontend.grading-scale') }}"
                                class="text-secondary small text-decoration-none hover-primary">Grading Scale</a></li>
                    </ul>
                </div>
            </div>

            <!-- Services -->
            <div class="col-lg-6 col-xl-2">
                <div class="footer-widget">
                    <h5 class="text-dark mb-0 mb-md-2 fw-bold">Services</h5>
                    @if (count($footerServices) > 0)
                        <ul class="list-unstyled">
                            @foreach ($footerServices as $service)
                                <li class="mb-2">
                                    <a href="{{ route('frontend.cardGradingDetails', $service->slug) }}"
                                        class="text-secondary small text-decoration-none hover-primary">
                                        {{ $service->title }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-secondary small">No Service</p>
                    @endif
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <a href="{{ route('frontend.cardGradingService') }}"
                                class="text-secondary small text-decoration-none hover-primary">
                                Trading Card Grading
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('frontend.sportsCardGrading') }}"
                                class="text-secondary small text-decoration-none hover-primary">
                                Sports Card Grading
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="{{ route('frontend.crossoverGradingService') }}"
                                class="text-secondary small text-decoration-none hover-primary">
                                Crossover Card Grading
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Newsletter & Partners -->
            <div class="col-lg-6 col-xl-3">
                <div class="footer-widget">
                    <h5 class="text-dark mb-0 mb-md-2 fw-bold">Subscribe Now</h5>
                    <form action="{{ route('newsletter') }}" method="post" class="mb-4">
                        @csrf
                        <div class="input-group">
                            <input type="email" name="email" id="email"
                                class="form-control form-control-sm border" required placeholder="Your email address">
                            <button type="submit" class="btn btn-primary btn-sm" aria-label="Subscribe">
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </form>

                    <h5 class="text-dark mb-0 mb-md-2 fw-bold">Our Partners</h5>
                    <div class="partner-logos d-flex flex-wrap gap-2">
                        @foreach ($brands as $brand)
                            @if (!empty($brand->link))
                                <a href="{{ $brand->link }}" target="_blank" class="d-inline-block">
                                    <img src="{{ asset($brand->image) }}" class="img-fluid"
                                        style="width: 70px; height: 70px; object-fit: contain;"
                                        alt="{{ $brand->name }}">
                                </a>
                            @else
                                <img src="{{ asset($brand->image) }}" class="img-fluid"
                                    style="width: 70px; height: 70px; object-fit: contain;"
                                    alt="{{ $brand->name }}">
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
<!-- ======================= footer end  ============================ -->

<!-- ======================= copyright start  ============================ -->
<div class="copyright bg-light py-3 border-top">
    <div class="container">
        <div class="text-center">
            <p class="m-0 text-secondary small">
                @if ($setting->copyright_text)
                    {{ $setting->copyright_text }}
                @else
                    Copyright © {{ date('Y') }} {{ config('app.name') }}. All Rights Reserved
                @endif
            </p>
        </div>
    </div>
</div>
<!-- ======================= copyright end  ============================ -->

<style>
    .footer-section a:hover {
        text-decoration: underline !important;
    }

    .border-top {
        border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
    }
</style>

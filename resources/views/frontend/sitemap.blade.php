@extends('frontend.layouts.app')

@section('title')
    {{ $title ?? 'Sitemap' }}
@endsection

@section('meta')
    <meta property="og:title" content="{{ $seo->title ?? $og_title }}" />
    <meta property="og:description" content="{{ $seo->description ?? $og_description }}" />
    <meta property="og:image" content="{{ asset($seo->image ?? $og_image) }}" />
    <meta name="description" content="{{$seo->meta_description ?? $og_description}}">
    <meta name="keywords" content="{{$seo->keywords ?? $meta_keywords}}">
@endsection
@push('style')
<style>
    .list-unstyled {
        padding-left: 0;
        list-style: none;
    }
    .sitemap_link li {
        padding-bottom: 5px;
    }
    .sitemap_link li a {
        text-decoration: none;
        color: #284889;
        font-size: 13px;
    }
    .page_content .page_title {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 0px;
    }
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">Sitemap</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!--======================= sitemap start ============================ -->
    <div class="sitemap_sction pt-3 pb-5">
        <div class="container">
            <div class="bg-white rounded p-4 border">
                <div class="page_content">
                    <h1 class="page_title">Sitemap</h1>
                    <div class="desc">
                        <div class="col-sm-12">
                            <div style="border-bottom: solid 1px #dad7d2; margin-top: 10px;"></div>
                        </div>
                        <div class="col-sm-12">
                            <h5 style="font-size: 15px; color: #bb0c13;padding: 13px 0 11px 0; font-weight: bold;">General Site Links</h5>
                            <ul class="list-unstyled sitemap_link row">
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.index') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="20"  height="20"  
                                        viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-home mb-1">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l-2 0l9 -9l9 9l-2 0" /><path d="M5 12v7a2 2 0 0 0 2 2h10a2 2 0 0 0 2 -2v-7" /><path d="M9 21v-6a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v6" /></svg> Home</a>
                                </li>
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.about') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> About us</a>
                                </li>
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.contact') }}">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  fill="none"  
                                        stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-address-book mb-1">
                                        <path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 6v12a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2z" /><path d="M10 16h6" /><path d="M13 11m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" /><path d="M4 8h3" /><path d="M4 12h3" /><path d="M4 16h3" /></svg>
                                         Contact us</a>
                                </li>
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.faq') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> FAQs</a>
                                </li>
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.privacy') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Privacy Policy</a>
                                </li>
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.terms') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Terms & Conditions</a>
                                </li>
                
                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.blogs') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> News</a>
                                </li>

                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.pricing') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Pricing</a>
                                </li>

                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.grading-scale') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Grading Scale</a>
                                </li>

                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.sitemap') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Sitemap</a>
                                </li>

                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.howToOrder') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> How To Order</a>
                                </li>

                                <li class="col-md-4">
                                    <a target="_blank" href="{{ route('frontend.certification') }}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Certificate Verification</a>
                                </li>

                                <li class="col-md-4">
                                    <a target="_blank" href="{{route('login')}}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Login</a>
                                </li>
                                <li class="col-md-4">
                                    <a target="_blank" href="{{route('register')}}"><svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                        fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                        class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                        <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                        <path d="M9 17h6" /><path d="M9 13h6" /></svg> Registration</a>
                                </li>
                            </ul>
                        </div>



                        <div class="col-sm-12">
                            <div style="border-bottom: solid 1px #dad7d2; margin-top: 10px;"></div>
                        </div>
                        <div class="col-sm-12">
                            <h5 style="font-size: 15px; color: #bb0c13;padding: 13px 0 11px 0; font-weight: bold;">News</h5>
                            <ul class="list-unstyled sitemap_link row">
                                @if (!empty($blogs))
                                    @foreach($blogs as $item)
                                    <li class="col-md-4">
                                        <a target="_blank" class="d-flex gap-1" href="{{ route('frontend.blogs.details', $item->slug) }}">
                                            <span>
                                                <svg  xmlns="http://www.w3.org/2000/svg"  width="22"  height="22"  viewBox="0 0 24 24"  
                                                fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  
                                                class="icon icon-tabler icons-tabler-outline icon-tabler-file-description mb-1"><path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                                                <path d="M14 3v4a1 1 0 0 0 1 1h4" /><path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
                                                <path d="M9 17h6" /><path d="M9 13h6" /></svg>
                                            </span>
                                            <span>
                                                {{$item->title}}
                                            </span>
                                        </a>
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--======================= sitemap end ============================ -->
@endsection

@push('script')
@endpush

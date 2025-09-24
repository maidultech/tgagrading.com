@extends('frontend.layouts.app')

@section('title')
    {{ $title ?? 'Pricing' }}
@endsection

@section('meta')
    <meta property="og:title" content="{{ $seo->title ?? $og_title }}"/>
    <meta property="og:description" content="{{ $seo->description ?? $og_description }}"/>
    {{--  <meta property="og:image" content="{{ asset($setting->seo_image ?? $og_image) }}"/>  --}}
    <meta property="og:image" content="{{ $setting->seo_image ? asset($setting->seo_image) : asset($og_image) }}" />
    <meta name="description" content="{{$seo->meta_description ?? $og_description}}">
    <meta name="keywords" content="{{$seo->keywords ?? $meta_keywords}}">
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
{{--  @dd($og_image)  --}}
{{--  @dd($setting->seo_image)  --}}
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ __('messages.common.pricing') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <!-- ======================= pricing start  ============================ -->
    <div class="pricing_section py-5 bg-transparent">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h1>Our Pricing Plans</h1>
                <p>
                    Explore transparent pricing plans to elevate your card collection. Choose the perfect plan for
                    seamless card grading services
                </p>
            </div>
            <div class="row g-4">
                <x-fees-section :plans="$plans" />
            </div>
        </div>
    </div>
    <!-- ======================= pricing end  ============================ -->
@endsection

@push('script')
@endpush

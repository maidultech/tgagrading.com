@extends('frontend.layouts.app')

@section('title')
{{ $title ?? 'TGA Grading' }}
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
.section_heading h1 {
    font-weight: 800;
    color: #505050;
    font-size: 28px;
}
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">Faq</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!--======================= faq start ============================ -->
    <div class="faq_section pt-4 pb-5">
        <div class="container">
            <div class="section_heading text-center mb-5">
                <h1>Frequently Asked Questions</h1>
                {{-- <p>
                    "Got questions? Check out the answers to our most common inquiries below. If you need
                    further help, feel free to reach out!"
                </p> --}}
            </div>
            <div class="faq-body mb-4">
                <div class="col-xl-9 mx-auto">
                    <div class="accordion" id="accordionExample">
                        @foreach($faqs as $k => $faq)
                            <div class="accordion-item">
                            <h2 class="accordion-header p-0">
                                <button class="accordion-button {{ $k == 0 ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse{{ $faq->id }}" aria-expanded="{{ $k === 0  }}" aria-controls="collapse{{ $faq->id }}">
                                    {{ $faq->title }}
                                </button>
                            </h2>
                            <div id="collapse{{ $faq->id }}" class="accordion-collapse collapse {{ $k == 0 ? 'show' : '' }}"
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
    <!--======================= faq end ============================ -->
@endsection

@push('script')
@endpush

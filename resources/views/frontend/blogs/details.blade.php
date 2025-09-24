@extends('frontend.layouts.app')
@section('title')
{{ $title ?? 'Page header' }}
@endsection

@php
    $metaKeywords = '';

    if ($tags->isNotEmpty()) {
        $tagArray = [];
        foreach ($tags as $row) {
            $tagArray[] = $row;
        }
        $metaKeywords = implode(', ', $tagArray);
    } else {
        $metaKeywords = $meta_keywords;
    }
    $metaDescription = !empty($blog->details) 
        ? \Illuminate\Support\Str::limit(strip_tags($blog->details), 155, '...') 
        : $og_description;
@endphp

@section('meta')
<meta property="og:title" content="{{$og_title}}" />
<meta property="og:description" content="{{ $og_description ?? $metaDescription }}" />
<meta property="og:image" content="{{getPhoto($og_image)}}" />
<meta name="description" content="{{$og_description ??$metaDescription}}">
<meta name="keywords" content="{{$meta_keywords ?? $metaKeywords}}">
@endsection

@push('style')
<style>
.small_img img {
    width: 100px;
    height: 90px;
    border-radius: 10px;
    object-fit: contain;
    background: #f3f3f3;
}
</style>
@endpush
    @push('style')
<style>
.desc ul {
    list-style-type: disc;         /* default bullet style */
    padding-left: 2rem;          /* indentation from the left */
}

.desc ul li {
    margin-bottom: 0.5rem;         /* space between list items */
}

/* Nested UL inside UL for different bullet styles */
.desc ul ul {
    list-style-type: circle;       /* second level bullets */
    margin-top: 0.5rem;
}

.desc ul ul ul {
    list-style-type: square;       /* third level bullets */
    margin-top: 0.5rem;
}
</style>
@endpush


@section('schema')
    {!! $schema_markup !!}
@endsection
@section('content')
<!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
<li class="breadcrumb-item">{{$blog->title}}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->

 <!--======================= blog details start ============================ -->
 <div class="blog_details pt-3 pb-5">
    <div class="container">
        <div class="row gy-4 gy-lg-0">
            <div class="col-lg-8 order-xl-1">
                <div class="single_post blog_wrapper border p-3 p-xl-4 rounded">
                    <div class="single_photo mb-3 lazyload_img">
                        <img data-src="{{ getPhoto($blog->image) }}" src="{{ getPhoto($blog->image) }}" class="rounded w-100" alt="{{$blog->title}}"
                            style="max-height: 500px; object-fit: cover;">
                    </div>
                    <div class="short_info d-flex flex-wrap align-items-center mb-3">
                        <div class="mb-2 mb-xl-0 me-3">
                            <div class="d-flex align-items-center">
                                <div class="icon me-1">
                                    <img src="{{ asset('frontend/assets/images/tag.svg') }}" alt="Category">
                                </div>
                                <div class="date mt-1"><span><a class="text-black" href="{{route('frontend.blogs', ['category' => $blog->category->slug])}}">{{$blog->category->name}}</a></span></div>
                            </div>
                        </div>
                        <div class="mb-2 mb-xl-0 me-3">
                            <div class="d-flex align-items-center">
                                <div class="icon me-1">
                                    <img src="{{ asset('frontend/assets/images/calendar.svg') }}" alt="Date">
                                </div>
                                <div class="date mt-1"><span>{{ \Carbon\Carbon::parse($blog->created_at)->format('d M, Y') }}</span></div>
                            </div>
                        </div>
                        {{-- <div class="mb-2 mb-xl-0 me-3">
                            <div class="d-flex align-items-center">
                                <div class="icon me-1">
                                    <img src="https://enjoycitytours.com/assets/images/eye.svg" alt="View">
                                </div>
                                <div class="date"><span>1612</span></div>
                            </div>
                        </div>
                        <div class="mb-2 mb-xl-0">
                            <div class="d-flex align-items-center position-relative overflow-hidden">
                                <div class="icon me-1">
                                    <a href="#" class="stretched-link">
                                        <img src="https://enjoycitytours.com/assets/images/user.svg" alt="Author">
                                    </a>
                                </div>
                                <div class="author_name"><span></span></div>
                            </div>
                        </div> --}}
                    </div>
                    <div class="title mb-3">
                        <h1>{{$blog->title}}</h1>
                    </div>
                    <div class="desc">
                        <div class="px-2">
                            {!! $blog->details !!}
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-8">
                    <div class="details_content rounded bg-light border-0 p-3">                  
                        <div class="mb-4">
                            <h1 class="title">{{$blog->title}}</h3>
                                <span class="text-muted">{{date('F d, Y', strtotime($blog->created_at))}}</span>
                        </div>
                        <div class="content">
                            {!! $blog->details !!}
                        </div>
                    </div>
                </div> --}}
            </div>
            <div class="col-lg-4 order-xl-2">
                <div class="blog-sidebar sticky-top py-0" style="top:1rem">
                    <div class="blog-card py-0">
                        <div class="heading mb-3">
                            <h4>Recent Post</h4>
                        </div>
                        @foreach ($recentBlogs as $item)
                        <div
                            class="blog_list text-center bg-white mb-2 rounded p-2 p-xl-3 border border-light-subtle text-sm-start">
                            <div class="d-sm-flex align-items-center">
                                <div class="small_img mb-3 mb-sm-0 me-0 me-sm-2">
                                    <a href="{{route('frontend.blogs.details', $item->slug)}}">
                                        <img src="{{getBlogPhoto($item->image)}}" class="img-fluid rounded"
                                            alt="Understanding Financial Planning">
                                    </a>
                                </div>
                                <div class="content">
                                    <h4><a class="post-name" href="{{route('frontend.blogs.details', $item->slug)}}">{{Str::limit($item->title, 25) }}</a></h4>
                                    <span>{{date('F d, Y', strtotime($item->created_at))}}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
 </div>
<!--======================= blog details end ============================ -->


@endsection
@push('style')
@endpush

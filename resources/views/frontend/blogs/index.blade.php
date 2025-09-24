@extends('frontend.layouts.app')
@section('title')
    {{ $title }}
@endsection

@section('meta')
    <meta property="og:title" content="" />
    <meta property="og:description" content="" />
    <meta property="og:image" content="" />
@endsection

@push('style')
    <style>
        .active>.page-link,
        .page-link.active {
            z-index: 3;
            color: #fff !important;
            background-color: var(--primary) !important;
        }
        .styled-list {
            list-style: none;
            padding-left: 0;
        }
        .styled-list li {
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 5px;
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .tag-list .tags {
            padding: 5px 10px;
            margin-bottom: 5px;
            margin-right: 10px;
            border-radius: 5px;
            background-color: #c4c6eb;
            color: #ffffff;
            transition: background-color 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
    </style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">Blogs</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->

<!-- ======================= blog start  ============================ -->
{{-- <div class="blog_sction pt-4 pb-5">
    <div class="container">
        <div class="row gy-4 gy-lg-0">
            <div class="col-lg-8">
                <!-- Blog Post 1 -->
                @foreach ($blogs as $blog)
                    <div class="d-md-flex blog-post mb-3">
                        <div class="mb-3 mb-md-0 me-0 me-sm-3">
                            <a href="{{ route('frontend.blogs.details', $blog->slug) }}">
                                <img src="{{ asset($blog->image) }}" class=" rounded" alt="{{ $blog->title }}" />
                            </a>
                        </div>

                        <div class="blog-card p-2">
                            <h5>
                                <a href="{{ route('frontend.blogs.details', $blog->slug) }}" class="blog-title">{{ $blog->title }}</a>
                            </h5>
                            <span class="blog-date">{{ \Carbon\Carbon::parse($blog->created_at)->format('F d, Y') }}</span>
                            <p class="blog-description">
                                {{ $blog->details }}
                            </p>
                            <a href="{{ route('frontend.blogs.details', $blog->slug) }}" class="learn-more">
                             Learn More    <i class="fas fa-arrow-right"></i>
                            </a>
                            </div>
                    </div>
                @endforeach
                <div class="d-flex justify-content-center align-items-center mt-5 custom-pagination">
                    {{ $blogs->links('pagination::bootstrap-4') }}
                </div>








            </div>


            <!-- Sidebar Section -->
            <div class="col-lg-4">
                <div class="blog-sidebar bg-light sticky-top rounded bg-white border-0 custom-sidebar">
                    <div class="">
                        <div class="mb-3 sidebar-heading">Categories</div>
                        <ul class="list-unstyled category-list">
                            <li class="category-item">
                                <a href="{{ route('frontend.blogs') }}" class="category-link {{ request()->is('blogs') ? 'active' : '' }}">All Categories</a>
                            </li>
                            @foreach ($categories as $category)
                                <li class="category-item">
                                    <a href="{{ route('frontend.blogs.category', $category->slug) }}" class="category-link {{ request()->is('blogs/category/' . $category->slug) ? 'active' : '' }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div> --}}

<!--======================= blog start ============================ -->
<div class="blog_sction pb-5">
    <div class="container">
        <div class="row gy-4 gy-lg-0">
            <div class="col-lg-8">
                <div class="blog_wrapper">
                    <div class="row gy-4">
                        @forelse ($blogs as $blog)
                            <div class="col-md-6">
                                <div class="blog_post p-3 p-lg-4 card h-100 bg-transparent shadow-sm border-opacity-10">
                                    <div class="blog_img mb-4 position-relative">
                                        <a class="lazyload_img" href="{{route('frontend.blogs.details', $blog->slug)}}">
                                            <img class="img-fluid rounded z-3" style="height: 280px; width: 100%;" height="280" width="100%" src="{{ getPhoto($blog->image) }}"
                                                alt="{{$blog->title}}">
                                        </a>
                                    </div>
                                    <div class="blog_content card-body p-0">
                                        <div class="short_info d-sm-flex d-md-block d-lg-flex align-items-center mb-3">
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
                                                            <img src="{{ asset('assets/images/eye.svg') }}" alt="View">
                                                        </div>
                                                        <div class="date"><span>{{$row->view}}</span></div>
                                                    </div>
                                                </div> --}}
                                            {{-- <div class="mb-2 mb-xl-0">
                                                <div class="d-flex align-items-center position-relative overflow-hidden">
                                                    <div class="icon me-1">
                                                        <a href="{{ route('frontend.author.blog', $row->author_slug) }}" class="stretched-link">
                                                            <img src="{{ asset('assets/images/user.svg') }}" alt="Author">
                                                        </a>
                                                    </div>
                                                    <div class="author_name"><span>{{ $row->author->name ?? '' }}</span></div>
                                                </div>
                                            </div> --}}
                                        </div>
                                        <h3 class="mb-3">
                                            <a href="{{route('frontend.blogs.details', $blog->slug)}}">{{$blog->title}}</a>
                                        </h3>
                                        <div class="blog_desc mb-2">
                                            {!! \Illuminate\Support\Str::limit(strip_tags($blog->details), 100, '...') !!}
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="card-footer mt-2 bg-transparent border-0 blog_content p-0">
                                        <a class="learn_more" rel="nofollow" href="{{route('frontend.blogs.details', $blog->slug)}}">Read More</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                        <div class="col-12 pt-4">
                           <strong class="text-danger" style="padding: 20px;">No News found.....</strong>
                        </div>
                        @endforelse
                    </div>

                    <!-- pagination -->
                    @if($blogs->isNotEmpty())
                    <div class="pagination_nav mt-5 d-flex justify-content-center">
                        {{ $blogs->links() }}
                    </div>
                    @endif
                </div>
                {{-- <div class="row">
                    <!-- Blog Post 1 -->
                    @foreach ($blogs as $blog)
                        <div class="col-sm-6 col-lg-6 mb-3 col-xl-6">
                            <div class="blog-post h-100 card border-0 position-relative">
                                <div class="blog_img mb-3">
                                    <a href="">
                                        <img src="{{ getBlogPhoto($blog->image) }}" class="w-100 rounded-2"
                                            alt="blog-post-1" />
                                    </a>
                                </div>
                                <div>
                                    <h5><a href="">{{ Str::limit($blog->title, 50) }}</a></h5>
                                    <span class="text-muted">{{ date('F d, Y', strtotime($blog->created_at)) }}</span>
                                    <p>
                                        {{ substr(strip_tags($blog->details), 0, 200) }}{{ strlen($blog->details) > 200 ? '...' : '' }}
                                    </p>

                                </div>
                                <div class="mt-auto">
                                    <a href="{{ route('frontend.blogs.details', $blog->slug) }}"
                                        class="text-primary stretched-link">Learn More →</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- pagination -->
                <div class="pagination_nav mt-5 d-flex justify-content-center">
                    {{ $blogs->links() }}
                </div> --}}
            </div>

            <!-- Sidebar Section -->
            <div class="col-lg-4">
                <div class="blog-sidebar sticky-top" style="top:1rem">
                    {{-- <div class="blog-card">
                        <div class="heading mb-3">
                            <h4>Recent Blog</h4>
                        </div>
                        @foreach ($recentBlogs as $item)
                            <div
                                class="blog_list text-center bg-white mb-2 rounded p-2 p-xl-3 border border-light-subtle text-sm-start">
                                <div class="d-sm-flex align-items-center">
                                    <div class="small_img mb-3 mb-sm-0 me-0 me-sm-2">
                                        <a href="{{ route('frontend.blogs.details', $item->slug) }}">
                                            <img src="{{ getBlogPhoto($item->image) }}" width="100"
                                                class="img-fluid rounded" alt="Understanding Financial Planning">
                                        </a>
                                    </div>
                                    <div class="content">
                                        <h4><a class="post-name"
                                                href="{{ route('frontend.blogs.details', $item->slug) }}">{{ Str::limit($item->title, 25) }}</a>
                                        </h4>
                                        <span>{{ date('F d, Y', strtotime($item->created_at)) }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div> --}}
                    @if($categories->isNotEmpty())
                        <div class="card mb-3">
                            <div class="heading card-header">
                                <h1 style="font-size: 20px;">Categories</h1>
                            </div>
                            <div class="sidebar_content card-body">
                                <div class="category_list">
                                    <ul class="styled-list">
                                        @foreach ($categories as $row)
                                            <a style="color:black;" href="{{route('frontend.blogs', ['category' => $row->slug])}}" class="text-decoration-none">
                                                <li>{{$row->name}}</li>
                                            </a>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($tags->isNotEmpty())
                    <div class="card mt-3">
                        <div class="heading card-header">
                            <h5>Tags</h5>
                        </div>
                        <div class="sidebar_content card-body">
                            <div class="tag_list">
                                <div class="tag-list d-flex flex-wrap">
                                    @foreach ($tags as $row)
                                        <a style="color:black;" href="{{ route('frontend.blogs', ['tag' => $row]) }}" class="text-decoration-none">
                                            <div class="tags">{{ $row }}</div>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!--======================= blog end ============================ -->
@endsection
@push('style')
@endpush



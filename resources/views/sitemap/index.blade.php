<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <url>
        <loc>{{ route('frontend.index') }}</loc>
        <changefreq>always</changefreq>
    </url>
    <url>
        <loc>{{ route('frontend.about') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.privacy') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.terms') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.contact') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.faq') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.pricing') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.grading-scale') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.sitemap') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.howToOrder') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.certification') }}</loc>
    </url>
    <url>
        <loc>{{ route('frontend.blogs') }}</loc>
    </url>

    {{-- @if (!empty($bcategories))
        @foreach($bcategories as $category)
            <url>
                <loc>{{ route('frontend.blogs.category', $category->slug) }}</loc>
            </url>
        @endforeach
    @endif --}}

    @if (!empty($blogs))
        @foreach($blogs as $item)
            <url>
                <loc>{{route('frontend.blogs.details', $item->slug)}}</loc>
            </url>
        @endforeach
    @endif

    {{-- <url>
        <loc>{{ route('login') }}</loc>
    </url>
    <url>
        <loc>{{ route('registration') }}</loc>
    </url> --}}
</urlset>

<meta property="og:title" content="{{ $row->meta_title ?? $og_title }}" />
<meta property="og:description" content="{{ $row->meta_description ?? $og_description }}" />
<meta property="og:image" content="{{ asset($setting->seo_image ?? $og_image) }}" />
<meta name="description" content="{{$row->meta_description ?? $og_description}}">
<meta name="keywords" content="{{$row->meta_keywords ?? $meta_keywords}}">

@extends('frontend.layouts.app')

@section('title')
{{ $title }}
@endsection

@section('meta')
    @include('frontend.layouts._meta')
@endsection

@push('style')
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{$title}}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!--======================= about start ============================ -->
    <div class="custom_page about_page pb-4">
        <div class="container">
            <div class="custom_content">
                <p class="mb-4">
                    {!! $row->body ?? 'About US' !!}
                </p>

            </div>
        </div>
    </div>
    <!--======================= about end ============================ -->

@endsection

@push('script')
@endpush

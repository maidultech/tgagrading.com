@extends('frontend.layouts.app')

@section('title')
    {{ $row->title ?? 'Privacy Policy' }}
@endsection

@section('meta')
    @include('frontend.layouts._meta')
@endsection
@push('style')
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{ $row->title ?? 'Privacy Policy' }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!--======================= privacy policy start ============================ -->
    <div class="custom_page  pb-4">
        <div class="container">
            <div class="custom_content">
                {!! $row->body ?? 'Privacy Policy' !!}
            </div>
        </div>
    </div>
    <!--======================= privacy policy end ============================ -->
@endsection

@push('script')
@endpush

@extends('frontend.layouts.app')

@section('title')
    {{ $row->title ?? 'Terms & Conditions' }}
@endsection

@section('meta')
    @include('frontend.layouts._meta')
@endsection
@push('style')
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item"> {{ $row->title ?? 'Terms & Conditions' }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!--======================= terms & conditon start ============================ -->
    <div class="custom_page pb-4">
        <div class="container">
            <div class="custom_content">
                {!! $row->body ?? 'Terms & Conditions' !!}
            </div>
        </div>
    </div>
    <!--======================= terms & conditon end ============================ -->
@endsection

@push('script')
@endpush

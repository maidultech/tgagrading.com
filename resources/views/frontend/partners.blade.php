@extends('frontend.layouts.app')

@section('title')
    {{ 'Partners' }}
@endsection

@section('meta')
    @include('frontend.layouts._meta')
@endsection
@push('style')
<style>
    .partnerHeader{
        padding: 40px; 
    }
    .partnerImage {
	height: 300px;
	width: 100%;
	object-fit: contain;
	object-position: center;
}
</style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
        <li class="breadcrumb-item">{{  'Partners' }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    

     <!-- Top Header -->
    {{--  <div class="container-fluid py-5 text-center">
        <h1 class="display-4">Top Header</h1>
    </div>  --}}

    <!-- Partner Banner Section -->
<section class="text-center text-white d-flex align-items-center justify-content-center mb-2" style="
    min-height: 350px;
    background: url('https://cdn.pixabay.com/photo/2020/06/03/15/54/banner-5255420_640.jpg') center center / cover no-repeat;
    position: relative;
    z-index: 1;">
    <div class="container position-relative z-2">
        <h1 class="display-4 fw-bold">All Partners</h1>
    </div>
    <div style="
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background-color: rgba(0,0,0,0.5);">
    </div>
</section>

    <!-- Intro Text -->
    <div class="container mb-4">
        <div class="alert alert-light text-center fs-5">
            We are proud to be partnered with the following amazing people
        </div>
    </div>

    <!-- Partners Grid -->
    <div class="container pb-5">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">

            <!-- Partner Card Example -->
            @foreach ($rows as $row)
            <div class="col">
                <div class="card h-100 text-center">
                    <a href="{{ $row->company_url }}" target="_blank">
                        <img class="partnerImage card-img-top p-3" src="{{ asset($row->logo) }}" alt="{{ $row->company_name }}">
                    </a>
                    <div class="card-body">
                        <h5 class="card-title">{{ $row->company_name }}</h5>
                        <p class="card-text">{!! $row->details !!}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>


   
@endsection

@push('script')
@endpush

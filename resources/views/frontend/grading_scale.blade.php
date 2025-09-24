@extends('frontend.layouts.app')

@section('title')
    {{ $row->title ?? 'Privacy Policy' }}
@endsection

@section('meta')
@include('frontend.layouts._meta')
@endsection
@push('style')
    <style>
        .grading-main .bor {
            background: #f8f8f8;
            border-color: #ccc;
            color: #2f3028;
        }

        .grading-main .bor {
            border: 1px solid #ccc;
            padding: 0;
        }

        .grading-main .bor {
            border: 1px solid #ccc;
        }

        .grading-main .bor {
            padding: 20px 10px;
        }

        .grading_scale {
            background: #505050 !important;
            border: 1px solid #505050 !important;
        }

        .bor h6 {
            padding: 28px 0px;
            line-height: 20px;
            margin: 0px;
        }

        .bor h6 {
            color: #fff;
            font-size: 19px;
            text-align: center;
            border-bottom: 0 solid #ccc;
            border-right: 0 solid #ccc;
            border-left: 0 solid #ccc;
            border-top: 0 solid #ccc;
            padding: 4px 0;
            font-family: oswald;
        }

        @media screen and (min-width: 993px) {
            body .our-representative {
                min-height: 300px;
            }
        }

        .our-representative {
            background: #fff;
            border-bottom: 1px solid #ccc;
            border-right: 1px solid #ccc;
            border-left: 1px solid #ccc;
            border-top: 0 solid #ccc;
            height: 350px;
        }

        .p-10 {
            padding: 10px;
        }

        .mt-15 {
            margin-top: 15px !important;
        }
    </style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
    <li class="breadcrumb-item">{{ $row->title ?? 'Grading Scale' }}</li>
@endsection
<!-- ======================= breadcrumb end  ============================ -->
<!--======================= privacy policy start ============================ -->
<div class="custom_page  pb-4">
    <div class="container">
        <div class="custom_content">
            <div class="row">
                <p class="my-3">
                    <strong>
                        {{-- {{ $row->meta_title }} --}}
                    </strong>
                </p>
            </div>
            <div class="row justify-content-center">
                <h1 style="font-size: 22px; text-align:center; text-transform: capitalize;"><b>Grading Scale</b></h1>
            </div>
            <div class="row">
                @if($rows && count($rows)>0) 
                @foreach($rows as $key => $item)
                <div class="col-lg-4 col-md-6 col-12 mt-15 mb-3 h-100">
                    <div class="bor text-center p-10 grading_scale">
                        <h6>{{ $item->title }}</h6>
                    </div>
                    <div class="our-representative shadow-sm p-10">
                        {!! nl2br($item->body) !!}
                    </div>
                </div>
                @endforeach
                @endif 
            </div>
            <div class="row">
                <p class="my-3">
                    {!! $row->body !!}
                </p>
            </div>
        </div>
    </div>
</div>
<!--======================= privacy policy end ============================ -->
@endsection

@push('script')
@endpush

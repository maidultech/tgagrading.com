@extends('frontend.layouts.app')

@section('title')
    {{ 'Certificate Verification' }}
@endsection

@section('meta')
    @include('frontend.layouts._meta')
@endsection
@push('style')
<style>
    .cert-number {
        color: #000000;
    }
    .certification-box {
        border: 5px solid #000000;
        background: #f0f0f0;
    }
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
        <li class="breadcrumb-item">{{  'Certificate Verification' }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <div class="pt-5 mb-5">
        <div class="container">
            <div class="section_heading text-center">
                <div class="row d-flex justify-content-center mb-4">
                    <div class="col-xl-6">
                        <h1>Cert Verification</h1>
                        <p>
                            Verify the validity of TGA certification numbers using the search box below. Always
                            confirm certification numbers for collectibles purchased online.
                        </p>
                    </div>
                </div>
                <div class="row d-flex justify-content-center">
                    <div class="col-md-6 col-lg-5 col-xl-4">
                        <form action="{{ route('frontend.certification') }}"  method="get">
                            <div class="mb-4 input-group">
                                <input type="number" name="number" class="form-control rounded-3 py-3 px-3 rounded-end-0"
                                       placeholder="TGA Cert Number" value="{{ $number ?? '' }}"  required="">
                                <button type="submit"
                                        class="input-group-text btn btn-primary py-3 px-4 rounded-start-0 rounded-3">
                                    Verify
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--======================= certification start ============================ -->
    <div class="certification_section pt-4 pb-5">
        <div class="container">
            @if($number && $certificate)
                <div class="text-center">
                    <p>According to the TGA database, the requested certification number is defined as the following:</p>
                </div>

                <!-- TGA Certificate Verification -->
                <div class="row d-flex align-items-center mb-4 justify-content-center">
                    <div class="col-md-7 col-lg-6 col-xl-5">
                        <div class="certification-box pb-3">
                            <h2>{{ 'TGA Certificate Verification' }}</h2>
                            <div class="cert-number">#{{ $certificate->card_number ?? '-' }}</div>
                            <img width="70" src="{{ getLogo($setting->site_logo) }}" alt="{{ $setting->site_name }}">
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center gy-4 gy-md-0">
                    <!-- Photo Section -->
                    {{-- <div class="col-md-4">
                        <div class="photo-placeholder py-5 card rounded-4 border border-light">
                            <img src="{{ asset('/frontend/assets/images/banner.png') }}" class="img-fluid" alt="">
                        </div>
                    </div> --}}

                    <!-- Information Section -->
                    <div class="col-md-8 mb-4">
                        <div class="orders_table card table-responsive">
                            <table class="table m-0 table-striped item-info">
                                <tbody>
                                <tr>
                                    <th class="rounded-0">Certification Number</th>
                                    <td>{{ $certificate->card_number ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th class="rounded-0">Year</th>
                                    <td>@isset($isManual)
                                        {{ $certificate->year ?? '-' }}
                                        @else
                                        {{ $certificate->year ?? '-' }}
                                        {{-- {{ $certificate->year ? $certificate->year : ($certificate->details->year ?? '-') }} --}}
                                    @endisset</td>
                                </tr>
                                <tr>
                                    <th class="rounded-0">Brand</th>
                                    <td class="text-uppercase">@isset($isManual)
                                        {{ $certificate->brand_name ?? '-' }}
                                        @else
                                        {{ $certificate->brand_name ?? '-' }}
                                        {{-- {{ $certificate->brand_name ? $certificate->brand_name : ($certificate->details->brand_name ?? '-') }} --}}
                                    @endisset</td>
                                </tr>
                                {{-- <tr>
                                    <th class="rounded-0">Sport</th>
                                    <td>@isset($isManual)
                                        {{ $certificate->sport ?? '-' }}
                                        @else
                                        {{ $certificate->sport ?? '-' }}
                                    @endisset</td>
                                </tr> --}}
                                <tr>
                                    <th class="rounded-0">Card Number</th>
                                    <td>@isset($isManual)
                                        {{ $certificate->card ?? '-' }}
                                        @else
                                        {{ $certificate->card ?? '-' }}
                                        {{-- {{ $certificate->card ? $certificate->card : ($certificate->details->card ?? '-') }} --}}
                                    @endisset</td>
                                </tr>
                                <tr>
                                    <th class="rounded-0">Player</th>
                                    <td class="text-uppercase">@isset($isManual)
                                        {{ $certificate->card_name ?? '-' }}
                                        @else
                                        {{ $certificate->card_name ?? '-' }}
                                        {{-- {{ $certificate->card_name ? $certificate->card_name : ($certificate->details->card_name ?? '-') }} --}}
                                    @endisset</td>
                                </tr>
                                {{-- <tr>
                                    <th class="rounded-0">Variety/Pedigree</th>
                                    <td>@isset($isManual)
                                        {{ $certificate->variety ?? '-' }}
                                        @else
                                        {{ $certificate->variety ?? '-' }}
                                    @endisset</td>
                                </tr> --}}
                                <tr>
                                    <th class="rounded-0">Grade</th>
                                    <td>@isset($isManual)
                                        {{ $certificate->grade ?? '-' }}
                                        @else
                                        {{ $certificate->final_grading ?? '-' }}
                                    @endisset</td>
                                </tr>
                                @if($pop_count > 0)
                                <tr>
                                    <th class="rounded-0">TGA Pop Count</th>
                                    <td>
                                        {{ $pop_count }}
                                    </td>
                                </tr>
                                @endif
                                @if($pop_higher_count > 0)
                                <tr>
                                    <th class="rounded-0">TGA Pop Higher</th>
                                    <td>
                                        {{ $pop_higher_count }}
                                    </td>
                                </tr>
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row justify-content-center align-items-md-center text-center">
                            @if($certificate->front_page)
                                <div class="col-md-6">
                                    <a href="{{ asset($certificate->front_page) }}" target="_blank">
                                        <img src="{{ asset($certificate->front_page) }}" class="img-fluid" alt="Front">
                                    </a>
                                    <h6 class="mt-3">Front</h6>
                                </div>
                            @endif

                            @if($certificate->back_page)
                                <div class="col-md-6">
                                    <a href="{{ asset($certificate->back_page) }}" target="_blank">
                                        <img src="{{ asset($certificate->back_page) }}" class="img-fluid" alt="Back">
                                    </a>
                                    <h6 class="mt-3">Back</h6>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @elseif($number)
                <div class="certification-box text-center">
                    <p class="m-0">The certification number you entered is not valid. Please try again.</p>
                </div>
            @else

            @endif

        </div>
    </div>
    <!--======================= certification end ============================ -->
@endsection

@push('script')
@endpush

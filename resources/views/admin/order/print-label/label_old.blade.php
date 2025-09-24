
@php
    $logo_main = public_path($setting->site_logo);
    $logo = public_path('/assets/logo_pdf.png');
    $logo_bottom = public_path('/assets/logo_pdf.png');
    $ext = pathinfo($logo)['extension'];
    $transLogo = str($logo)->swap([
            ".$ext" => "_opacity.$ext",
        ])->value();
    if(!File::exists($transLogo)){
        $transLogo = $logo;
    }
    $top_grade = false;
@endphp
<style>

</style>
<style>

    /* {!! file_get_contents(public_path('assets/adminlte/css/adminlte_pdf.min.css')) !!}  */

    @font-face {
        font-family: 'microgramma';
        src: url('data:font/ttf;base64,{{ base64_encode(file_get_contents(public_path('fonts/microgrammanormal.ttf'))) }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }

    :root {
        --primary: #6453f7;
    }

    /* bootstrap */
    .align-items-center {
        -ms-flex-align: center !important;
        align-items: center !important;
    }

    .row {
        /* display: -ms-flexbox; */
        display: flex;
        -ms-flex-wrap: wrap;
        flex-wrap: wrap;
        margin-right: -7.5px;
        margin-left: -7.5px;
        display: -webkit-box;

    }

    .col-4 {
        -ms-flex: 0 0 33.333333%;
        flex: 0 0 33.333333%;
        max-width: 33.333333%;
    }
    .col-sm-7 {
        -ms-flex: 0 0 56.333333%;
        flex: 0 0 56.333333%;
        max-width: 56.333333%;
    }
    .text-center {
        text-align: center !important;
    }

    .mt-4,
    .my-4 {
        margin-top: 1.5rem !important;
    }

    .col-6 {
        -ms-flex: 0 0 50%;
        flex: 0 0 50%;
        max-width: 50%;
    }

    .mb-4,
    .my-4 {
        margin-bottom: 1.5rem !important;
    }
    .mb-3 {
        margin-bottom: 1rem !important;
    }
    .justify-content-between {
        -ms-flex-pack: justify !important;
        justify-content: space-between !important;
    }

    .d-flex {
        display: -ms-flexbox !important;
        display: flex !important;
    }

    .text-left {
        text-align: left !important;
    }

    .col-2 {
        -ms-flex: 0 0 16.666667%;
        flex: 0 0 16.666667%;
        max-width: 16.666667%;
    }

    .text-right {
        text-align: right !important;
    }

    .back_page_text {
        font-family: 'microgramma';
        font-size: 15px;
        font-weight: 900;
        color: #034ea2;
        position: relative;
        top: -3px;
    }
    .back_page_bottom_text {
        font-size: 8px;
        font-weight: 700;
        position: relative;
        font-family: sans-serif;
        top: -3px;
    }
    /* end bootstrap */

    .mini_card {
        border: 5px solid #034EA2;
        clear: both;
        width: 100% !important;
        height: 100px;
        overflow: visible !important;
        padding: 0;
        margin: 0;
    }

    .bottom_card {
        left: -5px;
    }


            /* {{-- @dd($logo) --}} */

    .watermark_logo:before {
        position: absolute;
        content: "";
        background-image: url('data:{{ mime_content_type($transLogo) }};base64,{{ base64_encode(file_get_contents($transLogo)) }}');
        background-position: center;
        background-size: contain;
        background-repeat: no-repeat;
        width: 100%;
        height: 90px;
        opacity: 0.1;
        border: none;
        fill-opacity: 0.1;
        display: block;
        /* background-color: rgba(255, 255, 255, 0.9); Adjust transparency with alpha value */

        top: -10%;
        /* transform: translate(-50%, -50%); */
        z-index: 1;
        /* Ensure it stays behind other content */
    }


    .card_title {
        font-size: 10px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
    }

    .card_title_bottom {
        font-size: 9px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        position: relative;
        top: 3px;
    }

    .card_info {
        font-size: 8px;
        color: var(--primary);
        text-transform: uppercase;
        font-weight: 600;
    }

    .card_info .s_title-f {
        width: 30px;
        text-align: left;
        display: inline-block;
    }

    .card_info .s_title-l {
        width: 40px;
        text-align: left;
        display: inline-block;
    }

    .gem_num {
        font-weight: 900;
        color: var(--primary);
        line-height: 1px;
        text-align: center;
        height: 30px;
        position: relative;
        top: 35px;
    }
    .logo_qr {
        text-align: center;
        height: 70px;
        position: relative;
        top: 16px;
        right: 12px;
    }
    .gem_mint {
        color: var(--primary);
        font-weight: 800;
        text-align: center;
        position: relative;
        top: 30px;
        left: 12px;
        font-size: 8px;
    }

    .mini_card .number {
        font-size: 8px;
        font-weight: 600;
        top: 20px;
        position: relative;
    }

    .auth_title {
        font-size: 20px;
        font-weight: 600;
    }

    .grade_title {
        font-size: 37px;
        font-weight: 700;
        color: var(--primary);
    }

    .site_name {
        font-size: 18px;
        font-weight: 500;
        color: var(--primary);
    }

    .tga_logo {
        position: absolute;
        left: 38%;
        text-align: center;
        bottom: -5px;
        z-index: 999;
        min-width: 75px;
        max-width: 75px;
    }

    .page-break {
        page-break-after: always;
    }
</style>
@foreach($certs as $cert)
@php
    if($cert->final_grading >= 10) //
    {
        $logo_main = public_path('/assets/logo_pdf_2.png');
        $logo = public_path('/assets/logo_pdf_2.png');
        $logo_bottom = public_path('/assets/logo_pdf_bottom_2.png');
        $top_grade = true;

        $ext = pathinfo($logo)['extension'];
        $transLogo = str($logo)->swap([
                ".$ext" => "_opacity.$ext",
            ])->value();
        if(!File::exists($transLogo)){
            $transLogo = $logo;
        }
    } else {
        $logo_main = public_path('/assets/logo_pdf.png');
        $logo = public_path('/assets/logo_pdf.png');
        $logo_bottom = public_path('/assets/logo_pdf_bottom.png');
        $top_grade = false;

        $ext = pathinfo($logo)['extension'];
        $transLogo = str($logo)->swap([
                ".$ext" => "_opacity.$ext",
            ])->value();
        if(!File::exists($transLogo)){
            $transLogo = $logo;
        }
    }
@endphp

    <style>
        .watermark_logo:before {
            background-image: url('data:{{ mime_content_type($transLogo) }};base64,{{ base64_encode(file_get_contents($transLogo)) }}');
    }
    </style>
    <div class="mini_card position-relative overflow-hidden w-100" @if($top_grade == true) style="" @endif>
        <div class="row align-items-center justify-content-center w-100 d-flex"
             style="align-items: center; display: flex;vertical-align: middle; margin-top: 0;">
            <div class="col-sm-3">
                <div class="text-center" style="padding-top: 15px; position: relative; top: 15px; left: 5px">
                    <img src="data:{{ mime_content_type($logo_main) }};base64,{{ base64_encode(file_get_contents($logo_main)) }}"
                         class="w-100" alt="TGA-Grading">
                    <div class="number mt-0">7</div>
                </div>
            </div>
            <div class="col-sm-7">
                <div class="watermark_logo position-relative" style="top: 5px; left: -5px">
                    <div class="mb-2 card_title" @if($top_grade == true) style="color: #000000;" @endif>
                        {{ $cert->details->year }} {{ $cert->details->brand_name }} <br>
                        {{ $cert->details->card_name }} #{{ $cert->details->card }}
                        @if($cert->details->notes) <br> {{ $cert->details->notes }} @endif
                    </div>
                    <div class="">
                        <div class="card_info" style="width:55%;float:left; @if($top_grade == true) color: #000000; @endif">
                            <div>
                                <span class="s_title-f">SURFACE</span>
                                <span class="pl-2">{{ $cert->surface }}</span>
                            </div>
                            <div>
                                <span class="s_title-f">CORNERS</span>
                                <span class="pl-2">{{ $cert->corners }}</span>
                            </div>
                        </div>
                        <div class="card_info" style="width:auto;float:right; position: relative; left: 15px;  @if($top_grade == true) color: #000000; @endif">
                            <div>
                                <span class="s_title-l">CENTERING</span>
                                <span class="pl-2">{{ $cert->centering }}</span>
                            </div>
                            <div>
                                <span class="s_title-l">EDGES</span>
                                <span class="pl-2">{{ $cert->edges }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div style="text-align: right !important;">
                    <div class="gem_num"
                         style="
                            @if(is_numeric($cert->final_grading) && strpos($cert->final_grading, '.') !== false)
                                font-size: 32px; left: 8px;
                            @elseif(strlen((string)$cert->final_grading) == 2)
                                font-size: 42px; left: 5px;
                            @elseif(strlen((string)$cert->final_grading) == 1)
                                font-size: 42px; left: 12px;
                            @endif
                            @if($top_grade == true)
                                color: #000000;
                            @endif
                        ">
                        {{ $cert->final_grading }}
                    </div>

                    <div class="gem_mint text-uppercase"
                         style="
                                @if(strlen($cert->finalGrade->name ?? '') > 5 && strlen($cert->finalGrade->name ?? '') <= 10)
                                    line-height: 12px;
                                @elseif(strlen($cert->finalGrade->name ?? '') > 10 && strlen($cert->finalGrade->name ?? '') <= 13)
                                    font-size: 8px; line-height: 10px;
                                @elseif(strlen($cert->finalGrade->name ?? '') > 13)
                                    font-size: 8px; line-height: 8px;
                                @endif
                                @if($top_grade == true)
                                    color: #000000;
                                @endif
                            ">
                        {{ $cert->finalGrade->name ?? '' }}
                    </div>
                </div>
            </div>
        </div>
        <div class="tga_logo position-absolute">
            <div style="">
                <img src="data:{{ mime_content_type($logo_bottom) }};base64,{{ base64_encode(file_get_contents($logo_bottom)) }}"
                     class="w-50" alt="TGA-Grading">
            </div>
        </div>
    </div>
    <div class="page-break"></div>
    <div class="mini_card position-relative overflow-hidden" @if($top_grade == true) style="border: 5px solid #000000; background: #dbdbdb;" @endif>
        <div class="row align-items-center justify-content-center w-100 d-flex"
             style="align-items: center; display: flex;vertical-align: middle; margin-top: 0px;">

            <div class="col-sm-3">
                <div class="text-center" style="padding-top: 0; position: relative; top: 30px; left: 5px">
                    <img src="data:{{ mime_content_type($logo_main) }};base64,{{ base64_encode(file_get_contents($logo_main)) }}" alt="TGA-Grading" style="width: 110%">
                </div>
            </div>
            <div class="col-sm-7">
                <div class="watermark_logo bottom_card position-relative">
                    <div class="mb-4 card_title_bottom text-center mt-1" @if($top_grade == true) style="color: #000000;" @endif>
                        TRUE GRADE AUTHENTICATION
                    </div>
                    <div class="text-center back_page_text mt-0 mb-3"  @if($top_grade == true) style="color: #000000 !important;" @endif>
                        ITíS IN THE GRADE
                    </div>
                    <div class="text-center back_page_bottom_text">
                       <a href="{{route('frontend.index')}}" @if($top_grade == true) style="color: #000000 !important;" @else style="color: #ec1d25 !important;" @endif>www.tgagrading.com</a>
                    </div>
                </div>
            </div>
            <div class="col-sm-2">
                <div class="" style="text-align: right !important;">
                    <div class="logo_qr" @if($top_grade == true)
                        style="color: #000000;
                        text-align: center;
                        height: 70px;
                        position: relative;
                        top: 16px;
                        right: 12px;" @endif>
                        {!! QrCode::size(60)->style('round')->backgroundColor(255, 255, 255, 0)->generate(route('frontend.index')) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

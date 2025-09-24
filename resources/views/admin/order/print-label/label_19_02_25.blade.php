<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Black Label</title>
    
    @php
        $logo_main = public_path($setting->site_logo);
        $logo = public_path('/assets/logo_pdf.png');
        $logo_bottom = public_path('/assets/logo_pdf.png');
        $ext = pathinfo($logo)['extension'];
        $transLogo = str($logo)
            ->swap([
                ".$ext" => "_opacity.$ext",
            ])
            ->value();
        if (!File::exists($transLogo)) {
            $transLogo = $logo;
        }
        $top_grade = false;
    @endphp

    <style>
        @font-face {
            font-family: microgramma;
            src: url('data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path("fonts/microgrammanormal.ttf"))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: arialMedium;
            src: url('data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path("fonts/ArialMedium.ttf"))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: microgrammaBold;
            src: url('data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path("fonts/Microgramma D Extended Bold.otf"))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .commonFont{
            font-family: arialBold !important;
        }

         /* {!! file_get_contents(public_path('assets/adminlte/css/adminlte_pdf.min.css')) !!}  */
        .card_title,.number{
            /* font-family: arialMedium !important; */
            font-family: sans-serif !important;
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

        body, html{
            background-color: #000000
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
            font-family: microgrammaBold;
            font-size: 10px;
            font-weight: 900;
            color: #ffffff;
            position: relative;
            top: -3px;
        }

        .back_page_bottom_text {
            font-size: 8px;
            font-weight: 700;
            position: relative;
            /* font-family: sans-serif; */
            top: -3px;
        }

        /* end bootstrap */

        .mini_card {
            border: 5px solid #000000;
            clear: both;
            /* width: 100% !important; */
            /* height: 100px; */
            overflow: visible !important;
            padding: 0;
            margin: 0;
            background: #000000;
            height: 80px;
            width: 282px
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
            /* font-weight: 700; */
            font-weight: bolder;
            color: #ffffff;
            text-transform: uppercase;
            /* font-family: arialMedium !important; */
            font-family: system-ui;
        }

        .card_title_bottom {
            font-size: 6.2px;
            font-weight: 700;
            color: #ffffff;
            text-transform: uppercase;
            position: relative;
            font-family: microgramma;
            top: 3px;
        }

        .card_info {
            font-size: 8px;
            color: #ffffff;
            text-transform: uppercase;
            font-weight: bolder;
            /* font-family: arialBlack !important; */
        }

        .card_info .s_title-f {
            width: 35px;
            text-align: left;
            display: inline-block;
        }

        .card_info .s_title-l {
            width: 40px;
            text-align: left;
            display: inline-block;
        }

        .gem_num {
            /* font-weight: 900; */
            font-weight: 700;
            color: #ffffff;
            line-height: 1px;
            text-align: center;
            height: 30px;
            position: relative;
            top: 35px;
            font-family: sans-serif !important;
        }

        .logo_qr {
            text-align: center;
            height: 70px;
            position: relative;
            top: 16px;
            right: 12px;
        }

        .gem_mint {
            color: #ffffff;
            font-weight: 800;
            text-align: center;
            position: relative;
            top: 30px;
            /* left: 12px; */
            font-size: 8px;
            text-wrap-mode: nowrap;
            white-space: nowrap;
        }

        .mini_card .number {
            font-size: 8px;
            font-weight: 600;
            top: 20px;
            position: relative;
            color: #ffffff;
            /* font-family: arialMedium !important; */
            font-family: sans-serif !important;
        }

        .auth_title {
            font-size: 20px;
            font-weight: 600;
        }

        .grade_title {
            font-size: 37px;
            font-weight: 700;
            color: #ffffff;
        }

        .site_name {
            font-size: 18px;
            font-weight: 500;
            color: #ffffff;
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
</head>

<body>

    @php
    function getTitleStyle($text, $type = null) {
        $length = strlen($text ?? '');
        if ($type === 'fontsize') {
            return $length > 15 ? 'font-size: 9px;' : '';
        }
        if ($type === 'position') {
            return $length > 15 ? 'bottom: -5px !important;' : '';
        }
        return '';
    }
    function getStyle($text) {
        $length = strlen($text ?? '');
        if ($length > 5 && $length <= 10) return 'line-height: 12px;';
        if ($length > 10 && $length <= 13) return 'font-size: 9px; line-height: 10px;';
        if ($length > 15) return 'font-size: 8px; line-height: 8px; margin-left: -2px';
        return '';
    }

    // Formats the grading text by adding a line break before '/' if '/' present.
    function formatText($text) {
        if (strpos($text, '/') !== false) {
            $parts = explode('/', $text, 2);
            return trim($parts[0]) . ' <br> / ' . trim($parts[1]);
        }
        return $text;
    }
    @endphp
    @foreach ($certs as $cert)
        @php
            $avg_grade = collect([$cert?->centering, $cert?->corners, $cert?->edges, $cert?->surface])
                ->filter()
                ->avg();
            if ($avg_grade == 10) {
                //
                $logo_main = public_path('/assets/logo_pdf.png');
                $logo = public_path('/assets/logo_pdf.png');
                $logo_bottom = public_path('/assets/logo_pdf_bottom_2.png');
                $top_grade = true;

                $ext = pathinfo($logo)['extension'];
                $transLogo = str($logo)
                    ->swap([
                        ".$ext" => "_opacity.$ext",
                    ])
                    ->value();
                if (!File::exists($transLogo)) {
                    $transLogo = $logo;
                }
            } else {
                $logo_main = public_path('/assets/logo_pdf.png');
                $logo = public_path('/assets/logo_pdf.png');
                $logo_bottom = public_path('/assets/logo_pdf_bottom.png');
                $top_grade = false;

                $ext = pathinfo($logo)['extension'];
                $transLogo = str($logo)
                    ->swap([
                        ".$ext" => "_opacity.$ext",
                    ])
                    ->value();
                if (!File::exists($transLogo)) {
                    $transLogo = $logo;
                }
            }
        @endphp

        <style>
            .watermark_logo:before {
                background-image: url('data:{{ mime_content_type($transLogo) }};base64,{{ base64_encode(file_get_contents($transLogo)) }}');
            }
        </style>
        <div class="mini_card position-relative overflow-hidden w-100">
            <div class="row align-items-center justify-content-center w-100 d-flex"
                style="align-items: center; display: flex;vertical-align: middle; margin-top: 0;">
                <div class="col-sm-3">
                    <div class="text-center" style="padding-top: 12px; position: relative; top: 15px; left: 5px">
                        <img height="35px" src="data:{{ mime_content_type($logo_main) }};base64,{{ base64_encode(file_get_contents($logo_main)) }}"
                            class="w-100" alt="TGA-Grading">
                        <div class="number" style="margin-top: -.9rem">{{ $cert->card_number }}</div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="watermark_logo position-relative" style="top: 5px; left: -5px">
                        <div class="mb-2 card_title" style="{{getTitleStyle(($cert->details->card ?? $cert->card), 'fontsize')}}">
                            {{ $cert->details->year ?? $cert->year }} {{ $cert->details->brand_name ?? $cert->brand_name }} <br>
                            {{ $cert->details->card_name ?? $cert->card_name }} #{{ $cert->details->card ?? $cert->card }}
                            @if ($cert->details?->notes) <br> {{ $cert->details->notes }} @elseif ($cert->notes) <br> {{ $cert->notes }} @endif
                        </div>
                        <div class="">
                            <div class="card_info" style="width:55%;float:left;position: relative;{{ $cert->details?->notes || $cert->notes ? 'bottom:0' : 'bottom:-1rem' }}; {{getTitleStyle(($cert->details->card ?? $cert->card), 'position')}}">
                                <div>
                                    <span class="s_title-f">SURFACE</span>
                                    <span>&nbsp;&nbsp;&nbsp;{{ $cert->surface }}</span>
                                </div>
                                <div>
                                    <span class="s_title-f">CORNERS</span>
                                    <span>&nbsp;&nbsp;&nbsp;{{ $cert->corners }}</span>
                                </div>
                            </div>
                            <div class="card_info" style="width:auto;float:right; position: relative; left: 8px;{{ $cert->details?->notes || $cert->notes ? 'bottom:0' : 'bottom:-1rem' }}; {{getTitleStyle(($cert->details->card ?? $cert->card), 'position')}}">
                                <div>
                                    <span class="s_title-l">CENTERING</span>
                                    <span>&nbsp;&nbsp;&nbsp;{{ $cert->centering }}</span>
                                </div>
                                <div>
                                    <span class="s_title-l">EDGES</span>
                                    <span>&nbsp;&nbsp;&nbsp;{{ $cert->edges }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div style="text-align: right !important;">
                        @php
                            $cert->final_grading = $isManual ? $cert->grade : $cert->final_grading;
                        @endphp
                        <div class="gem_num"
                            style=" @if (is_numeric($cert->final_grading) && strpos($cert->final_grading, '.') !== false) 
                                font-size: 32px; left: 8px; @elseif(strlen((string) $cert->final_grading) == 2)
                                font-size: 40px; left: -1px; @elseif(strlen((string) $cert->final_grading) == 1)
                                font-size: 40px; left: 10px; @endif">

                            {{-- @if (str($cert->final_grading)->contains('.') && str_ends_with($cert->final_grading,'0'))
                                @php
                                    $cert->final_grading = substr($cert->final_grading, 0, -1);
                                @endphp
                            @endif
                            {{ $cert->final_grading }} --}}

                            {{-- Convert the grading value in standard format --}}
                            @php
                                $grading = (string) $cert->final_grading;
                                if( $cert->is_authentic == 1) {
                                    $grade = $grading;
                                } elseif (str_contains($grading, '.5')) {
                                    $grade = rtrim($grading, '0');
                                } else {
                                    $grade = (int) $grading;
                                }
                            @endphp

                            {{ $grade }}
                        </div>


                        @php
                            $grading = $cert?->is_authentic == 1 ? 'A' : (int) str($cert?->final_grading)->before('.')->value();
                        @endphp
                        
                        <div class="gem_mint text-uppercase" style="text-align: center;
                            @if (is_numeric($cert->final_grading) && strpos($cert->final_grading, '.') !== false)
                                left: 8px; @elseif(strlen((string) $cert->final_grading) == 2)
                                left: -1px; @elseif(strlen((string) $cert->final_grading) == 1)
                                left: 10px; @endif">
                                
                            @if($isManual)
                                <div style="{{ getStyle($cert->grade_name) }}">
                                    {!! formatText($cert->grade_name) !!}
                                </div>
                            @elseif ($avg_grade == 10)
                                <div style="{{ getStyle($finalGradings[$grading][1] ?? '') }}">
                                    {!! formatText($finalGradings[$grading][1] ?? '') !!}
                                </div>
                            @else
                                <div style="{{ getStyle($finalGradings[$grading][0] ?? '') }}">
                                    {!! formatText($finalGradings[$grading][0] ?? '') !!}
                                </div>
                            @endif
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
        <div class="mini_card position-relative overflow-hidden">
            <div class="row align-items-center justify-content-center w-100 d-flex"
                style="align-items: center; display: flex;vertical-align: middle; margin-top: 0px;">

                <div class="col-sm-3">
                    <div class="text-center" style="padding-top: 0; position: relative; top: 20px; left: 5px">
                        <img height="35px" src="data:{{ mime_content_type($logo_main) }};base64,{{ base64_encode(file_get_contents($logo_main)) }}"
                            alt="TGA-Grading" style="width: 110%">
                    </div>
                </div>
                <div class="col-sm-7">
                    <div class="watermark_logo bottom_card position-relative">
                        <div class="mb-4 card_title_bottom text-center mt-1">
                            TRUE GRADE AUTHENTICATION
                        </div>
                        <div class="text-center back_page_text mt-0 mb-3">
                            IT'S IN THE GRADE
                        </div>
                        <div class="text-center back_page_bottom_text">
                            <a href="{{ route('frontend.index') }}"
                                style="color: #ffffff !important;">www.tgagrading.com</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="" style="text-align: right !important;">
                        <div class="logo_qr">
                            {!! QrCode::size(60)->style('round')->color(255, 255, 255)->backgroundColor(0, 0, 0)->generate(
                                    route('frontend.certification', [
                                        'number' => $isManual ? $cert->qr_link : $cert->card_number,
                                    ]),
                                ) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

</body>

</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>White Label</title>

    <style>
        @font-face {
            font-family: microgramma;
            src: url('data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path("fonts/microgrammanormal.ttf"))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }
        @font-face {
            font-family: microgrammaBold;
            src: url('data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path("fonts/Microgramma D Extended Bold.otf"))) }}') format('truetype');
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
            font-family: arialBold;
            src: url('data:font/truetype;charset=utf-8;base64,{{ base64_encode(file_get_contents(public_path("fonts/ArialBold.ttf"))) }}') format('truetype');
            font-weight: 700;
            font-style: normal;
        }
        body {
            background: #000000;
            margin: 0px;
            /* letter-spacing: 1px; */
        }

        .s_title-f,.s_title-l {
            font-family: 'arialBold';
        }
        :root {
            --primary: #6453f7;
        }
        .position-relative {
            position: relative;
        }
        .position-absolute {
            position: absolute;
        }
        .overflow-hidden {
            overflow: hidden;
        }
        .align-items-center {
            -ms-flex-align: center !important;
            align-items: center !important;
        }

        .w-100 {
            width: 100%;
        }
        .w-75 {
            width: 75%;
        }
        .w-50 {
            width: 50%;
        }
        .w-25 {
            width: 25%;
        }


        .mt-1,
        .my-1 {
            margin-top: .25rem !important;
        }


        .mb-1,
        .my-1 {
            margin-bottom: .25rem !important;
        }
        .mt-2,
        .my-2 {
            margin-top: .5rem !important;
        }


        .mb-2,
        .my-2 {
            margin-bottom: .5rem !important;
        }
        .mt-3,
        .my-3 {
            margin-top: 1rem !important;
        }


        .mb-3,
        .my-3 {
            margin-bottom: 1rem !important;
        }

        .mt-4,
        .my-4 {
            margin-top: 1.5rem !important;
        }


        .mb-4,
        .my-4 {
            margin-bottom: 1.5rem !important;
        }

        .row {
            display: flex;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: -7.5px;
            margin-left: -7.5px;
            display: -webkit-box;
        }

        .col-2 {
            -ms-flex: 0 0 16.666667%;
            flex: 0 0 16.666667%;
            max-width: 16.666667%;
            width: 16.666667%;
        }
        .col-3 {
            -ms-flex: 0 0 17%;
            flex: 0 0 17%;
            max-width: 17%;
            width: 17%;
        }
        .col-4 {
            -ms-flex: 0 0 27.333333%;
            flex: 0 0 27.333333%;
            max-width: 27.333333%;
            width: 27.333333%;
        }
        .col-5 {
            -ms-flex: 0 0 53%;
            flex: 0 0 53%;
            max-width: 53%;
            width: 53%;
        }
        .col-6 {
            -ms-flex: 0 0 50%;
            flex: 0 0 50%;
            max-width: 50%;
            width: 50%;
        }
        .col-7 {
            -ms-flex: 0 0 58.333333%;
            flex: 0 0 58.333333%;
            max-width: 58.333333%;
            width: 58.333333%;
        }
        .text-center {
            text-align: center !important;
        }

        .text-center {
            text-align: center !important;
        }

        body, html{
            background-color: #000000
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


        .text-right {
            text-align: right !important;
        }

        .card_title_bottom {
            font-size: 8px;
            font-weight: 700;
            color: #000000;
            text-transform: uppercase;
            position: relative;
            font-family: microgrammaBold;
            top: -15px;
            margin-top: -12px;
            transform: scale(1.5, 1.1) !important;
        }

        .back_page_text {
            font-family: microgrammaBold;
            font-size: 14px;
            font-weight: 900;
            color: #000000;
            position: relative;
            /* top: 25px; */
            transform: scale(1.1, 1) !important;
        }

        .back_page_bottom_text {
            font-size: 12px;
            font-weight: 700;
            position: relative;
            font-family: sans-serif;
            top: 24px;
        }

        
        .logo_qr {
            text-align: center;
            position: relative;
            /* top: 5px; */
            right: 0px;
        }

        a {
            text-decoration: none;
            background-color: transparent;
        }

        .mini_card {
            border: 5px solid #000000;
            background: #ffffff;
            height: 90px;
            overflow: visible !important;
        }

        .bottom_card {
            left: 0px;
        }
        .bottom_card:before {
            bottom: -80%;
        }
        .watermark_logo:before {
            position: absolute;
            content: "";
            background-position: center;
            background-repeat: no-repeat;
            width: 180px;
            height: 90px;
            opacity: 0.1;
            border: none;
            fill-opacity: 0.1;
            display: block;
            z-index: 1;
            background-size: 180px auto;
        }


        .card_title {
            font-size: 12px;
            font-weight: 800;
            color: #000000;
            text-transform: uppercase;
            font-family: 'arialBold';
        }

        .card_info {
            font-size: 12px;
            color: #000000;
            text-transform: uppercase;
            font-weight: 700;
        }

        .card_info .s_title-f {
            width: 48px;
            text-align: left;
            display: inline-block;
        }

        .card_info .s_title-l {
            width: 50px;
            text-align: left;
            display: inline-block;
            margin-right: 10px;
        }

        .gem_num {
            font-weight: 700;
            color: #000000;
            text-align: center;
            font-family: 'arialBold' !important;
            position: relative;
            top: 10px;
        }

        .gem_mint {
            color: #000000;
            font-weight: 700 !important;
            text-align: center;
            position: relative;
            top: -1px;
            font-size: 12px;
            text-wrap-mode: nowrap;
            white-space: nowrap;
            font-family: 'arialBold' !important;
        }

        .mini_card .number {
            font-size: 12px;
            font-weight: 600;
            position: relative;
            color: #000000;
            font-family: sans-serif !important;
        }

        .auth_title {
            font-size: 20px;
            font-weight: 600;
        }

        .grade_title {
            font-size: 37px;
            font-weight: 700;
            color: #000000;
        }

        .site_name {
            font-size: 18px;
            font-weight: 500;
            color: #000000;
        }

        .tga_logo {
            position: absolute;
            left: 45%;
            text-align: center;
            bottom: -9px;
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
                return $length > 15 ? 'font-size: 12px;' : '';
            }
            if ($type === 'position') {
                return $length > 15 ? 'bottom: -5px !important;' : '';
            }
            return '';
        }
        function getStyle($text) {
            $length = strlen($text ?? '');
            if ($length > 5 && $length <= 10) return 'line-height: 20px;';
            if ($length > 10 && $length <= 13) return 'font-size: 9px; line-height: 10px;';
            if ($length > 15) return 'font-size: 8px; line-height: 8px; margin-left: -2px';
            return 'line-height: 20px;';
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

    @php
        $avg_grade = collect([$cert->centering, $cert->corners, $cert->edges, $cert->surface])
            ->filter()
            ->avg();
        $logo_main = public_path('/assets/logo_pdf_2.png');
        $logo = public_path('/assets/logo_pdf_2.svg');
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
    @endphp

    <style>
        .watermark_logo:before {
            background-image: url('data:{{ mime_content_type($transLogo) }};base64,{{ base64_encode(file_get_contents($transLogo)) }}');
        }
    </style>
    <div class="mini_card position-relative overflow-hidden">
        <div class="row align-items-center justify-content-center w-100 d-flex"
            style="align-items: center; display: flex;vertical-align: middle; margin-top: 0; justify-content: space-between !important; padding: 0px 6px;">
            <div class="col-4">
                <div class="text-center" style="position: relative; left: 5px; top: 5px;">
                    <img height="35px" src="data:{{ mime_content_type($logo_main) }};base64,{{ base64_encode(file_get_contents($logo_main)) }}"
                        class="w-100" alt="TGA-Grading" style="width: 90px; height: 45px;">
                    <div class="number" style="font-family: 'arialMedium';">{{ $cert->card_number }}</div>
                </div>
            </div>
            <div class="col-5">
                <div class="watermark_logo position-relative" style="@if ($cert->details?->notes) top: 5px; @elseif($cert->notes) top: 5px; @else top: -4px; @endif">
                    <div class="mb-2 card_title" style="padding: 0px 5px; {{getTitleStyle(($cert->details->card ?? $cert->card), 'fontsize')}}">
                        {{ $cert->details->year ?? $cert->year }} {{ $cert->details->brand_name ?? $cert->brand_name }} <br>
                        {{ $cert->details->card_name ?? $cert->card_name }} #{{ $cert->details->card ?? $cert->card }}
                        @if ($cert->details?->notes) <br> {{ $cert->details?->notes }} @elseif ($cert->notes) <br> {{ $cert->notes }} @endif
                    </div>
                    <div class="" style="padding: 0px 5px;">
                        <div class="card_info" style="width:50%; float:left; position: relative;{{ $cert->details?->notes || $cert->notes ? 'bottom:-5px' : 'bottom:-1rem' }}; {{getTitleStyle(($cert->details->card ?? $cert->card), 'position')}}">
                            <div>
                                <span class="s_title-f">SURFACE</span>
                                <span style="font-family: 'arialBold';">&nbsp;&nbsp;&nbsp;{{ $cert->surface }}</span>
                            </div>
                            <div>
                                <span class="s_title-f">CORNERS</span>
                                <span style="font-family: 'arialBold';">&nbsp;&nbsp;&nbsp;{{ $cert->corners }}</span>
                            </div>
                        </div>
                        <div class="card_info" style="width:50%; float:right; position: relative; text-align: right;{{ $cert->details?->notes || $cert->notes ? 'bottom:-5px' : 'bottom:-1rem' }}; {{getTitleStyle(($cert->details->card ?? $cert->card), 'position')}}">
                            <div>
                                <span class="s_title-l">CENTERING</span>
                                <span style="font-family: 'arialBold';">&nbsp;&nbsp;&nbsp;{{ $cert->centering }}</span>
                            </div>
                            <div>
                                <span class="s_title-l">EDGES</span>
                                <span style="font-family: 'arialBold';">@if(str_contains($cert->centering, '.5') && !str_contains($cert->edges, '.5')) &nbsp;&nbsp; @endif &nbsp;&nbsp;&nbsp;{{ $cert->edges }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-3" style="margin-left: 0px;">
                <div style="text-align: right !important;">
                    @php
                        $cert->final_grading = $isManual ? $cert->grade : $cert->final_grading;
                    @endphp
                    <div class="gem_num"
                        style="@if (is_numeric($cert->final_grading) && strpos($cert->final_grading, '.5') !== false) 
                            font-size: 50px; transform: scale(.8, 1); @else font-size: 60px; transform: scale(1, 1.1); @endif">

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
                        $grading = $cert->is_authentic == 1 ? 'A' : (int) str($cert->final_grading)->before('.')->value();
                    @endphp
                    
                    <div class="gem_mint text-uppercase" style="text-align: center;">

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
    {{-- <div class="page-break"></div> --}}
    <div class="mini_card position-relative overflow-hidden" style="transform: rotate(180deg);">
        <div class="row align-items-center justify-content-center w-100 d-flex"
            style="align-items: center; display: flex;vertical-align: middle; margin-top: 0; justify-content: space-between !important; padding: 0px 6px; height: 100%;">

            <div class="col-3" style="height: 100%; display: flex; align-items: center; justify-content: center;">
                <div class="text-center" style="padding-top: 0; position: relative;">
                    <img height="35px" src="data:{{ mime_content_type($logo_main) }};base64,{{ base64_encode(file_get_contents($logo_main)) }}"
                        alt="TGA-Grading" style="width: 90px; height: 45px;">
                </div>
            </div>
            
            <div class="col-5" style="height: 100%; display: flex; align-items: center; justify-content: center;">
                <div class="watermark_logo bottom_card position-relative">
                    <div class="card_title_bottom text-center">
                        TRUE GRADE AUTHENTICATION
                    </div>
                    <div class="text-center back_page_text">
                        IT'S IN THE GRADE
                    </div>
                    <div class="text-center back_page_bottom_text">
                        <a href="{{ route('frontend.index') }}"
                            style="color: black !important;">www.tgagrading.com</a>
                    </div>
                </div>
            </div>
            <div class="col-3" style="height: 100%; display: flex; align-items: center; justify-content: center;">
                <div class="" style="text-align: right !important;">
                    <div class="logo_qr">
                        {!! QrCode::size(70)->style('round')->color(0, 0, 0)->backgroundColor(255, 255, 255, 0)->generate(
                                route('frontend.certification', [
                                    'number' => $isManual ? $cert->qr_link : $cert->card_number,
                                ]),
                            ) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>

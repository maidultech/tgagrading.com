@extends('frontend.layouts.app')

@section('title')
    {{ 'Support Ticket' }}
@endsection


@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/css/intlTelInput.css">
    <style>
        .iti.iti--allow-dropdown.iti--show-flags.iti--inline-dropdown {
            width: 100% !important;
        }
    </style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User') }}</li>
        <li class="breadcrumb-item">{{ __('Support Ticket') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!-- ======================= my account start  ============================ -->
    <div class="account_seciton pb-5 pt-3">
        <div class="container">
            <div class="section_heading mb-4">
                <h1>Edit Accounts</h1>
            </div>
            <div class="row gy-4 gy-lg-0">
                <div class="col-lg-3">
                    @include('user.sidebar')
                </div>
                <div class="col-lg-9">
                    <div class="user_dashboard p-3 p-xl-4 rounded border border-light">
                        <div class="header mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="title">
                                    <h4>Create New Ticket</h4>
                                </div>
                                <div>
                                    <a href="{{ route('user.support') }}" class="btn btn-light rounded-pill p-2">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="user_info">
                            <form action="{{ route('user.support.store') }}" method="post" class="mb-5" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                            <input type="text" name="subject" id="subject" class="form-control"  placeholder="Enter subject" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="priority" class="form-label">Priority</label>
                                            <select name="priority" id="priority" class="form-select" required>
                                                <option value="low">Low</option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                            <textarea name="message" id="message" class="form-control" placeholder="Enter your message" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--======================= my account end ============================ -->

@endsection

@push('script')
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/intlTelInput.min.js"></script>
    <script>
        const input = document.querySelector("#phone");
        const iti = window.intlTelInput(input, {
            initialCountry: "ca", // Canada as the initial country
            separateDialCode: true,
            autoPlaceholder: false,
            onlyCountries: ["ca", "us"], // Allow only Canada and USA
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@24.5.0/build/js/utils.js",
        });

        // Function to get the dial code
        function getDialCode() {
            const countryData = iti.getSelectedCountryData();
            if (countryData.dialCode === "1" && countryData.iso2 !== "ca") {
                iti.setCountry("ca");
                countryData.name = 'Canada';
            }
            $('input[name="dial_code"]').val(countryData.dialCode);
        }
        getDialCode();

        // Example of using it when the input changes
        input.addEventListener('countrychange', function() {
            getDialCode();
        });

    </script>
@endpush

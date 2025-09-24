@extends('frontend.layouts.app')

@section('title')
    {{ 'My Account' }}
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
        <li class="breadcrumb-item">{{ __('My Account') }}</li>
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
                    @section('user_dashboard','active')
                    @include('user.sidebar')
                </div>
                <div class="col-lg-9">
                    <div class="user_dashboard p-3 p-xl-4 rounded border border-light">
                        <div class="header mb-4">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="title">
                                    <h4>Edit Account</h4>
                                </div>
                                <div>
                                    <a href="{{ route('user.dashboard') }}" class="btn btn-light rounded-pill p-2">
                                        <svg  xmlns="http://www.w3.org/2000/svg"  width="24"  height="24"  viewBox="0 0 24 24"  fill="none"  stroke="currentColor"  stroke-width="2"  stroke-linecap="round"  stroke-linejoin="round"  class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M5 12l14 0" /><path d="M5 12l6 6" /><path d="M5 12l6 -6" /></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="user_info">
                            <form action="{{ route('user.profile.update') }}" method="post" class="mb-5" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-4">
                                            <label for="image" class="form-label">Profile Image</label>
                                            <input type="file" name="image" class="form-control">
                                            <img class="mt-2 rounded-pill" src="{{ getPhoto($user->image) }}" alt="{{ $user->name }}" style="width: 50px; height: 50px;">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="first_name" class="form-label">First Name</label>

                                         <input type="text" name="first_name" id="first_name" class="form-control" value="{{ $user->name }}" placeholder="Enter your first name" required>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="last_name" class="form-label">Last Name</label>
                                            <input type="text" name="last_name" id="last_name" class="form-control" value="{{ $user->last_name }}" placeholder="Enter your last name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="email" class="form-label">Email</label>
                                            <input readonly type="email" name="email" id="email" class="form-control" value="{{ $user->email }}" required placeholder="Enter your email address">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="phone" class="form-label">Phone Number</label>
                                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter your phone number" value="{{ $user->dial_code ? '+'.$user->dial_code : '+1' }}{{ $user->phone ?? '' }}" >
                                            <input type="hidden" name="dial_code">
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </form>

                            <div class="title mb-4">
                                <h4>Change Password</h4>
                            </div>
                            <form action="{{ route('user.change.password') }}" method="post" id="passChangeForm" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" name="new_password" id="password" class="form-control" placeholder="New password" required>
                                        </div>
                                        <div class="w-100">
                                            <div id="password-requirements" class="mt-3 d-none">
                                                <span id="upper-case" class="text-danger">✖ Uppercase letter</span><br>
                                                <span id="lower-case" class="text-danger">✖ Lowercase letter</span><br>
                                                <span id="number-char" class="text-danger">✖ Include Number</span><br>
                                                <span id="special-char" class="text-danger">✖ Special character</span><br>
                                                <span id="length" class="text-danger">✖ Minimum 8 characters</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="confirm_password" class="form-label">Confirm Password</label>
                                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Confirm new  password" required>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
<script>
    $(document).on('input change','#password', function() {
        var password = $(this).val();
        checkPasswordComplexity(password,'#passChangeForm');
    });
</script>
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

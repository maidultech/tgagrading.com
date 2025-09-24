@extends('admin.layouts.master')

@section('customer', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/css/intlTelInput.css">
    <style>
        .iti.iti--allow-dropdown.iti--show-flags.iti--inline-dropdown {
            width: 100% !important;
        }
    </style>
    <style>
        .profile-pic {
            width: 200px;
            max-height: 200px;
            display: inline-block;
        }

        .file-upload {
            display: none;
        }

        .circle {
            border-radius: 100% !important;
            overflow: hidden;
            width: 128px;
            height: 128px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            position: relative;
            /* top: 72px; */
        }

        img {
            max-width: 100%;
            height: auto;
        }

        .p-image {
            position: absolute;
            top: 88px;
            left: 92px;
            color: #666666;
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        }

        .p-image:hover {
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
        }

        .upload-button {
            font-size: 1.2em;
        }

        .upload-button:hover {
            transition: all .3s cubic-bezier(.175, .885, .32, 1.275);
            color: #999;
        }
        .upload-button {
            cursor: pointer;
        }
        .hr-text {
            display: flex;
            align-items: center;
            margin: 2rem 0;
            font-size: .825rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            line-height: 1rem;
            color: #6c7a91;
            height: 1px;
        }
        .hr-text:before {
            content: "";
            margin-right: .5rem;
        }
        .hr-text:after, .hr-text:before {
            flex: 1 1 auto;
            height: 1px;
            background-color: #dce1e7;
        }
        .hr-text:after {
            content: "";
            margin-left: .5rem;
        }
    </style>
@endpush
@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">{{ __('Create New Customer') }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            {{-- @if (Auth::user()->can('admin.customer.index')) --}}
                                                <a href="{{ route('admin.customer.index') }}"
                                                    class="btn btn-primary btn-gradient btn-sm">{{ __('messages.common.back') }}</a>
                                            {{-- @endif --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <form action="{{ route('admin.customer.store') }}" method="post" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="small-12 medium-2 large-2 columns">
                                                    <div class="circle">
                                                        <img class="profile-pic" src="https://t3.ftcdn.net/jpg/03/46/83/96/360_F_346839683_6nAPzbhpSkIpb8pmAwufkC7c5eD7wYws.jpg">
                                                    </div>
                                                    
                                                    <div class="p-image">
                                                        <i class="fa fa-camera upload-button"></i>
                                                        <input class="file-upload" name="image" type="file"
                                                            accept="image/*" />
                                                    </div>
                                                </div>
                                            </div>
                                            {{-- <div class="form-group">
                                                <label for="image"
                                                    class="form-lable">{{ __('messages.customer.user_image') }}
                                                    <small class="text-info fw-bold"><strong>({{ __('messages.settings_home_content.recommended_size') }}
                                                            150x150px)</strong></small>
                                                </label>
                                                <input type="file" name="image" id="image" class="form-control">
                                            </div> --}}
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="name" class="form-lable">{{ __('First Name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="name" id="name"
                                                    placeholder="{{ __('First Name') }}" class="form-control" required
                                                    value="{{ old('name') }}">
                                            </div>
                                        </div>


                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="name" class="form-lable">{{ __('Last Name') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="last_name" id="last_name"
                                                    placeholder="{{ __('Last Name') }}" class="form-control" required
                                                    value="{{ old('last_name') }}">
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="email" class="form-lable">{{ __('Email') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" name="email" id="email"
                                                    placeholder="{{ __('Email') }}" class="form-control" required
                                                    value="{{ old('email') }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="email" class="form-lable">{{ __('Phone Number') }} <span
                                                        class="text-danger">*</span></label>
                                                <input type="tel" name="phone" id="phone"
                                                    placeholder="{{ __('Phone Number') }}" class="form-control" required
                                                    value="{{ old('phone') }}" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*?)\..*/g, '$1');">
                                                <input type="hidden" name="dial_code">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="password" class="form-lable">{{ __('Password') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group input-group-flat">
                                                    <input type="password" name="password" id="password"
                                                        placeholder="{{ __('Password') }}" class="form-control" required>
                                                    <span class="input-group-text"
                                                        style="border-top-left-radius: 0px;border-bottom-left-radius: 0px;">
                                                        <a href="javascript:void(0)"
                                                            class="link-secondary fa fa-fw fa-eye field-icon toggle-password"
                                                            toggle="#password">
                                                        </a>
                                                    </span>
                                                </div>
                                                <div class="password_validation"></div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="dob" class="form-lable">Date Of Birth</label>
                                                <input type="date" name="dob" id="dob" placeholder="{{ __('dob') }}" class="form-control" value="{{ old('dob') }}" style="background: transparent;">
                                            </div>
                                        </div> --}}

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="status" class="form-lable">{{ __('Status') }} <span
                                                        class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>
                                                        {{ __('Active') }}</option>
                                                    <option value="0" {{ old('address') == '0' ? 'selected' : '' }}>
                                                        {{ __('Inactive') }}</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-lg-12 mb-3">
                                            <div class="row">
                                                <div class="hr-text col-lg-12">Address Information</div>
                                                <div class="form-group my-2 col-md-6">
                                                    <label for="street">Street</label>
                                                    <input type="text" name="street" class="form-control" value="{{ old('street') }}"
                                                        placeholder="Enter Street">
                                                </div>
                                                <div class="form-group my-2 col-md-6">
                                                    <label for="apt_unit">Apt/Unit</label>
                                                    <input type="text" name="apt_unit" class="form-control" value="{{ old('apt_unit') }}"
                                                        placeholder="Enter Apt/Unit">
                                                </div>
                                                <div class="form-group my-2 col-md-6">
                                                    <label for="city">City</label>
                                                    <input type="text" name="city" class="form-control" value="{{ old('city') }}" placeholder="Enter City">
                                                </div>
                                                <div class="form-group my-2 col-md-6">
                                                    <label for="state">State</label>
                                                    <input type="text" name="state" class="form-control" value="{{ old('state') }}" placeholder="Enter State">
                                                </div>
                                                <div class="form-group my-2 col-md-6">
                                                    <label for="zip_code">Zip Code</label>
                                                    <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}"
                                                        placeholder="Enter Zip Code">
                                                </div>
                                                <div class="form-group my-2 col-md-6">
                                                    <label for="country">Country</label>
                                                    <input type="text" name="country" class="form-control" value="{{ old('country') }}"
                                                        placeholder="Enter Country">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <button type="submit"
                                                    class="btn btn-success">{{ __('Save') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('script')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/js/intlTelInput.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dob').flatpickr({
                dateFormat: "Y-m-d" // Customize the date format as needed
            });
        });

        $(document).ready(function() {
            // Frontend Password Validation
            $(document).on('input', '[name="password"]', function() {
                var password = $(this).val();
                var regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

                // Find the .password_validation div inside the parent .form-group
                var validationContainer = $(this).closest('.form-group').find('.password_validation');

                if (!regex.test(password)) {
                    $(this).addClass('is-invalid');

                    // Add error message only if it doesn't already exist
                    if (validationContainer.find('#password-error').length === 0) {
                        validationContainer.html(
                            '<small id="password-error" class="text-danger">Password must contain at least one uppercase letter, one special character, and be at least 8 characters long.</small>'
                        );
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    validationContainer.html(''); // Clear the error message
                }
            });
        });
        // password show hide
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });

        $(document).ready(function() {
            var readURL = function(input) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        $('.profile-pic').attr('src', e.target.result);
                    }

                    reader.readAsDataURL(input.files[0]);
                }
            }
            $(".file-upload").on('change', function() {
                readURL(this);
            });

            $(".upload-button").on('click', function() {
                $(".file-upload").click();
            });
        });
    </script>
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

@extends('admin.layouts.master')


@section('settings_menu', 'menu-open')
@section('home', 'active')

@section('title')
    {{ $data['title'] ?? '' }}
@endsection

@push('style')
@endpush

@section('content')

    <div class="content-wrapper">
        <div class="container-fluid pt-3">
            <div class="row">
                <div class="col-md-4">
                    <div class="content">
                        <div class="container-fluid">
                            <div>
                                @if (Session::get('success'))
                                    <div class="col-lg-12">
                                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                                            {{ Session::get('success') }}
                                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-lg-12">
                                    <form action="{{ route('admin.settings.homeContent.update') }}" method="post"
                                        enctype="multipart/form-data" id="settingUpdate">
                                        @csrf
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        {{ __('messages.settings_home_content.banner_section') }}</h3>
                                                    <div class="float-right">
                                                        <button type="button" class="btn btn-primary addBanner">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div id="bannerContainer">
                                                        @if (!empty($banners) && $banners->count() > 0)
                                                            @foreach ($banners as $k => $banner)
                                                                <div class="banner-group mb-4">
                                                                    <input type="hidden" name="banners[]" value="banner">
                                                                    <input type="hidden" name="banner_id[]"
                                                                        value="{{ $banner->id }}">
                                                                    <div class="mb-3">
                                                                        <img src="{{ asset($banner['image'] ?? 'assets/default.png') }}"
                                                                            height="50px" />
                                                                        <br>
                                                                        <label class="form-label mt-2">Banner Image</label>
                                                                        <input type="file" class="form-control"
                                                                            name="banner_image[]"
                                                                            accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Banner Information</label>
                                                                        <select name="banner_info_active[]"
                                                                            class="form-control form-select"
                                                                            id="info_active">
                                                                            <option value="1"
                                                                                {{ $banner->info_active == 1 ? 'selected' : '' }}>
                                                                                Enable</option>
                                                                            <option value="0"
                                                                                {{ $banner->info_active == 0 ? 'selected' : '' }}>
                                                                                Disable</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Banner Heading</label>
                                                                        <input type="text" class="form-control"
                                                                            name="banner_heading[]"
                                                                            value="{{ $banner->title ?? '' }}"
                                                                            placeholder="Banner Heading...">
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Banner Description</label>
                                                                        <textarea type="text" class="form-control summernote" name="banner_description[]"
                                                                            placeholder="Banner Description...">{!! $banner->description ?? '' !!}</textarea>
                                                                    </div>
                                                                    @if ($k > 0)
                                                                        <div class="text-right mb-3">
                                                                            <button type="button"
                                                                                class="btn btn-danger removeBanner"><i
                                                                                    class="fa fa-minus"></i></button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endforeach
                                                        @else
                                                            <div class="banner-group mb-4">
                                                                <input type="hidden" name="banners[]" value="banner">
                                                                <div class="mb-3">
                                                                    <img src="{{ asset('assets/default.png') }}"
                                                                        height="50px" />
                                                                    <br>
                                                                    <label class="form-label mt-2">Banner Image</label>
                                                                    <input type="file" class="form-control"
                                                                        name="banner_image[]"
                                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label required">Banner
                                                                        Heading</label>
                                                                    <input type="text" class="form-control"
                                                                        name="banner_heading[]"
                                                                        placeholder="Banner Heading..." required>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label ">Banner Description</label>
                                                                    <textarea type="text" class="form-control summernote" name="banner_description[]"
                                                                        placeholder="Banner Description..."></textarea>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- SB Section --}}
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        SB Section</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Title</label>
                                                                <input type="text" class="form-control"
                                                                    name="sb_title" value="{{ $home->sb_title ?? '' }}"
                                                                    placeholder="Section heading..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Short
                                                                    Details</label>
                                                                <textarea class="form-control" name="sb_details" rows="3" placeholder="Section Sub Heading"
                                                                    style="height: 100px !important;" required>{{ $home->sb_details ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Button
                                                                    Text</label>
                                                                <input type="text" class="form-control"
                                                                    name="sb_button_text"
                                                                    value="{{ $home->sb_button_text ?? '' }}"
                                                                    placeholder="Section button text">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Button
                                                                    Link</label>
                                                                <input type="text" class="form-control"
                                                                    name="sb_button_links"
                                                                    value="{{ $home->sb_button_links ?? '' }}"
                                                                    placeholder="Section button link">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row justify-content-end m-1">
                                                                <a class="text-end"
                                                                    href="{{ route('admin.image-content.index') }}">Upload
                                                                    Image
                                                                    Contents</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Why TGA Section --}}
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        Why Tga Section</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Why Tga Title</label>
                                                                <input type="text" class="form-control"
                                                                    name="why_title" value="{{ $home->why_title ?? '' }}"
                                                                    placeholder="Section heading..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Why Tga Subtitle</label>
                                                                <input class="form-control" name="why_subtitle"
                                                                    rows="3" placeholder="Section Sub Heading"
                                                                    value="{{ $home->why_subtitle ?? '' }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Service Section --}}
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        Service Section</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Service Title</label>
                                                                <input type="text" class="form-control"
                                                                    name="service_title"
                                                                    value="{{ $home->service_title ?? '' }}"
                                                                    placeholder="Section heading..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Service Subtitle</label>
                                                                <input class="form-control" name="service_subtitle"
                                                                    rows="3" placeholder="Section Sub Heading"
                                                                    value="{{ $home->service_subtitle ?? '' }}" required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="row justify-content-end m-1">
                                                                <a class="text-end"
                                                                    href="{{ route('admin.service.index') }}">Manage
                                                                    Services</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- How It Work Section --}}
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        {{ __('messages.settings_home_content.how_it_work_section') }}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings_home_content.section_heading') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="hw_heading"
                                                                    value="{{ old('hw_heading', $home->hw_heading ?? '') }}"
                                                                    placeholder="Heading..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Sub heading</label>
                                                                <textarea class="form-control" name="hw_sub_heading" rows="3" placeholder="Sub heading..."
                                                                    style="height: 80px !important;" required>{{ old('hw_sub_heading', $home->hw_sub_heading ?? '') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <h3 class="card-title">Steps</h3>
                                                                    {{-- <div class="float-right">
                                                                        <button type="button" class="btn btn-primary addStep">
                                                                            <i class="fa fa-plus"></i>
                                                                        </button>
                                                                    </div> --}}
                                                                </div>
                                                                <div class="card-body">
                                                                    <div id="stepContainer">
                                                                        @if (!empty($steps) && $steps->count() > 0)
                                                                            @foreach ($steps as $k => $step)
                                                                                <div class="step-group mb-4">
                                                                                    <input type="hidden" name="steps[]"
                                                                                        value="step">
                                                                                    <input type="hidden" name="step_id[]"
                                                                                        value="{{ $step->id }}">
                                                                                    <div class="mb-3">
                                                                                        <img src="{{ asset($step['image'] ?? 'assets/default.png') }}"
                                                                                            height="50px" />
                                                                                        <br>
                                                                                        <label class="form-label mt-2">Step
                                                                                            {{ $k + 1 }}
                                                                                            Icon</label>
                                                                                        <input type="file"
                                                                                            class="form-control"
                                                                                            name="step_icon[]"
                                                                                            accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label
                                                                                            class="form-label required">Step
                                                                                            {{ $k + 1 }}
                                                                                            Heading</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="step_heading[]"
                                                                                            value="{{ $step->title ?? '' }}"
                                                                                            placeholder="Step Heading..."
                                                                                            required>
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label
                                                                                            class="form-label required">Step
                                                                                            {{ $k + 1 }} Sub
                                                                                            Heading</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="step_subheading[]"
                                                                                            value="{{ $step->description ?? '' }}"
                                                                                            placeholder="Step Sub Heading..."
                                                                                            required>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        @else
                                                                            <div class="step-group mb-4">
                                                                                <input type="hidden" name="steps[]"
                                                                                    value="step">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label mt-2">Step 1
                                                                                        Icon</label>
                                                                                    <input type="file"
                                                                                        class="form-control"
                                                                                        name="step_icon[]"
                                                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label required">Step
                                                                                        1 Heading</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="step_heading[]"
                                                                                        placeholder="Step Heading..."
                                                                                        required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label required">Step
                                                                                        1 Sub Heading</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="step_subheading[]"
                                                                                        placeholder="Step Sub Heading..."
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="step-group mb-4">
                                                                                <input type="hidden" name="steps[]"
                                                                                    value="step">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label mt-2">Step 2
                                                                                        Icon</label>
                                                                                    <input type="file"
                                                                                        class="form-control"
                                                                                        name="step_icon[]"
                                                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label required">Step
                                                                                        2 Heading</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="step_heading[]"
                                                                                        placeholder="Step Heading..."
                                                                                        required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label required">Step
                                                                                        2 Sub Heading</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="step_subheading[]"
                                                                                        placeholder="Step Sub Heading..."
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                            <div class="step-group mb-4">
                                                                                <input type="hidden" name="steps[]"
                                                                                    value="step">
                                                                                <div class="mb-3">
                                                                                    <label class="form-label mt-2">Step 3
                                                                                        Icon</label>
                                                                                    <input type="file"
                                                                                        class="form-control"
                                                                                        name="step_icon[]"
                                                                                        accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label required">Step
                                                                                        3 Heading</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="step_heading[]"
                                                                                        placeholder="Step Heading..."
                                                                                        required>
                                                                                </div>
                                                                                <div class="mb-3">
                                                                                    <label class="form-label required">Step
                                                                                        3 Sub Heading</label>
                                                                                    <input type="text"
                                                                                        class="form-control"
                                                                                        name="step_subheading[]"
                                                                                        placeholder="Step Sub Heading..."
                                                                                        required>
                                                                                </div>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Pricing --}}
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">
                                                        {{ __('messages.settings_home_content.pricing_section') }}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings_home_content.section_heading') }}
                                                                    (en)</label>
                                                                <input type="text" class="form-control"
                                                                    name="pricing_heading"
                                                                    value="{{ $home->pricing_heading ?? '' }}"
                                                                    placeholder="Section heading..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Sub
                                                                    Heading</label>
                                                                <textarea class="form-control" name="pricing_sub_heading" rows="3" placeholder="Section Sub Heading"
                                                                    style="height: 100px !important;" required>{{ $home->pricing_sub_heading ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Verification --}}
                                        <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Verification</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Heading</label>
                                                                <input type="text" class="form-control"
                                                                    name="verification_heading"
                                                                    value="{{ $home->verification_heading ?? '' }}"
                                                                    placeholder="Section heading..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Section Sub
                                                                    Heading</label>
                                                                <textarea class="form-control" name="verification_sub_heading" rows="3" placeholder="Section Sub Heading"
                                                                    style="height: 100px !important;" required>{{ $home->verification_sub_heading ?? '' }}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-footer">
                                                    <div class="row">
                                                        <div class="col-md-12 text-center">
                                                            <button type="submit" class="btn btn-success"
                                                                id="updateButton">{{ __('messages.common.update') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Copyright --}}
                                        {{-- <div>
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Copyright</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">Copyright Text</label>
                                                                <input type="text" class="form-control"
                                                                       name="copyright_text"
                                                                       value="{{$home->copyright_text ?? ''}}"
                                                                       placeholder="Copyright Text..."
                                                                       required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                        {{-- <div class="card p-3">
                                            <div class="row">
                                                <div class="col-md-12 text-center">
                                                    <button type="submit" class="btn btn-success"
                                                        id="updateButton">{{ __('messages.common.update') }}</button>
                                                </div>
                                            </div>
                                        </div> --}}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8" style="min-height: 500px;">
                    <iframe src="{{ route('frontend.index') }}" width="100%" height="100%"
                        style="border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        const form = document.getElementById("settingUpdate");
        const submitButton = form.querySelector("button[type='submit']");

        form.addEventListener("submit", function() {

            $("#updateButton").html(`
                <span id="">
                    <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
                    Updating Setting...
                </span>
            `);

            submitButton.disabled = true;

        });

        $(document).ready(function() {
            // Function to create new step input fields
            function createStep() {
                const stepGroup = `
                    <div class="step-group mb-4">
                        <input type="hidden" name="steps[]" value="step">
                        <div class="mb-3">
                            <label class="form-label mt-2">Step Icon</label>
                            <input type="file" class="form-control" name="step_icon[]" accept=".png,.jpg,.jpeg,.gif,.svg" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Step Heading</label>
                            <input type="text" class="form-control" name="step_heading[]" placeholder="Step Heading..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label required">Step Sub Heading</label>
                            <input type="text" class="form-control" name="step_subheading[]" placeholder="Step Sub Heading..." required>
                        </div>
                        <div class="text-right">
                            <button type="button" class="btn btn-danger removeStep"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                `;
                $('#stepContainer').append(stepGroup);
            }

            // Add event listener to the "Add Step" button
            $('.addStep').click(function() {
                createStep();
            });

            // Remove step functionality (delegated event handler for dynamic content)
            $('#stepContainer').on('click', '.removeStep', function() {
                $(this).closest('.step-group').remove();
            });

            // Function to create new banner input fields
            function createBanner() {
                const bannerGroup = `
                    <div class="banner-group mb-4">
                        <input type="hidden" name="banners[]" value="banner">
                        <div class="mb-3">
                            <img src="{{ asset('assets/default.png') }}" height="50px" />
                             <br>
                            <label class="form-label mt-2">Banner Image</label>
                            <input type="file" class="form-control" name="banner_image[]" accept=".png,.jpg,.jpeg,.gif,.svg" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banner Information</label>
                            <select name="banner_info_active[]" class="form-control form-select" id="info_active">
                                <option value="1">Enable</option>
                                <option value="0">Disable</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Banner Heading</label>
                            <input type="text" class="form-control" name="banner_heading[]" placeholder="Banner Heading...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label ">Banner Description</label>
                            <textarea type="text" class="form-control summernote" name="banner_description[]" placeholder="Banner Description..." ></textarea>
                        </div>
                        <div class="text-right mb-3">
                            <button type="button" class="btn btn-danger removeBanner"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                `;
                $('#bannerContainer').append(bannerGroup);
            }

            // Add event listener to the "Add Banner" button
            $('.addBanner').click(function() {
                createBanner();
            });

            // Remove banner functionality (delegated event handler for dynamic content)
            $('#bannerContainer').on('click', '.removeBanner', function() {
                $(this).closest('.banner-group').remove();
            });
        });
    </script>
@endpush

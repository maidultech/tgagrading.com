@extends('admin.layouts.master')
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <style>
        .custom-img {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            width: 100px;
            height: 90px;
        }

        .dropify-wrapper {
            width: 500px !important;
        }

        .dropify-wrapper .dropify-message span.file-icon {
            font-size: 31px !important;
        }
    </style>
@endpush
@section('service', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@php
    $service = $data['service'];
@endphp

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
                                        <h3 class="card-title"> Edit Sports Card Grading Service</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.service.index') }}"
                                                class="btn btn-primary btn-sm btn-gradient">Back</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <form action="{{ route('admin.service.update', $service->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="thumb" class="form-lable">Featured Image <small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            600x600px)</strong></small></label>
                                                <input type="file" name="thumb" id="thumb" class="form-control"
                                                    accept=".jpg, .jpeg, .png, .webp"
                                                    data-allowed-file-extensions="jpg jpeg png webp">
                                                <img src="{{ asset($service->thumb) }}" class="mt-1" width="200px"
                                                    alt="">

                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="top_bg" class="form-lable">Banner Image <small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            1000x600px)</strong></small></label>
                                                <input type="file" name="top_bg" id="top_bg" class="form-control"
                                                    accept=".jpg, .jpeg, .png, .webp"
                                                    data-allowed-file-extensions="jpg jpeg png webp">
                                                <img src="{{ asset($service->top_bg) }}" class="mt-1" width="200px"
                                                    alt="">

                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="title" class="form-lable">Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="title" id="title"
                                                    value="{{ old('title', $service->title) }}" class="form-control"
                                                    placeholder="Enter your service title">
                                            </div>
                                        </div>
                                        {{-- <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="sub_title" class="form-lable">Subtitle <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="sub_title" id="sub_title"
                                                    value="{{ old('sub_title', $service->sub_title) }}" class="form-control"
                                                    placeholder="Enter your service sub title" required>
                                            </div>
                                        </div> --}}

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="status" class="form-lable">Status <span
                                                        class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1" {{ $service->status == 1 ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $service->status == 0 ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="top_links" class="form-lable">Banner Button Link</label>
                                                <input type="text" name="top_links" id="top_links"
                                                    value="{{ old('top_links', $service->top_links) }}" class="form-control"
                                                    placeholder="Enter Banner Button Link">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">
                                        {{-- <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="asking_title" class="form-lable">Asking Title</label>
                                                <input type="text" name="asking_title" id="asking_title"
                                                    value="{{ old('asking_title', $service->asking_title) }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="asking_link" class="form-lable">Asking Link</label>
                                                <input type="text" name="asking_link" id="asking_link"
                                                    value="{{ old('asking_link', $service->asking_link) }}"
                                                    class="form-control">
                                            </div>
                                        </div> --}}

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pricing_heading" class="form-lable">Pricing Title</label>
                                                <input type="text" name="pricing_heading" id="pricing_heading"
                                                    value="{{ old('pricing_heading', $service->pricing_heading) }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pricing_sub_heading" class="form-lable">Pricing
                                                    subtitle</label>
                                                <input type="text" name="pricing_sub_heading" id="pricing_sub_heading"
                                                    value="{{ old('pricing_sub_heading', $service->pricing_sub_heading) }}"
                                                    class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-lg-12 m-1">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" value="2"
                                                        name="type" checked id="typeCheckbox" disabled>
                                                    <label class="form-check-label" for="typeCheckbox">Footer Link</label>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" value="3" name="type">
                                        <input type="hidden" value="1" name="extra_fields">

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="status" class="form-lable">Short Description <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="details" id="summernote" class="summernote" cols="30" rows="5" class="form-control"
                                                    required>{{ old('details', $service->details) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Benefits of TGA Grading</h5>
                                            <div class="form-group">
                                                <label for="se_img" class="form-lable">Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="se_img" id="se_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->se_img) }}" class="mt-1" height="70px"
                                                    alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_title" class="form-lable">Title</label>
                                                <input type="text" name="se_title" id="se_title"
                                                    value="{{ old('se_title', $service->se_title) }}"
                                                    class="form-control" placeholder="Enter your title">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_link" class="form-lable">Button
                                                    Link</label>
                                                <input type="text" name="se_link" id="se_link"
                                                    value="{{ old('se_link', $service->se_link) }}"
                                                    placeholder="Enter button link" class="form-control">
                                            </div>

                                            <div class="form-group">
                                                <label for="se_details" class="form-lable">Details</label>
                                                <textarea type="text" name="se_details" id="se_details" class="summernote" class="form-control"
                                                    placeholder="Enter details">{{ old('se_details', $service->se_details) }}</textarea>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <h4 class="mt-5"> Explore Our Sports Card Grading Service Section</h4>
                                        <div class="form-group">
                                            <label for="sb_main_title" class="form-lable">Explore Section Title</label>
                                            <input type="text" name="sb_main_title" id="sb_main_title"
                                                value="{{ old('sb_main_title', $service->sb_main_title) }}"
                                                class="form-control" placeholder="Enter section title">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="sub_title" class="form-lable">Explore Section Subtitle</label>
                                            <input type="text" name="sub_title" id="sub_title"
                                                value="{{ old('sub_title', $service->sub_title) }}" class="form-control"
                                                placeholder="Enter section sub title">
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Explore Service Section 1</h5>
                                            <div class="form-group">
                                                <label for="sa_img" class="form-lable">SA Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sa_img" id="sa_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->sa_img) }}" class="mt-1" height="70px"
                                                    alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sa_title" class="form-lable">SA Title</label>
                                                <input type="text" name="sa_title" id="sa_title"
                                                    value="{{ old('sa_title', $service->sa_title) }}"
                                                    class="form-control" placeholder="Enter your service sub title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sa_link" class="form-lable">SA Button
                                                    Link</label>
                                                <input type="text" name="sa_link" id="sa_link"
                                                    value="{{ old('sa_link', $service->sa_link) }}"
                                                    placeholder="Enter button link" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sa_details" class="form-lable">SA Details</label>
                                                <textarea type="text" name="sa_details" id="sa_details" class="summernote" class="form-control"
                                                    placeholder="Enter sa details">{{ old('sa_details', $service->sa_details) }}</textarea>
                                            </div>

                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Explore Service Section 2</h5>
                                            <div class="form-group">
                                                <label for="sb_img" class="form-lable">SB Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sb_img" id="sb_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->sb_img) }}" class="mt-1" height="70px"
                                                    alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sb_title" class="form-lable">SB Title</label>
                                                <input type="text" name="sb_title" id="sb_title"
                                                    value="{{ old('sb_title', $service->sb_title) }}"
                                                    class="form-control" placeholder="Enter your sb title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sb_link" class="form-lable">SB Button
                                                    Link</label>
                                                <input type="text" name="sb_link" id="sb_link"
                                                    value="{{ old('sb_link', $service->sb_link) }}"
                                                    placeholder="Enter button link" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sb_details" class="form-lable">SB Details</label>
                                                <textarea type="text" name="sb_details" id="sb_details" class="summernote" class="form-control"
                                                    placeholder="Enter sb details">{{ old('sb_details', $service->sb_details) }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Explore Service Section 3</h5>
                                            <div class="form-group">
                                                <label for="sc_img" class="form-lable">SC Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sc_img" id="sc_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->sc_img) }}" class="mt-1" height="70px"
                                                    alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sc_title" class="form-lable">SC Title</label>
                                                <input type="text" name="sc_title" id="sc_title"
                                                    value="{{ old('sc_title', $service->sc_title) }}"
                                                    class="form-control" placeholder="Enter sc title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sc_link" class="form-lable">SC Button
                                                    Link</label>
                                                <input type="text" name="sc_link" id="sc_link"
                                                    value="{{ old('sc_link', $service->sc_link) }}"
                                                    placeholder="Enter button link" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sc_details" class="form-lable">SC Details</label>
                                                <textarea type="text" name="sc_details" id="sc_details" class="summernote"
                                                    value="{{ old('sc_details', $service->sa_details) }}" class="form-control" placeholder="Enter sc details">{{ old('sc_details', $service->sc_details) }}</textarea>
                                            </div>

                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Explore Service Section 4</h5>
                                            <div class="form-group">
                                                <label for="sd_img" class="form-lable">SD Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sd_img" id="sd_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->sd_img) }}" class="mt-1" height="70px"
                                                    alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sd_title" class="form-lable">SD Title</label>
                                                <input type="text" name="sd_title" id="sd_title"
                                                    value="{{ old('sd_title', $service->sd_title) }}"
                                                    class="form-control" placeholder="Enter sd title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sd_link" class="form-lable">SD Button
                                                    Link</label>
                                                <input type="text" name="sd_link" id="sd_link"
                                                    value="{{ old('sd_link', $service->sd_link) }}"
                                                    placeholder="Enter button link" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sd_details" class="form-lable">SD Details</label>
                                                <textarea type="text" name="sd_details" id="sd_details" class="summernote"
                                                    value="{{ old('sd_details', $service->sd_details) }}" class="form-control" placeholder="Enter sd details">{{ old('sd_details', $service->sd_details) }}</textarea>
                                            </div>

                                        </div>

                                        {{-- <div class="col-lg-4">
                                            <h5 class="mt-3">SE Section</h5>
                                            <div class="form-group">
                                                <label for="se_img" class="form-lable">SE Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="se_img" id="se_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->se_img) }}" class="mt-1" height="70px"
                                                    alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_title" class="form-lable">SE Title</label>
                                                <input type="text" name="se_title" id="se_title"
                                                    value="{{ old('se_title', $service->se_title) }}"
                                                    class="form-control" placeholder="Enter se title">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_link" class="form-lable">SE Button
                                                    Link</label>
                                                <input type="text" name="se_link" id="se_link"
                                                    value="{{ old('se_link', $service->se_link) }}"
                                                    placeholder="Enter button link" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_details" class="form-lable">SE Details</label>
                                                <textarea type="text" name="se_details" id="se_details" class="summernote" class="form-control"
                                                    placeholder="Enter sa details">{{ old('se_details', $service->se_details) }}</textarea>

                                            </div>

                                        </div> --}}
                                    </div>

                                    <h4 class="mt-5">Grading Subgrades & Criteria Section</h4>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="extra_title" class="form-lable">Section Title</label>
                                            <input type="text" name="extra_title" id="extra_title"
                                                placeholder="Enter Section Title"
                                                value="{{ old('extra_title', $service->serviceExtra?->extra_title) }}"
                                                class="form-control">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 1</h5>
                                            <div class="form-group">
                                                <label for="sf_img" class="form-lable">Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sf_img" id="sf_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->serviceExtra?->sf_img) }}" class="mt-1"
                                                    height="70px" alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sf_title" class="form-lable">Title</label>
                                                <input type="text" name="sf_title" id="sf_title"
                                                    value="{{ old('sf_title', $service->serviceExtra?->sf_title) }}"
                                                    class="form-control" placeholder="Enter title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sf_details" class="form-lable">Details</label>
                                                <textarea type="text" name="sf_details" id="sf_details" class="summernote" class="form-control"
                                                    placeholder="Enter details">{{ old('sf_details', $service->serviceExtra?->sf_details) }}</textarea>
                                            </div>

                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 2</h5>
                                            <div class="form-group">
                                                <label for="sg_img" class="form-lable">Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sg_img" id="sg_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->serviceExtra?->sg_img) }}" class="mt-1"
                                                    height="70px" alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sg_title" class="form-lable">Title</label>
                                                <input type="text" name="sg_title" id="sg_title"
                                                    value="{{ old('sg_title', $service->serviceExtra?->sg_title) }}"
                                                    class="form-control" placeholder="Enter title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sg_details" class="form-lable">Details</label>
                                                <textarea type="text" name="sg_details" id="sg_details" class="summernote" class="form-control"
                                                    placeholder="Enter details">{{ old('sg_details', $service->serviceExtra?->sg_details) }}</textarea>
                                            </div>

                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 3</h5>
                                            <div class="form-group">
                                                <label for="sh_img" class="form-lable">Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sh_img" id="sh_img"
                                                    class="form-control">
                                                <img src="{{ asset($service->serviceExtra?->sh_img) }}" class="mt-1"
                                                    height="70px" alt="">
                                            </div>
                                            <div class="form-group">
                                                <label for="sh_title" class="form-lable">Title</label>
                                                <input type="text" name="sh_title" id="sh_title"
                                                    value="{{ old('sh_title', $service->serviceExtra?->sh_title) }}"
                                                    class="form-control" placeholder="Enter title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sh_details" class="form-lable">Details</label>
                                                <textarea type="text" name="sh_details" id="sh_details" class="summernote" class="form-control"
                                                    placeholder="Enter details">{{ old('sh_details', $service->serviceExtra?->sh_details) }}</textarea>
                                            </div>

                                        </div>

                                    </div>

                                    <h5 class="mt-3">Meta Data</h5>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_title" class="form-lable">Meta Title</label>
                                                <input type="text" name="meta_title" id="meta_title"
                                                    value="{{ old('meta_title', $service->meta_title) }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_key" class="form-lable">Meta Key</label>
                                                <input type="text" name="meta_key" id="meta_key"
                                                    value="{{ old('meta_key', $service->meta_key) }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_description" class="form-lable">Meta Description</label>
                                                <input type="text" name="meta_description" id="meta_description"
                                                    value="{{ old('meta_description', $service->meta_description) }}"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        {{-- Schema Markup --}}
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="schema_markup" class="form-label">Schema Markup Code <small
                                                        class="text-primary">(Including script tag)</small></label>
                                                <textarea name="schema_markup" id="schema_markup" class="form-control" placeholder="Enter Schema Markup Code">{{ old('schema_markup', $service->schema_markup ?? '<script> </script>') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">Update</button>
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
    <script src="{{ asset('assets/js/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/tagify.polyfills.min.js') }}"></script>
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        var input = document.querySelector('.tags');
        // initialize Tagify on the above input node reference
        var tagify = new Tagify(input, {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        })
        $('.dropify').dropify();
    </script>
@endpush

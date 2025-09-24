@extends('admin.layouts.master')

@section('service', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <style>
        .dropify-wrapper {
            width: 500px !important;
        }

        .dropify-wrapper .dropify-message span.file-icon {
            font-size: 31px !important;
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
                                        <h3 class="card-title"> Create Blog Post</h3>
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
                                <form action="{{ route('admin.service.store') }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="thumb" class="form-lable">Featured Image <span
                                                        class="text-danger">*</span> <small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            600x600px)</strong></small>
                                                </label>
                                                <input type="file" name="thumb" id="thumb" class="form-control"
                                                    accept=".jpg, .jpeg, .png, .webp" required
                                                    data-allowed-file-extensions="jpg jpeg png webp">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="top_bg" class="form-lable">Banner Image <span
                                                        class="text-danger">*</span> <small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            1000x600px)</strong></small>
                                                </label>
                                                <input type="file" name="top_bg" id="top_bg" class="form-control"
                                                    accept=".jpg, .jpeg, .png, .webp" required
                                                    data-allowed-file-extensions="jpg jpeg png webp">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="title" class="form-lable">Title <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="title" id="title"
                                                    value="{{ old('title') }}" class="form-control"
                                                    placeholder="Enter your service title" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="sub_title" class="form-lable">Subtitle <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" name="sub_title" id="sub_title"
                                                    value="{{ old('sub_title') }}" class="form-control"
                                                    placeholder="Enter your service sub title" required>
                                            </div>
                                        </div>


                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="status" class="form-lable">Status <span
                                                        class="text-danger">*</span></label>
                                                <select name="status" id="status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="0">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="top_links" class="form-lable">Banner Button Link</label>
                                                <input type="text" name="top_links" id="top_links"
                                                    value="{{ old('top_links') }}" class="form-control"
                                                    placeholder="Enter Banner Button Link" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="sb_main_title" class="form-lable">Main Title</label>
                                                <input type="text" name="sb_main_title" id="sb_main_title"
                                                    value="{{ old('sb_main_title') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="asking_title" class="form-lable">Asking Title</label>
                                                <input type="text" name="asking_title" id="asking_title"
                                                    value="{{ old('asking_title') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="asking_link" class="form-lable">Asking Link</label>
                                                <input type="text" name="asking_link" id="asking_link"
                                                    value="{{ old('asking_link') }}" class="form-control">
                                            </div>
                                        </div>

                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pricing_heading" class="form-lable">Pricing Title</label>
                                                <input type="text" name="pricing_heading" id="pricing_heading"
                                                    value="{{ old('pricing_heading') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="pricing_sub_heading" class="form-lable">Pricing
                                                    subtitle</label>
                                                <input type="text" name="pricing_sub_heading" id="pricing_sub_heading"
                                                    value="{{ old('pricing_sub_heading') }}" class="form-control">
                                            </div>
                                        </div>


                                        <div class="col-lg-12 m-1">
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="type"
                                                        id="typeCheckbox">
                                                    <label class="form-check-label" value="2"
                                                        for="typeCheckbox">Footer Link</label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="status" class="form-lable">Description <span
                                                        class="text-danger">*</span></label>
                                                <textarea name="details" id="summernote" class="summernote" cols="30" rows="5" class="form-control"
                                                    required>{{ old('description') }}</textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Single Tab Section(SA)</h5>
                                            <div class="form-group">
                                                <label for="sa_img" class="form-lable">SA Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sa_img" id="sa_img"
                                                    value="{{ old('sa_img') }}" class="form-control"
                                                    placeholder="Enter your service sub title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sa_title" class="form-lable">SA Title</label>
                                                <input type="text" name="sa_title" id="sa_title"
                                                    value="{{ old('sa_title') }}" class="form-control"
                                                    placeholder="Enter your service sub title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sa_details" class="form-lable">SA Details</label>
                                                <textarea type="text" name="sa_details" id="sa_details" class="summernote" class="form-control"
                                                    placeholder="Enter sa details"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <h4 class="mt-3">Multiple Tab Section</h4>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 1</h5>
                                            <div class="form-group">
                                                <label for="sb_img" class="form-lable">SB Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sb_img" id="sb_img"
                                                    value="{{ old('sb_img') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sb_title" class="form-lable">SB Title</label>
                                                <input type="text" name="sb_title" id="sb_title"
                                                    value="{{ old('sb_title') }}" class="form-control"
                                                    placeholder="Enter your sb title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sb_details" class="form-lable">SB Details</label>
                                                <textarea type="text" name="sb_details" id="sb_details" class="summernote" class="form-control"
                                                    placeholder="Enter sb details"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 2</h5>
                                            <div class="form-group">
                                                <label for="sc_img" class="form-lable">SC Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sc_img" id="sc_img"
                                                    value="{{ old('sc_img') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sc_title" class="form-lable">SC Title</label>
                                                <input type="text" name="sc_title" id="sc_title"
                                                    value="{{ old('sc_title') }}" class="form-control"
                                                    placeholder="Enter sc title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sc_details" class="form-lable">SC Details</label>
                                                <textarea type="text" name="sc_details" id="sc_details" class="summernote" class="form-control"
                                                    placeholder="Enter sc details"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 3</h5>
                                            <div class="form-group">
                                                <label for="sd_img" class="form-lable">SD Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="sd_img" id="sd_img"
                                                    value="{{ old('sd_img') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="sd_title" class="form-lable">SD Title</label>
                                                <input type="text" name="sd_title" id="sd_title"
                                                    value="{{ old('sd_title') }}" class="form-control"
                                                    placeholder="Enter sd title">
                                            </div>
                                            <div class="form-group">
                                                <label for="sd_details" class="form-lable">SD Details</label>
                                                <textarea type="text" name="sd_details" id="sd_details" class="summernote" class="form-control"
                                                    placeholder="Enter sd details"></textarea>
                                            </div>
                                        </div>

                                        <div class="col-lg-4">
                                            <h5 class="mt-3">Tab 4</h5>
                                            <div class="form-group">
                                                <label for="se_img" class="form-lable">SE Image<small
                                                        class="text-info fw-bold"><strong>(Recommended size
                                                            300x300px)</strong></small></label>
                                                <input type="file" name="se_img" id="se_img"
                                                    value="{{ old('se_img') }}" class="form-control">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_title" class="form-lable">SE Title</label>
                                                <input type="text" name="se_title" id="se_title"
                                                    value="{{ old('se_title') }}" class="form-control"
                                                    placeholder="Enter se title">
                                            </div>
                                            <div class="form-group">
                                                <label for="se_details" class="form-lable">SE Details</label>
                                                <textarea type="text" name="se_details" id="se_details" class="summernote" class="form-control"
                                                    placeholder="Enter se details"></textarea>
                                            </div>
                                        </div>

                                    </div>
                                    <h5 class="mt-3">Meta Data</h5>
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_title" class="form-lable">Meta Title</label>
                                                <input type="text" name="meta_title" id="meta_title"
                                                    value="{{ old('meta_title') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_key" class="form-lable">Meta Key</label>
                                                <input type="text" name="meta_key" id="meta_key"
                                                    value="{{ old('meta_key') }}" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_description" class="form-lable">Meta Description</label>
                                                <input type="text" name="meta_description" id="meta_description"
                                                    value="{{ old('meta_description') }}" class="form-control">
                                            </div>
                                        </div>
                                        {{-- Schema Markup --}}
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="schema_markup" class="form-label">Schema Markup Code <small
                                                        class="text-primary">(Including script tag)</small></label>
                                                <textarea name="schema_markup" id="schema_markup" class="form-control" placeholder="Enter Schema Markup Code">{{ old('schema_markup', '<script> </script>') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">Save</button>
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
    <script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script>
        $('.dropify').dropify();
    </script>
@endpush

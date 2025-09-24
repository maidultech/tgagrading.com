@extends('admin.layouts.master')
@push('style')
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@3.8.2/build/jodit.min.css">

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
@section('blogDropdown', 'menu-open')
@section('blockDropdownMenu', 'd-block')
@section('blog-post', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@php
$row = $data['row'];
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
                                    <h3 class="card-title"> Edit Blog Post</h3>
                                </div>
                                <div class="col-6">
                                    <div class="float-right">
                                        <a href="{{ route('admin.blog-post.index') }}" class="btn btn-primary btn-sm btn-gradient">Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-4">
                            <form action="{{ route('admin.blog-post.update',$row->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="image" class="form-lable">Featured Image
                                                <br><small class="text-info fw-bold"><strong>(Recommended size 600x600px)</strong></small>
                                            </label>
                                            <input type="file" name="image" id="image" class="dropify" data-default-file="{{ getBlogPhoto($row->image) }}"
                                            data-allowed-file-extensions="jpg jpeg png webp" accept=".jpg, .jpeg, .png, .webp">
                                            {{-- <img class="custom-img mt-2" src="{{ getBlogPhoto($row->image) }}" alt="Paris" width="60" height="80"> --}}
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="title" class="form-lable">Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="title" value="{{ $row->title }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="slug" class="form-lable">Slug <span class="text-danger">*</span></label>
                                            <input type="text" name="slug" id="slug" value="{{ $row->slug }}" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="category" class="form-lable">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                 <option value="" class="d-none">-- Select Category --</option>
                                                @foreach($data['bog_category'] as $category)
                                                    <option value="{{ $category->id }}" {{ $row->category_id == $category->id? "selected" : "" }}>{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="status" class="form-lable">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{ $row->status == 1? "selected" : "" }}>Active</option>
                                                <option value="0" {{ $row->status == 0? "selected" : "" }}>Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="title" class="form-lable">Tags <span class="text-danger">*</span></label>
                                            <input type="text" name="tags[]" id="tags" value="
                                                @foreach(json_decode($row->tags,true) as $key => $value)
                                                    {{ $value }}
                                                @endforeach
                                            " class="form-control tags"data-role="tagsinput"required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="details" class="form-label">Description <span class="text-danger">*</span></label>
                                            <textarea name="details" id="jodit" cols="30" rows="5" class="form-control" required>{{ $row->details }}</textarea>
                                        </div>
                                           <h5 class="mt-3">Meta Data</h5>

                                    </div>

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_title" class="form-lable">Meta Title <small class="text-primary">(Max 60 characters)</small></label>
                                                <input type="text" name="meta_title" id="meta_title"
                                                    value="{{ old('meta_title', $row->meta_title) }}" placeholder="Enter Meta Title"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_key" class="form-lable">Meta Keywords</label>
                                                <input name="meta_keywords[]" type="text" id="meta_key" class="form-control tags" value="{{ old('meta_keywords', $row->meta_keywords) }}">

                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="meta_description" class="form-lable">Meta Description <small class="text-primary">(Max 160 characters)</small></label>
                                                <input type="text" name="meta_description" id="meta_description"
                                                    value="{{ old('meta_description', $row->meta_description) }}" placeholder="Enter Meta Description"
                                                    class="form-control">
                                            </div>
                                        </div>
                                        {{-- Schema Markup --}}
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="schema_markup" class="form-label">Schema Markup Code <small
                                                        class="text-primary">(Including script tag)</small></label>
                                                <textarea name="schema_markup" id="schema_markup" class="form-control"
                                                    placeholder="Enter Schema Markup Code">{{ old('schema_markup', $row->schema_markup ?? '<script> </script>') }}</textarea>
                                            </div>
                                        </div>
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">Update</button>
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
<script src="{{ asset('assets/js/tagify.js') }}"></script>
<script src="{{ asset('assets/js/tagify.polyfills.min.js') }}"></script>
<script type="text/javascript" src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
<!-- Jodit Editor CSS -->

<!-- Jodit Editor JS -->
<script src="https://cdn.jsdelivr.net/npm/jodit@3.8.2/build/jodit.min.js"></script>

<script>
    const editor = new Jodit('#jodit', {
        height: 300,
        uploader: { insertImageAsBase64URI: true }, // Optional: embed images directly
        toolbarAdaptive: false,
        readonly: false
    });
</script>


<script>
       var input = document.querySelector('.tags');
        // initialize Tagify on the above input node reference
        var tagify = new Tagify(input, {
        originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        })
        var input2 = document.querySelector('#meta_key');

        var tagify = new Tagify(input2, {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        });
        $('.dropify').dropify();
</script>
@endpush

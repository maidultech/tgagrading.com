@extends('admin.layouts.master')

@section('blogDropdown', 'menu-open')
@section('blockDropdownMenu', 'd-block')
@section('blog-post', 'active')
@section('title') {{ $data['title'] ?? 'Create Blog Post' }} @endsection

@push('style')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://jeremyfagis.github.io/dropify/dist/css/dropify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@3.8.2/build/jodit.min.css">

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
                                    <h3 class="card-title">Create Blog Post</h3>
                                </div>
                                <div class="col-6">
                                    <div class="float-right">
                                        <a href="{{ route('admin.blog-post.index') }}" class="btn btn-primary btn-sm btn-gradient">Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-4">
                            <form action="{{ route('admin.blog-post.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    {{-- Featured Image --}}
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="image">Featured Image <span class="text-danger">*</span>
                                                <br><small class="text-info fw-bold">(Recommended size 600x600px)</small>
                                            </label>
                                            <input type="file" name="image" id="image" class="dropify" required
                                                data-allowed-file-extensions="jpg jpeg png webp" accept=".jpg,.jpeg,.png,.webp">
                                        </div>
                                    </div>

                                    {{-- Title, Slug, Category, Status --}}
                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="title">Title <span class="text-danger">*</span></label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                placeholder="Enter your post title" value="{{ old('title') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="slug">Slug <span class="text-danger">*</span></label>
                                            <input type="text" name="slug" id="slug" class="form-control"
                                                value="{{ old('slug') }}" placeholder="Auto-generated or enter custom slug" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="category_id">Category <span class="text-danger">*</span></label>
                                            <select name="category_id" id="category_id" class="form-control" required>
                                                <option value="" class="d-none">-- Select Category --</option>
                                                @foreach ($data['bog_category'] as $category)
                                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-3">
                                        <div class="form-group">
                                            <label for="status">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Tags --}}
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label for="tags">Tags <span class="text-danger">*</span></label>
                                            <input type="text" name="tags[]" id="tags" class="form-control tags" required
                                                placeholder="Enter tags separated by comma" />
                                        </div>
                                    </div>

                                    {{-- Description --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="details">Description <span class="text-danger">*</span></label>
                                            <textarea name="details" id="jodit" cols="30" rows="5" class="form-control" required>{{ old('details') }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Meta Info --}}
                                    <div class="col-12">
                                        <h5 class="mt-3">Meta Data</h5>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="meta_title">Meta Title <small class="text-primary">(Max 60 characters)</small></label>
                                            <input type="text" name="meta_title" id="meta_title" class="form-control"
                                                value="{{ old('meta_title') }}" placeholder="Enter Meta Title">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="meta_key">Meta Keywords</label>
                                            <input type="text" name="meta_keywords[]" id="meta_key" class="form-control tags"
                                                value="{{ old('meta_keywords') }}" placeholder="Enter Meta Keywords">
                                        </div>
                                    </div>

                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <label for="meta_description">Meta Description <small class="text-primary">(Max 160 characters)</small></label>
                                            <input type="text" name="meta_description" id="meta_description" class="form-control"
                                                value="{{ old('meta_description') }}" placeholder="Enter Meta Description">
                                        </div>
                                    </div>

                                    {{-- Schema Markup --}}
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="schema_markup">Schema Markup Code <small class="text-primary">(Including &lt;script&gt; tag)</small></label>
                                            <textarea name="schema_markup" id="schema_markup" class="form-control"
                                                placeholder="Enter Schema Markup Code">{{ old('schema_markup', '<script> </script>') }}</textarea>
                                        </div>
                                    </div>

                                    {{-- Submit --}}
                                    <div class="col-lg-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </div>

                                </div> {{-- .row --}}
                            </form>
                        </div> {{-- .card-body --}}
                    </div> {{-- .card --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('assets/js/tagify.js') }}"></script>
    <script src="{{ asset('assets/js/tagify.polyfills.min.js') }}"></script>
    <script src="https://jeremyfagis.github.io/dropify/dist/js/dropify.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jodit@3.8.2/build/jodit.min.js"></script>

    <script>
        const editor = new Jodit('#jodit', {
            height: 300,
            uploader: { insertImageAsBase64URI: true },
            toolbarAdaptive: false,
            readonly: false
        });

        // Initialize tagify for both tag inputs
        new Tagify(document.querySelector('#tags'), {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        });

        new Tagify(document.querySelector('#meta_key'), {
            originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
        });

        $('.dropify').dropify();
    </script>
@endpush

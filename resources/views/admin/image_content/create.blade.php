@extends('admin.layouts.master')
@section('settings_menu', 'menu-open')
@section('image-contents', 'active')
@section('title')
    {{ __('Create Image Content') }}
@endsection

@push('style')
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="line-height: 36px;">{{ __('Image Content Info') }}</h3>
                                <a href="{{ route('admin.image-content.index') }}"
                                    class="btn bg-primary float-right d-flex align-items-center justify-content-center"><i
                                        class="fas fa-arrow-left"></i>&nbsp;{{ __('Back') }}</a>
                            </div>
                            <div class="row py-4 px-3 justify-content-center">
                                <div class="col-md-6">
                                    <form method="POST" action="{{ route('admin.image-content.store') }}" enctype="multipart/form-data">
                                        @csrf
                
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Image Content Name <span class="text-danger">*</span></label>
                                            <input name="name" id="name" placeholder="Write Image Content Name" type="text"
                                                class="form-control" value="{{ old('name') }}" required>
                                        </div>
                
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image Content Image <span class="text-danger">* Image
                                                    resulation ( 100px X 100px )</span></label>
                                            <input id="image" type="file" name="image" accept=".png, .jpg, .jpeg, .webp, .gif"
                                                class="form-control" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="link" class="form-label">Image Content Link</label>
                                            <input name="link" id="link" placeholder="Write Image Content Link" type="url"
                                                class="form-control" value="{{ old('link') }}">
                                        </div>
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option selected="" value="1">Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                
                                        <div>
                                            <button type="submit" class="btn btn-success">Save</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
@endpush

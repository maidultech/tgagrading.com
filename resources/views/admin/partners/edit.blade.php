@extends('admin.layouts.master')


@section('settings_menu', 'menu-open')
@section('partner', 'active')

@section('title') Program Partner @endsection

@push('style')
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
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
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="">
                                        <h5 class="card-title">Program Partner </h5>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('admin.partner.index') }}" class="btn btn-sm btn-primary btn-gradient">Back</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.partner.update', $partner->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="col-md-6 offset-md-3">
                                        <div class="">
                                            <div class="form-group ">
                                                <img src="{{ asset($partner->image ?? 'assets/default.png') }}" alt="Current Image" class="mt-2" width="100">
                                            </div>
                                        </div>
                                        <!-- Image -->
                                        <div class="mb-3">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>


                                        <!-- Title -->
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $partner->title ?? '') }}">
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label for="description" class="form-label">Description</label>
                                            <textarea type="text" class="form-control" id="description" name="description" >{{ old('description', $partner->description ?? '') }}</textarea>
                                        </div>

                                        <!-- Status -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{ $partner->status == 1 ? 'selected' : '' }}>Active</option>
                                                <option value="0" {{ $partner->status == 0 ? 'selected' : '' }}>Inactive</option>
                                            </select>
                                        </div>
                                        <div class="">
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

    </div>
@endsection

@push('script')

@endpush

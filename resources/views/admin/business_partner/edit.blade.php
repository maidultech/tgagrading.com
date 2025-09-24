@extends('admin.layouts.master')
@section('settings_menu', 'menu-open')
@section('business_partner', 'active')
@section('title') Business  Partner @endsection

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
                                 <div class="d-flex justify-content-between align-items-center w-100">
                                    <h5 class="m-0">Business Partner Edit</h5>
                                    <a href="{{ route('admin.business-partner.index') }}" class="btn btn-primary btn-gradient btn-sm">Back</a>
                                </div>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.business-partner.update',$partner->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="col-md-6 offset-md-3">
                                        <div class="">
                                            @if($partner->logo)
                                            <div class="form-group ">
                                                <img src="{{ asset($partner->logo) }}" alt="Current Image" class="mt-2" width="100">
                                            </div>
                                            @else
                                            <div class="form-group ">
                                                <img src="{{ asset('assets/default.png') }}" alt="Current Image" class="mt-2" width="100">
                                            </div>
                                            @endif

                                        </div>
                                        <!-- logo -->
                                        <div class="mb-3 ">
                                            <label for="logo" class="form-label">Logo <span class="text-danger">*</span> <span class="text-info"> [Recommened size 320 * 270] </span> </label>
                                            <input type="file" class="form-control" id="logo" name="logo">
                                        </div>
                                        <!-- Company name -->
                                        <div class="mb-3 ">
                                            <label for="company_name" class="form-label">Company Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="company_name" name="company_name" value="{{ old('company_name', $partner->company_name) }}">
                                        </div>
                                        <!-- Company url -->
                                        <div class="mb-3 ">
                                            <label for="company_url" class="form-label">Company Url <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="company_url" name="company_url" value="{{ old('company_url', $partner->company_url) }}">
                                        </div>

                                        <!-- Details-->
                                        <div class="mb-3 ">
                                            <label for="details" class="form-label">Details</label>
                                            <textarea type="text" class="form-control" id="details" name="details" >{!! old('details',$partner->details) !!}</textarea>
                                        </div>
                                         <!-- Status -->
                                        <div class="mb-3">
                                            <label for="status" class="form-label">Status</label>
                                            <select name="status" id="status" class="form-control">
                                                <option value="1" {{ $partner->status==1?'selected':"" }} >Active</option>
                                                <option value="0" {{ $partner->status==0?'selected': "" }}  >Inactive</option>
                                            </select>
                                        </div>
                                         <!-- Company url -->
                                        <div class="mb-3 ">
                                            <label for="order_id" class="form-label">Order Number </label>
                                            <input type="text" class="form-control" id="order_id" name="order_id" value="{{ old('order_id', $partner->order_id) }}">
                                        </div>
                                        <div class="text-right">
                                            <button type="submit" class="btn btn-primary">Update</button>
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

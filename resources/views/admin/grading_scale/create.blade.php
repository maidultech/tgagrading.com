@extends('admin.layouts.master')
@section('grading_scale', 'active')
@section('title') {{ $title ?? 'Create Grading Scale' }} @endsection
@push('style')
{{-- <style>
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
</style> --}}
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
                                    <h3 class="card-title">{{ $title }}</h3>
                                </div>
                                <div class="col-6">
                                    <div class="float-right">
                                        <a href="{{ route('admin.grading-scale.index') }}" class="btn btn-primary btn-gradient btn-sm">{{__('messages.common.back')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive p-4">
                            <form action="{{ route('admin.grading-scale.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="row d-flex justify-content-center">
                                    <div class="col-lg-8">
                                        {{-- <div class="hr-text col-lg-12">Information in English</div> --}}
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="title" class="form-lable">Title <span class="text-success font-bold">*</span></label>
                                                <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                                            </div>
                                        </div>

                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="body" class="form-lable">body <span class="text-success font-bold">*</span></label>
                                                <textarea name="body" id="body" cols="30" rows="5"
                                                    class="form-control" required style="height: 150px !important;">{{ old('body') }}</textarea>
                                            </div>
                                        </div>
                                        {{-- <div class="hr-text col-lg-12">Information in German</div> --}}
  
                                        {{-- <div class="col-12">
                                            <div class="form-group">
                                                <label for="is_active" class="form-lable">{{__('messages.common.status')}}</label>
                                                <select name="is_active" id="is_active"  class="form-control">
                                                    <option value="1">{{__('messages.common.active')}}</option>
                                                    <option value="2">{{__('messages.common.inactive')}}</option>
                                                </select>
                                            </div>
                                        </div> --}}

                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">Save</button>
                                            </div>
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

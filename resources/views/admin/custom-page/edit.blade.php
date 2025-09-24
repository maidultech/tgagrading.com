@extends('admin.layouts.master')
@section('cpage', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection
@push('style')
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <style>
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
                                        <h3 class="card-title"> {{ $data['title'] ?? '' }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.cpage.index') }}" class="btn btn-primary btn-gradient btn-sm">{{__('messages.common.back')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <form action="{{ route('admin.cpage.update', $row->id) }}" method="post"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="title" class="form-lable">{{__('messages.seo.page_name')}}</label>
                                                <input type="text" name="title" id="title" class="form-control"
                                                    required value="{{ $row->title }}" placeholder="{{__('messages.seo.page_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="slug" class="form-lable">{{__('messages.seo.page_slug')}}</label>
                                                <input type="text" name="slug" id="slug" readonly placeholder="Page Slug"
                                                    class="form-control" required value="{{ $row->url_slug }}">
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <label for="status" class="form-lable">Status</label>
                                                <select name="status" id="status" class="form-control" @if($row->is_editable == 0) disabled @endif>
                                                    <option value="1" {{ $row->is_active == 1 ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $row->is_active == 0 ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                                <div class="form-group">
                                                    <label for="status" class="form-lable">{{__('messages.common.description')}} <span class="text-danger">*</span></label>
                                                    <textarea name="body" cols="30" rows="5" class="form-control summernote">{!! $row->body !!}</textarea>
                                                </div>
                                            </div>
                                        <div class="hr-text col-lg-12">{{__('messages.custom_page.meta_info')}}</div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="meta_title" class="form-lable">{{__('messages.seo.meta_title')}}
                                                    <span class="ml-2 text-info">( {{__('messages.seo.meta_title_recommend')}} )</span>
                                                </label>
                                                <input type="text" name="meta_title" id="meta_title" placeholder="{{__('messages.seo.meta_title')}}"
                                                    class="form-control" value="{{ $row->meta_title }}" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label for="meta_keywords" class="form-lable">{{__('messages.seo.meta_keywords')}}</label>
                                                <input type="text" name="meta_keywords" id="meta_keywords" placeholder="{{__('messages.seo.meta_keywords')}}"
                                                    class="form-control tags" value="{{ $row->meta_keywords }}" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="meta_description" class="form-lable">{{__('messages.seo.meta_desc')}}
                                                    <span class="ml-2 text-info">( {{__('messages.seo.meta_description_recommend')}} )</span>
                                                </label>
                                                <textarea name="meta_description" cols="30" rows="5" id="meta_description"
                                                    class="form-control" placeholder="{{__('messages.seo.meta_desc')}}">{{$row->meta_description}}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">{{__('messages.common.update')}}</button>
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
<script>
    var input = document.querySelector('.tags');
    var tagify = new Tagify(input, {
    originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
    })
</script>
@endpush

@extends('admin.layouts.master')
@section('cpage', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@php
    $row = $data['row'];
@endphp

@push('style')
<style>
    td {
        width: 0;
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
                                    <h3 class="card-title"> {{ $data['title'] ?? '' }}</h3>
                                </div>
                                <div class="col-6">
                                    <div class="float-right">
                                        <a href="{{ route('admin.cpage.index') }}" class="btn btn-primary btn-gradient btn-sm">Back</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive px-2">            
                            <table class="table">
                                <tr>
                                    <td style="width:10%; border-top: 0px;">Page Name :</td>
                                    <td style="border-top: 0px;">{{ $row->title }}</td>
                                </tr>
                                <tr>
                                    <td>Page Slug :</td>
                                    <td>{{ $row->url_slug }}</td>
                                </tr>
                                <tr>
                                    <td>Status :</td>
                                    <td>
                                        @if ($row->is_active == 1)
                                            <span class="text-success">Active</span>
                                        @else
                                            <span class="text-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td  colspan="2">Description :
                                        <br>
                                        {!! $row->body !!}
                                    </td>
                                </tr>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

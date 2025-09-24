@extends('admin.layouts.master')
@section('grading_scale', 'active')
@section('title') {{ $title ?? 'Grading Scale View List' }} @endsection

@push('style')
<style>
    td {
        width: 0;
    }
</style>
@endpush

{{-- @php
$row = $data['row'];
@endphp --}}

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
                                        <h3 class="card-title">{{ $title ?? 'Grading Scale View List' }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.grading-scale.index') }}" class="btn btn-primary btn-gradient btn-sm">{{__('messages.common.back')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <table class="table">
                                    <tr>
                                        <td style="width:15%;">Title</td>
                                        <td>{{ $row->title }}</td>
                                    </tr>
                                    <tr>
                                        <td>Body</td>
                                        <td>{!! $row->body !!}</td>
                                    </tr>
                                    <tr>
                                        <td>Order Number</td>
                                        <td>
                                            <span class="text-info">{{ $row->order_id }}</span>
                                        </td>
                                    </tr>
                                    {{-- <tr>
                                        <td>{{__('messages.common.status')}} :</td>
                                        <td>
                                            @if ($row->is_active == 1)
                                                <span class="text-success">Active</span>
                                            @else
                                                <span class="text-danger">Inactive</span>
                                            @endif
                                        </td>
                                    </tr> --}}
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

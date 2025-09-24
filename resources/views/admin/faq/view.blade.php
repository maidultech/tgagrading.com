@extends('admin.layouts.master')
@section('faq', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@push('style')
<style>
    td {
        width: 0;
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
                                        <h3 class="card-title">{{ $data['title'] ?? '' }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.faq.index') }}" class="btn btn-primary btn-gradient btn-sm">{{__('messages.common.back')}}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-4">
                                <table class="table">
                                    <tr>
                                        <td style="width:15%;">Service</td>
                                        <td>
                                            @forelse($data['selectedServices'] as $service)
                                                <li>{{ $service->title }}</li>
                                            @empty
                                               <span class="text-danger" >No services selected.</span>
                                            @endforelse
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:15%;">{{__('messages.common.question')}}</td>
                                        <td>{{ $row->title }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.common.answer')}}</td>
                                        <td>{!! $row->body !!}</td>
                                    </tr>
                                    <tr>
                                        <td>{{__('messages.common.status')}} :</td>
                                        <td>
                                            @if ($row->is_active == 1)
                                                <span class="text-success">Active</span>
                                            @else
                                                <span class="text-danger">Inactive</span>
                                            @endif
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

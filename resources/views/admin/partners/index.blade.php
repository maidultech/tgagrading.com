@extends('admin.layouts.master')
@section('title') Program Partner @endsection
@section('settings_menu', 'menu-open')
@section('partner', 'active')


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
                                        <h3 class="card-title">Manage Program Partner</h3>
                                    </div>
                                    {{-- <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.partner.create') }}" class="btn btn-primary btn-sm">Add New</a>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th style="width:15%;" class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($partners as $key => $partner)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    <img src="{{ asset($partner->image) }}" alt="Image" width="100">
                                                </td>
                                                <td>{{ $partner->title }}</td>
                                                <td>{{ $partner->description }}</td>
                                                <td>
                                                    @if ($partner->status == 1)
                                                        <span class="text-success">Active</span>
                                                    @else
                                                        <span class="text-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    @if (Auth::user()->can('admin.partner.edit'))
                                                        <div class="dropdown">
                                                            <button
                                                                class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient"
                                                                type="button" data-toggle="dropdown" aria-expanded="false">
                                                                {{ __('messages.common.actions') }}
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a href="{{ route('admin.partner.edit', $partner->id) }}"
                                                                    class="dropdown-item"><i class="fa fa-pencil"></i>
                                                                    {{ __('messages.common.edit') }}</a>
                                                            </div>
                                                        </div>
                                                    @endif

                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

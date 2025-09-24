@extends('admin.layouts.master')
@section('settings_menu', 'menu-open')
@section('image-contents', 'active')
@section('title')
    {{ $data['title'] ?? 'Image Content List' }}
@endsection

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="line-height: 36px;">Image Content List</h3>
                                @if (Auth::user()->can('admin.image-content.create'))
                                    <a href="{{ route('admin.image-content.create') }}"
                                        class="btn bg-success float-right d-flex align-items-center justify-content-center">
                                        <i class="fas fa-plus"></i>&nbsp; Add Image Content
                                    </a>
                                @endif
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped text-center">
                                    <thead>
                                        <tr>
                                            <th>SN.</th>
                                            <th>Name</th>
                                            <th>Iamge</th>
                                            <th>Link</th>
                                            <th>Status</th>
                                            <th>Order No</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                        <tr>
                                            <th scope="row">{{ $key + 1 }}</th>
                                            <td>{{ $row->name }}</td>
                                            <td>
                                                <a href="{{ asset($row->image) }}" target="__blank">
                                                    <img src="{{ asset($row->image) }}" alt="Image" style="width: 120px; height: 120px; object-fit: contain;">
                                                </a>
                                            </td>
                                            <td>{{ $row->link }}</td>
                                            <td>
                                                @if ($row->status == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>{{ $row->order_id }}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        {{__('messages.common.actions')}}
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @if (Auth::user()->can('admin.image-content.edit'))
                                                            <a href="{{ route('admin.image-content.edit', $row->id) }}" class="dropdown-item"><i class="fa fa-pencil"></i> {{__('messages.common.edit')}}</a>
                                                        @endif
                                                        @if (Auth::user()->can('admin.image-content.delete'))
                                                            <a href="{{route('admin.image-content.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('messages.common.delete')}}</a>
                                                        @endif
                                                    </div>
                                                </div>
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

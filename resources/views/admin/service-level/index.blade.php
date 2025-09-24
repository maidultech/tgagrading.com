@extends('admin.layouts.master')
@section('service-level', 'active')

@section('title') {{ $title ?? '' }} @endsection

@section('content')
    <div class="content-wrapper">
       
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                        <h4 class="card-title">{{ $title ?? __('messages.common.plan') }}</h4>
                                    </div>
                                    @if (Auth::user()->can('admin.service-level.create'))
                                    <div class="">
                                        <a href="{{ route('admin.service.level.create') }}" class="btn btn-sm btn-primary btn-gradient">Add New</a>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body p-0 table-responsive">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Estimated Days</th>
                                            <th>Status</th>
                                            <th>Order ID</th>
                                            <th>Extra Price</th>
                                            <th>{{__('messages.common.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>Name</th>
                                            <th>Estimated Days</th>
                                            <th>Status</th>
                                            <th>Order ID</th>
                                            <th>Extra Price</th>
                                            <th>{{__('messages.common.actions')}}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($plan as $key => $row)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$row->name}}</td>
                                            <td>{{$row->estimated_days}} days</td>
                                            <td>
                                                @if($row->status == 1)
                                                    <span class="text-success">{{__('messages.common.active')}}</span>
                                                @else
                                                    <span class="text-danger">{{__('messages.common.deactive')}}</span>
                                                @endif
                                            </td>
                                            <td>{{$row->order_id}}</td>
                                            <td>{{$row->extra_price}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        {{__('messages.common.actions')}}
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @if (Auth::user()->can('admin.service-level.edit'))
                                                        <a href="{{ route('admin.service.level.edit', $row->id) }}"  class="dropdown-item"><i class="fa fa-pencil"></i> {{__('messages.common.edit')}}</a>
                                                        @endif
                                                        @if (Auth::user()->can('admin.service-level.delete'))
                                                        <a href="{{route('admin.service.level.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('messages.common.delete')}}</a>
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

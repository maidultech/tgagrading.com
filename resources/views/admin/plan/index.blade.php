@extends('admin.layouts.master')
@section('plan', 'active')

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
                                    <div class="">
                                        @if (Auth::user()->can('admin.plan.create'))
                                        <a href="{{ route('admin.plan.create') }}" class="btn btn-sm btn-primary btn-gradient">{{__('Add New')}}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{__('messages.plan.plan_name')}}</th>
                                            <th>Type</th>
                                            <th>{{__('messages.common.price')}}</th>
                                            <th>Minimum Card</th>
                                            <th>{{__('messages.common.status')}}</th>
                                            <th>Popular Badge</th>
                                            <th>Order No</th>
                                            <th>{{__('messages.common.actions')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>SN</th>
                                            <th>{{__('messages.plan.plan_name')}}</th>
                                            <th>Type</th>
                                            <th>{{__('messages.common.price')}}</th>
                                            <th>Minimum Card</th>
                                            <th>{{__('messages.common.status')}}</th>
                                            <th>Badge</th>
                                            <th>Order No</th>
                                            <th>{{__('messages.common.actions')}}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($plan as $key => $row)
                                        <tr>
                                            <td>{{$key+1}}</td>
                                            <td>{{$row->name}}</td>
                                            <td>{{ ucwords($row->type) }}</td>
                                            <td>{{getDefaultCurrencySymbol()}}{{$row->price}}</td>
                                            <td>{{$row->minimum_card}}</td>
                                            <td>
                                                @if($row->status == 1)
                                                    <span class="text-success">{{__('messages.common.active')}}</span>
                                                @else
                                                    <span class="text-danger">{{__('messages.common.deactive')}}</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($row->is_badge == 'popular')
                                                    <span class="text-success">{{__('Popular')}}</span>
                                                @elseif($row->is_badge == 'custom')
                                                    <span class="text-success">{{__('Custom')}}</span>
                                                @else
                                                    <span class="text-danger">{{__('No')}}</span>
                                                @endif
                                            </td>
                                            <td>{{$row->order_number}}</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                        data-toggle="dropdown" aria-expanded="false">
                                                        {{__('messages.common.actions')}}
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        @if (Auth::user()->can('admin.plan.edit'))
                                                        <a href="{{ route('admin.plan.edit', $row->id) }}"  class="dropdown-item"><i class="fa fa-pencil"></i> {{__('messages.common.edit')}}</a>
                                                        @endif
                                                        @if (Auth::user()->can('admin.plan.delete'))
                                                        <a href="{{route('admin.plan.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('messages.common.delete')}}</a>
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

@extends('admin.layouts.master')
@section('logs', 'active')

@section('title') {{ $title ?? '' }} @endsection

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="line-height: 36px;">Log History</h3>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SN</th>
                                            <th width="35%">User</th>
                                            <th width="35%">Created At</th>
                                            <th width="25%">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SN</th>
                                            <th width="35%">User</th>
                                            <th width="35%">Created At</th>
                                            <th width="25%">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td>
                                                    @if($row->is_admin == 1)
                                                    <a href="{{route('admin.user.index')}}">{{ $row->user?->name }}</a>
                                                    @else
                                                    <a href=""{{ route('admin.customer.index', ['openModal' => $row->user_id]) }}"">{{ $row->user?->name.' '.$row->user?->last_name }}</a>
                                                    @endif
                                                </td>
                                                <td>{{ date('d M, Y', strtotime($row->created_at)) }}</td>
                                                <td>{{ $row->action }}</td>
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

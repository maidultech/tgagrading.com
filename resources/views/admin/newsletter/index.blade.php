@extends('admin.layouts.master')
@section('subscribers', 'active')

@section('title') {{ $title ?? '' }} @endsection

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title" style="line-height: 36px;">Subscribers list</h3>
                            </div>
                            <div class="card-body p-0 table-responsive">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SN</th>
                                            <th width="60%">Email</th>
                                            <th width="20%">Subscriptions Date</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SN</th>
                                            <th width="60%">Email</th>
                                            <th width="20%">Subscriptions Date</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>
                                                <td>{{$key+1}}</td>
                                                <td><a href="mailto:{{ $row->email }}">{{ $row->email }}</a></td>
                                                <td>{{ date('d M, Y', strtotime($row->created_at)) }}</td>
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

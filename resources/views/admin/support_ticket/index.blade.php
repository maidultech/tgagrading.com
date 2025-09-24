@extends('admin.layouts.master')

@section('support', 'active')
@section('title') Support Ticket @endsection
@push('style')
    <style>
        .status-select {
            padding: 2px 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: transparent;
            color: #FFFFFF !important;
        }
        .status-select:focus-visible {
            outline: none;
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
                                        <h3 class="card-title">Manage Support Ticket </h3>
                                    </div>

                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th width="10%">SN</th>
                                        <th width="15%">Subject</th>
                                        <th width="15%">Priority</th>
                                        {{-- <th width="15%">Message</th> --}}
                                        <th width="15%">Status</th>
                                        <th width="15%">Date</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                    </thead>
                                    <tfoot>
                                    <tr>
                                        <th width="10%">SN</th>
                                        <th width="15%">Subject</th>
                                        <th width="15%">Priority</th>
                                        {{-- <th width="15%">Message</th> --}}
                                        <th width="15%">Status</th>
                                        <th width="15%">Date</th>
                                        <th width="15%">Action</th>
                                    </tr>
                                    </tfoot>
                                    <tbody>
                                    @if ($tickets->count() > 0)
                                        @foreach ($tickets as $key => $row)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>{{ $row->subject }}</td>
                                                <td>
                                                    @if($row->priority == 1)
                                                        Low
                                                    @elseif($row->priority == 2)
                                                        Medium
                                                    @else
                                                        High
                                                    @endif
                                                </td>
                                                {{-- <td>{{ \Illuminate\Support\Str::limit($row->message,100,"...") }}</td> --}}
                                                <td>
                                                    @if ($row->status == 1)
                                                        <span class="badge badge-success">Open</span>
                                                    @elseif($row->status == 0)
                                                        <span class="badge badge-warning">Pending</span>
                                                    @else
                                                        <span class="badge badge-danger">Close</span>
                                                    @endif
                                                </td>

                                                <td>{{ date('M d, Y', strtotime($row->created_at)) }}</td>

                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                                data-toggle="dropdown" aria-expanded="false">
                                                            {{__('messages.common.actions')}}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if (Auth::user()->can('admin.support-ticket.edit'))
                                                                <a href="{{route('admin.support-ticket.show', $row->id)}}" class="dropdown-item"><i class="fas fa-reply"></i> Reply</a>
                                                            @endif

                                                            @if (Auth::user()->can('admin.support-ticket.view'))
                                                                <a href="javascript:void(0)" class="dropdown-item view" data-route="{{ route('admin.support-ticket.view', $row->id) }}"><i class="fa fa-eye"></i> {{__('messages.common.view')}}</a>
                                                            @endif

                                                            @if (Auth::user()->can('admin.support-ticket.delete'))
                                                                <a href="{{route('admin.support-ticket.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('messages.common.delete')}}</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- edit modal --}}
    <div class="modal fade" id="viewContactModal" tabindex="-1" aria-labelledby="viewContactModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h5 class="modal-title" id="viewContactModalLabel">Support Ticket View</h5>
                    <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal_body">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script type="text/javascript">
        $(document).on('click', '.view', function() {
            let route = $(this).data('route');
            $.get(route, function(data) {
                console.log(data);
                $('#viewContactModal').modal('show');
                $('#modal_body').html(data);
            });
        });
    </script>
@endpush

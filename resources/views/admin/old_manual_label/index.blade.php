@extends('admin.layouts.master')
@section('manual_label', 'active')
@section('title'){{ $title ?? 'Manage Manual label' }} @endsection

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
                                        <h3 class="card-title">Manage Manual label </h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            {{-- @if (Auth::user()->can('admin.manual-label.create')) --}}
                                                <a href="{{ route('admin.manual-label.create') }}" class="btn btn-primary btn-gradient btn-sm">Add New</a>
                                            {{-- @endif --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                 <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>SN</th>                                            
                                            <th>Cert Number</th>
                                            <th>Year</th>
                                            <th>Brand</th>                                         
                                            <th>Card #</th>
                                            <th>Player/Card Name</th>
                                            <th>Notes</th>
                                            <th>Grade</th>
                                            <th>Grade name</th>
                                            <th>Created By</th>
                                            <th>Updated By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>

                                    <tfoot>
                                        <tr>
                                            <th>SN</th>                                            
                                            <th>Cert Number</th>
                                            <th>Year</th>
                                            <th>Brand</th>                                         
                                            <th>Card #</th>
                                            <th>Player/Card Name</th>
                                            <th>Notes</th>
                                            <th>Grade</th>
                                            <th>Grade name</th>
                                            <th>Created By</th>
                                            <th>Updated By</th>
                                            <th>Action</th>
                                        </tr>
                                 
                                    </tfoot>

                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>
                                                <td>{{ $key+1 }}</td>
                                                <td>{{ $row->card_number }}</td>
                                                <td>{{ $row->year }}</td>
                                                <td>{{ $row->brand_name }}</td>
                                                <td>{{ $row->card }}</td>
                                                <td>{{ $row->card_name }}</td>
                                                <td>{{ $row->notes }}</td>
                                                <td>{{ $row->grade }}</td>
                                                <td>{{ $row->grade_name }}</td>
                                                <td>
                                                    {{ $row->createdBy?->name ?? 'Admin' }} 
                                                    <br><span style="color: #006fe5">on {{$row->created_at?->format('d M, Y h:i:s a') ?? ''}}</span>
                                                </td>
                                                <td>
                                                @if($row->updatedBy)
                                                    {{ $row->updatedBy?->name ?? 'Admin' }} 
                                                    <br><span style="color: #006fe5">on {{$row->updated_at?->format('d M, Y h:i:s a') ?? ''}}</span>
                                                @endif
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                            {{__('messages.common.actions')}}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            {{-- @if (Auth::user()->can('admin.manual-label.view'))
                                                            <a href="{{ route('admin.manual-label.view', $row->id) }}" class="dropdown-item"><i class="fa fa-eye"></i> {{__('messages.common.view')}}</a>
                                                            @endif --}}
                                                            {{-- @if (Auth::user()->can('admin.manual-label.download')) --}}
                                                            <a href="{{ route('admin.manual-label.download', $row->id) }}" class="dropdown-item"><i class="fa fa-download"></i> {{__('messages.common.download')}}</a>
                                                            {{-- @endif --}}

                                                            @if (Auth::user()->can('admin.manual-label.edit'))
                                                            <a href="{{ route('admin.manual-label.edit', $row->id) }}" class="dropdown-item"><i class="fa fa-pencil"></i> {{__('messages.common.edit')}}</a>
                                                            @endif

                                                            @if (Auth::user()->can('admin.manual-label.delete'))
                                                            <a href="{{route('admin.manual-label.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('messages.common.delete')}}</a>
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

@push('script')
@endpush

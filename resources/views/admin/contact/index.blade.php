@extends('admin.layouts.master')

@section('contact', 'active')
@section('title') {{ $data['title'] ?? __('messages.common.contact') }} @endsection

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
                                        <h3 class="card-title">{{__('messages.contact.manage_contact')}} </h3>
                                    </div>

                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                 <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SN</th>
                                            <th width="10%">{{__('messages.common.name')}}</th>
                                            <th width="10%">{{__('messages.common.email')}}</th>
                                            <th width="10%">{{__('Phone')}}</th>
                                            <th width="15%">{{__('messages.common.message')}}</th>
                                            <th width="15%">{{__('Type')}}</th>
                                            <th width="10%">{{__('messages.common.date')}}</th>
                                            <th width="10%">{{__('messages.common.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SN</th>
                                            <th width="10%">{{__('messages.common.name')}}</th>
                                            <th width="10%">{{__('messages.common.email')}}</th>
                                            <th width="10%">{{__('Phone')}}</th>
                                            <th width="15%">{{__('messages.common.message')}}</th>
                                            <th width="15%">{{__('Type')}}</th>
                                            <th width="10%">{{__('messages.common.date')}}</th>
                                            <th width="10%">{{__('messages.common.action')}}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @if (isset($data['rows']) && count($data['rows']) > 0)
                                            @foreach ($data['rows'] as $key => $row)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $row->name }}</td>
                                                    <td><a href="mailto:{{ $row->email }}">{{ $row->email }}</a></td>
                                                    <td><a href="tel: {{ $row->phone ?? 'N/A' }}">{{ $row->phone ?? '' }}</a></td>

                                                    <td>{{ \Illuminate\Support\Str::limit($row->message,100,"...") }}</td>
                                                    <td>
                                                        @if($row->contact_type == 1)
                                                            <span class="text-primary">Contact Sale</span>
                                                        @else
                                                            <span class="text-success">General</span>
                                                        @endif
                                                    </td>

                                                    <td>{{ date('M d, Y', strtotime($row->created_at)) }}</td>

                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                                data-toggle="dropdown" aria-expanded="false">
                                                                {{__('messages.common.actions')}}
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                @if (Auth::user()->can('admin.contact.view'))
                                                                    <a href="javascript:void(0)" class="dropdown-item view" data-toggle="modal" data-target="#view{{ $row->id }}"><i class="fa fa-eye"></i> {{__('messages.common.view')}}</a>
                                                                @endif

                                                                @if (Auth::user()->can('admin.contact.delete'))
                                                                    <a href="{{route('admin.contact.delete', $row->id)}}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> {{__('messages.common.delete')}}</a>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>


                                            <!-- View Modal -->
                                            <div class="modal fade" id="view{{ $row->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary">
                                                            <h5 class="modal-title">View Contact List</h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="view_modal_content">
                                                                <label>Name : </label>
                                                                <span class="text-dark">{{ $row->name }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Email : </label>
                                                                <a href="mailto: {{ $row->email }}" class="text-info">{{ $row->email }}</a>
                                                            </div>
                                                            @if ($row->phone)
                                                            <div class="view_modal_content">
                                                                <label>Phone : </label>
                                                                <a href="tel:{{ $row->phone }}" class="text-info">{{ $row->phone  }}</a>
                                                            </div>
                                                            @endif

                                                            <div class="view_modal_content">
                                                                <label>Contact Type : </label>
                                                                <span class="text-dark">
                                                                    @if($row->contact_type == 1)
                                                                        Contact Sale
                                                                    @else
                                                                        General
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            @if ($row->customer_number)
                                                            <div class="view_modal_content">
                                                                <label>TGA Customer Number : </label>
                                                                <span class="text-dark"> {{ $row->customer_number }} </span>
                                                            </div>
                                                            @endif
                                                            <div class="view_modal_content">
                                                                <label>Date : </label>
                                                                <span class="text-dark"> {{ date('d M, Y', strtotime($row->created_at)) }} </span>
                                                            </div>

                                                            <div class="message_content">
                                                                <label>Message : </label>
                                                                <span class="text-dark"> {{ $row->message }} </span>
                                                            </div>

                                                        </div>

                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
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
{{-- <div class="modal fade" id="viewContactModal" tabindex="-1" aria-labelledby="viewContactModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewContactModalLabel">{{__('messages.contact.view_contact')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="modal_body"></div>

            </div>
        </div>
    </div>
</div> --}}

@endsection

@push('script')
{{-- <script type="text/javascript">
    $(document).on('click', '.view', function() {
        let cat_id = $(this).data('id');
        $.get('contact/'+cat_id+'/view', function(data) {
            console.log(data);
            $('#viewContactModal').modal('show');
            $('#modal_body').html(data);
        });
    });
</script> --}}
@endpush

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
                                            <th>Uploaded</th>
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
                                            <th>Uploaded</th>
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
                                                    @if(!empty($row->front_page))
                                                        <div class="d-flex flex-column mb-3">
                                                            <a href="{{ asset($row->front_page) }}" target="_blank" class="text-center" title="Front Page">
                                                                Front
                                                            </a>
                                                        </div>
                                                    @endif
                                                    @if(!empty($row->back_page))
                                                        <div class="d-flex flex-column">                                        
                                                            <a href="{{ asset($row->back_page) }}" target="_blank" class="text-center" title="Back Page"> 
                                                                Back
                                                            </a>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td data-order="{{ $row->created_at }}">
                                                    {{ $row->createdBy?->name ?? 'Admin' }} 
                                                    <br><span style="color: #006fe5">on {{$row->created_at?->format('d M, Y h:i:s a') ?? ''}}</span>
                                                </td>
                                                <td data-order="{{ $row->updated_at }}">
                                                    {{ $row->updatedBy?->name ?? 'Admin' }} 
                                                    <br><span style="color: #006fe5">on {{$row->updated_at?->format('d M, Y h:i:s a') ?? ''}}</span>
                                                </td>
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
                                                            <a href="javascript:void(0);" class="dropdown-item browse-files-btn" 
                                                                data-toggle="modal"
                                                                data-target="#imageUploadModal" 
                                                                data-card-id="{{ $row->id }}" 
                                                                data-type="front_page"><i class="fa fa-solid fa-print"></i> Front Scan</a>
                                                            <a href="javascript:void(0);" class="dropdown-item browse-files-btn" 
                                                                data-toggle="modal"
                                                                data-target="#imageUploadModal" 
                                                                data-card-id="{{ $row->id }}" 
                                                                data-type="back_page"><i class="fa fa-solid fa-print"></i> Back Scan</a>
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

    <div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageUploadModalLabel">Upload Image From Your Device</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.manual-label.upload.image') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="card_id" id="card_id">
                        <input type="hidden" name="page_type" id="page_type">
                        <input type="hidden" name="is_manual" id="is_manual" value="1">
                        <div class="form-group">
                            <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" id="image" class="form-control form-control-file"
                                accept=".jpg, .jpeg, .png, .webp" required>
                        </div>
                        <div class="row justify-content-center imgPreviewWrapper mb-2">
                            <div class="col-12">
                                <img src="" class="img-fluid img-thumbnail preview-img" alt="Image">
                            </div>
                        </div>
                        <div class="form-group float-right button-group">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('script')
<script>
    $(document).on('click', '.browse-files-btn', function() {
        $('#imageUploadModal #card_id').val('');
        $('#imageUploadModal #page_type').val('');
        $('.imgPreviewWrapper').hide();
        const cardId = $(this).data('card-id');
        const pageType = $(this).attr('data-type');

        $('#imageUploadModal #card_id').val(cardId);
        $('#imageUploadModal #page_type').val(pageType);
        
        // Clear any existing buttons inside .button-group
        $('.scan_front, .scan_back').remove();

        // Append the appropriate button based on pageType
        if (pageType === 'front_page') {
            $('.button-group').append(`<button type="button" class="btn btn-info scan_front scan_btn" data-page-type="front_page" data-card-id="${cardId}">Front Scan</button>`);
        } else if (pageType === 'back_page') {
            $('.button-group').append(`<button type="button" class="btn btn-info scan_back scan_btn" data-page-type="back_page" data-card-id="${cardId}">Back Scan</button>`);
        }
    });
    $(document).on('click', '.scan_btn', function() {
        let pageType = $(this).data('page-type');
        let cardId = $(this).data('card-id');

        // Send values to Laravel via AJAX
        $.ajax({
            url: '/admin/set-scan-session', // A new route to store session data
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                order_id: 0,
                page_type: pageType,
                card_id: cardId,
                is_manual: 1
            },
            success: function() {
                // Redirect to the clean URL after session is set
                window.location.href = '/admin/scan-card';
            }
        });
    });
</script>
@endpush

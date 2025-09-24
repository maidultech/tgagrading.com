@extends('admin.layouts.master')
@push('style')
@endpush
@section('state', 'active')
@section('title') Manage States @endsection
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
                                        <h3 class="card-title">Manage {{ $stateTitle }} </h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            @if (Auth::user()->can('admin.state.create'))
                                                <a href="" data-toggle="modal"
                                                    data-target="#addStateModal" class="btn btn-primary btn-sm btn-gradient">Add New</a>
                                             @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                         

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th width="20%">Country </th>
                                            <th width="20%">State </th>
                                            <th width="10%">GST(%)</th>
                                            <th width="10%">PST(%)</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th width="20%">Country </th>
                                            <th width="20%">State </th>
                                            <th width="10%">GST(%)</th>
                                            <th width="10%">PST(%)</th>
                                            <th width="10%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        {{-- @if (isset($data['states']) && count($data['states']) > 0) --}}
                                        @foreach ($states as $key => $state)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $state->country->name ?? 'N/A' }}</td>
                                            <td>{{ $state->name }}</td>
                                            <td>{{ $state->gst }}</td>
                                            <td>{{ $state->pst }}</td>
                                            <td>
                                                @if ($state->status == 1)
                                                    <span class="text-success">Active</span>
                                                @else
                                                    <span class="text-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button" data-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                    
                                                    <div class="dropdown-menu">
                                                        @if (Auth::user()->can('admin.state.edit'))
                                                            <a href="javascript:void(0)" class="dropdown-item edit" data-id="{{ $state->id }}">
                                                                <i class="fa fa-pencil"></i> Edit
                                                            </a>
                                                        @endif
                                    
                                                        @if (Auth::user()->can('admin.state.delete'))
                                                            <a href="{{ route('admin.state.delete', $state->id) }}" id="deleteData" class="dropdown-item">
                                                                <i class="fa fa-trash"></i> Delete
                                                            </a>
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

    {{-- create modal --}}
    <div class="modal fade" id="addStateModal" tabindex="-1" aria-labelledby="addStateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStateModalLabel">Add State</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.state.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="country_id" class="form-label font-weight-bold">Country</label>
                            <select name="country_id" id="country_id" class="form-control" required>
                                
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="name" class="form-label">State Name</label>
                            <input type="text" name="name" id="name" class="form-control"
                                placeholder="State name" required>
                        </div>
                        <div class="form-group">
                            <label for="gst" class="form-label">GST Rate (%)</label>
                            <input type="number" step="0.01" name="gst" id="gst" 
                                   value="{{ old('gst', 0) }}" class="form-control" 
                                   placeholder="e.g., 5.0, 7.5" min="0" max="100">
                        </div>
                        <div class="form-group">
                            <label for="pst" class="form-label">PST Rate (%)</label>
                            <input type="number" step="0.01" name="pst" id="pst" 
                                   value="{{ old('pst', 0) }}" class="form-control" 
                                   placeholder="e.g., 1.0, 3.5" min="0" max="100">
                        </div>
                        
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    {{-- edit modal --}}
    <div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit State</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="modal_body"></div>

                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')
    <script type="text/javascript">
        $(document).on('click', '.edit', function () {
            let cat_id = $(this).data('id');
            $.get('state/' + cat_id + '/edit', function (data) {
                console.log(data);
                $('#editCategoryModal').modal('show');
                $('#modal_body').html(data);
            });
        });
    </script>
@endpush
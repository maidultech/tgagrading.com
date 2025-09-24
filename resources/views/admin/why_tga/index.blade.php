@extends('admin.layouts.master')
@push('style')
@endpush
@section('why-tga', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection
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
                                        <h3 class="card-title">{{ $data['title'] ?? '' }} </h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            @if (Auth::user()->can('admin.why-tga.create'))
                                                <a href="javascript:void(0)" data-toggle="modal"
                                                    data-target="#addCategoryModal"
                                                    class="btn btn-primary btn-sm btn-gradient">Add New</a>
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
                                            <th width="25%">Title</th>
                                            <th width="25%">Details</th>
                                            <th width="15%">Order Number</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th width="25%">Title</th>
                                            <th width="25%">Details</th>
                                            <th width="15%">Order Number</th>
                                            <th width="15%">Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @if (isset($data['rows']) && count($data['rows']) > 0)
                                            @foreach ($data['rows'] as $key => $row)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $row->title }}</td>
                                                    <td>{{ $row->destails }}</td>
                                                    <td>{{ $row->order_id }}</td>
                                                    <td>
                                                        @if ($row->status == 1)
                                                            <span class="text-success">Active</span>
                                                        @else
                                                            <span class="text-danger">Inactive</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        <div class="dropdown">
                                                            <button
                                                                class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient"
                                                                type="button" data-toggle="dropdown" aria-expanded="false">
                                                                Actions
                                                            </button>

                                                            <div class="dropdown-menu" style="">
                                                                @if (Auth::user()->can('admin.why-tga.edit'))
                                                                    <a href="javascript:void(0)" class="dropdown-item edit"
                                                                        data-id="{{ $row->id }}"><i
                                                                            class="fa fa-pencil"></i> Edit</a>
                                                                @endif

                                                                @if (Auth::user()->can('admin.why-tga.delete'))
                                                                    <a href="{{ route('admin.why-tga.delete', $row->id) }}"
                                                                        id="deleteData" class="dropdown-item"><i
                                                                            class="fa fa-trash"></i> Delete</a>
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

    {{-- create modal --}}
    <div class="modal fade" id="addCategoryModal" tabindex="-1" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add New</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.why-tga.store') }}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" name="title" id="title" class="form-control"
                                placeholder="Title" required>
                        </div>
                        <div class="form-group">
                            <label for="destails" class="form-label">Details</label>
                            <textarea name="destails" id="destails" class="form-control"
                                placeholder="Details" required></textarea>
                        </div>
                        {{-- <div class="form-group">
                            <label for="order_id" class="form-label">Order Number</label>
                            <input type="number" name="order_id" id="order_id" class="form-control"
                                placeholder="Order Number">
                        </div> --}}
                        <div class="form-group">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Add</button>
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
                    <h5 class="modal-title" id="editCategoryModalLabel">Edit New</h5>
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
        $(document).on('click', '.edit', function() {
            let cat_id = $(this).data('id');
            $.get('why-tga/' + cat_id + '/edit', function(data) {
                console.log(data);
                $('#editCategoryModal').modal('show');
                $('#modal_body').html(data);
            });
        });
    </script>
@endpush

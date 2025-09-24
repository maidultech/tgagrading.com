@extends('admin.layouts.master')
@section('title') {{ $data['title'] ?? '' }} @endsection
@section('final_grading', 'active')
@php
    $rows = $data['rows'];
@endphp

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
                                        <h3 class="card-title">{{ __('messages.common.manage') }} {{ $data['title'] ?? '' }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            {{-- @if (Auth::user()->can('admin.final-grading.create'))
                                                <a href="{{ route('admin.cpage.create') }}" class="btn btn-primary btn-sm">Add
                                                    New</a>
                                            @endif --}}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table  class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            {{-- <th>SN</th> --}}
                                            <th>{{__('Final Grade')}}</th>
                                            <th>{{__('Name')}}</th>
                                            <th>{{__('Order ID')}}</th>
                                            <th style="width:15%;" class="text-center">{{__('messages.common.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                          
                                            <th>{{__('messages.seo.page_name')}}</th>
                                            <th>{{__('messages.common.status')}}</th>
                                            <th style="width:15%;" class="text-center">{{__('messages.common.action')}}</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @foreach ($rows as $key => $row)
                                            <tr>                                           
                                                <td>{{ $row->finalgrade }}</td>
                                                <td>{{ $row->name  }}</td>
                                                <td>{{ $row->order_id  }}</td>
                                                <td class="text-center">
                                                    <div class="dropdown">
                                                        <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient" type="button"
                                                            data-toggle="dropdown" aria-expanded="false">
                                                            {{__('messages.common.actions')}}
                                                        </button>
                                                        <div class="dropdown-menu">
                                                            @if (Auth::user()->can('admin.final-grading.edit'))
                                                                <a id="gradingNameUpdateModalBtn" class="dropdown-item" data-name="{{ $row->name }}" data-id={{ $row->id }} class="fa fa-pencil"></i> {{__('messages.common.edit')}}</a>
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
    {{-- gradingNameUpdateModal --}}
    <!-- Modal -->
<div class="modal fade" id="gradingNameUpdateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Grading Name Update</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.finalgrading.update') }}" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" value="" name="grading_id" >
                <div class="form-group">
                    <label for="">Grading Name</label>
                    <input type="text" name="grading_name" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('script')
    <script>
        
        $(document).on('click' ,'#gradingNameUpdateModalBtn', function (e) {
            $('#gradingNameUpdateModal').modal('show')
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#gradingNameUpdateModal').find('[name="grading_id"]').val(id)
            $('#gradingNameUpdateModal').find('[name="grading_name"]').val(name)
        })
    </script>
@endpush
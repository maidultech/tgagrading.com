@extends('admin.layouts.master')
@section('service', 'active')

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
                                        <h3 class="card-title">Manage {{ $data['title'] ?? '' }} </h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            @if (Auth::user()->can('admin.service.create'))
                                                <a href="{{ route('admin.service.create') }}"
                                                    class="btn btn-primary btn-sm btn-gradient">Add
                                                    New</a>
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
                                            <th width="10%">Featured Image</th>
                                            <th width="25%">Title</th>
                                            <th width="25%">Subtitle</th>
                                            <th width="10%">Date</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Type</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th width="10%">Featured Image</th>
                                            <th width="25%">Title</th>
                                            <th width="25%">Subtitle</th>
                                            <th width="10%">Date</th>
                                            <th width="10%">Status</th>
                                            <th width="10%">Type</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @if (isset($data['rows']) && count($data['rows']) > 0)
                                            @foreach ($data['rows'] as $key => $row)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ getBlogPhoto($row->thumb) }}">
                                                            <img src="{{ getBlogPhoto($row->thumb) }}" width="100"
                                                                height="100" alt="{{ $row->thumb }}">
                                                        </a>
                                                    </td>
                                                    <td>{{ $row->title }}</td>
                                                    <td>
                                                        @if ($row->type == 3)
                                                            {{-- {!! $row->details !!} --}}
                                                        @else
                                                            {{ $row->sub_title }}
                                                        @endif
                                                    </td>
                                                    <td>{{ date('d M Y', strtotime($row->created_at)) }}</td>
                                                    <td>
                                                        @if ($row->status == 1)
                                                            <span class="text-success">Active</span>
                                                        @else
                                                            <span class="text-danger">Inactive</span>
                                                        @endif

                                                    </td>
                                                    <td>
                                                        @if ($row->type == 1)
                                                            <span>Service</span>
                                                        @else
                                                            <span>Footer Page</span>
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
                                                                @if (Auth::user()->can('admin.service.view'))
                                                                    @if ($row->slug == 'sports-card-grading-service')
                                                                        <a href="{{ route('frontend.sportsCardGrading') }}"
                                                                            class="dropdown-item"><i class="fa fa-eye"></i>
                                                                            View</a>
                                                                    @elseif($row->slug == 'crossover-card-grading-service')
                                                                        <a href="{{ route('frontend.crossoverGradingService') }}"
                                                                            class="dropdown-item"><i class="fa fa-eye"></i>
                                                                            View</a>
                                                                    @elseif($row->slug == 'trading-card-grading-service')
                                                                        <a href="{{ route('frontend.cardGradingService') }}"
                                                                            class="dropdown-item"><i class="fa fa-eye"></i>
                                                                            View</a>
                                                                    @else
                                                                        <a href="{{ route('frontend.cardGradingDetails', $row->slug) }}"
                                                                            class="dropdown-item"><i class="fa fa-eye"></i>
                                                                            View</a>
                                                                    @endif
                                                                @endif

                                                                @if (Auth::user()->can('admin.service.edit'))
                                                                    <a href="{{ route('admin.service.edit', $row->id) }}"
                                                                        class="dropdown-item"><i class="fa fa-pencil"></i>
                                                                        Edit</a>
                                                                @endif

                                                                @if ($row->type != 3)
                                                                    @if (Auth::user()->can('admin.service.delete'))
                                                                        <a href="{{ route('admin.service.delete', $row->id) }}"
                                                                            id="deleteData" class="dropdown-item"><i
                                                                                class="fa fa-trash"></i> Delete</a>
                                                                    @endif
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>


                                                <!-- View Modal -->
                                                <div class="modal fade" id="view{{ $row->id }}" tabindex="-1"
                                                    aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-primary">
                                                                <h5 class="modal-title">View Service List</h5>
                                                                <button type="button" class="btn-close"
                                                                    data-dismiss="modal" aria-label="Close">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>

                                                            <div class="modal-body">

                                                                <div class="view_modal_content">
                                                                    <label>Featured Image : </label>
                                                                    <a href="{{ getBlogPhoto($row->image) }}"
                                                                        target="__target">
                                                                        <img src="{{ getBlogPhoto($row->image) }}"
                                                                            width="100" alt="Featured Image">
                                                                    </a>
                                                                </div>

                                                                <div class="view_modal_content">
                                                                    <label>Title : </label>
                                                                    <span class="text-dark">{{ $row->title }}</span>
                                                                </div>

                                                                {{-- <div class="view_modal_content">
                                                                <label>Tags : </label>
                                                                <span class="text-dark">
                                                                    @php
                                                                        $tags = json_decode($row->tags, true);
                                                                    @endphp

                                                                    @foreach ($tags as $tag)
                                                                    {{$tag}}
                                                                    @endforeach
                                                                </span>
                                                            </div> --}}

                                                                <div class="view_modal_content">
                                                                    <label>Date : </label>
                                                                    <span class="text-dark">
                                                                        {{ date('d M Y', strtotime($row->created_at)) }}
                                                                    </span>
                                                                </div>

                                                                <div class="message_content">
                                                                    <label>Description : </label>
                                                                    <span class="text-dark"> {!! $row->details !!} </span>
                                                                </div>

                                                                <div class="view_modal_content">
                                                                    <label>Status : </label>
                                                                    @if ($row->status == 1)
                                                                        <span class="text-success">Active</span>
                                                                    @else
                                                                        <span class="text-danger">Active</span>
                                                                    @endif
                                                                </div>
                                                            </div>

                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-dismiss="modal">Close</button>
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


@endsection

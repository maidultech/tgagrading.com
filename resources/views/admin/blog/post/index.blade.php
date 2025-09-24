@extends('admin.layouts.master')
@section('blogDropdown', 'menu-open')
@section('blog-post', 'active')

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
                                            @if (Auth::user()->can('admin.blog-post.create'))
                                                <a href="{{ route('admin.blog-post.create') }}" class="btn btn-primary btn-sm btn-gradient">Add
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
                                            <th width="25%">Post Title</th>
                                            <th width="15%">Category</th>
                                            <th width="10%">Date</th>
                                            <th width="10%">Published Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">SL</th>
                                            <th width="10%">Featured Image</th>
                                            <th width="25%">Post Title</th>
                                            <th width="15%">Category</th>
                                            <th width="10%">Date</th>
                                            <th width="10%">Published Status</th>
                                            <th width="15%">Action</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @if (isset($data['rows']) && count($data['rows']) > 0)
                                            @foreach ($data['rows'] as $key => $row)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>
                                                        <a href="{{ getBlogPhoto($row->image) }}">
                                                            <img src="{{ getBlogPhoto($row->image) }}"
                                                            width="100" height="100" alt="{{ $row->title }}">
                                                        </a>
                                                    </td>
                                                    <td>{{ $row->title }}</td>
                                                    <td>{{ $row->category->name }}</td>
                                                    <td>{{ date('d M Y', strtotime($row->created_at)) }}</td>
                                                    <td>
                                                        @if ($row->status == 1)
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
                                                            
                                                            <div class="dropdown-menu" style="">
                                                                @if (Auth::user()->can('admin.blog-post.view'))
                                                                    <button type="button" class="dropdown-item" data-toggle="modal" data-target="#view{{ $row->id }}"><i class="fa fa-eye"></i> View</button>
                                                                @endif

                                                                @if (Auth::user()->can('admin.blog-post.edit'))
                                                                    <a href="{{ route('admin.blog-post.edit', $row->id) }}" class="dropdown-item"><i class="fa fa-pencil"></i> Edit</a>
                                                                @endif
        
                                                                @if (Auth::user()->can('admin.blog-post.delete'))
                                                                    <a href="{{ route('admin.blog-post.delete', $row->id) }}" id="deleteData" class="dropdown-item"><i class="fa fa-trash"></i> Delete</a>
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
                                                            <h5 class="modal-title">View Blog Post List</h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="view_modal_content">
                                                                <label>Featured Image : </label>
                                                                <a href="{{ getBlogPhoto($row->image) }}" target="__target">
                                                                    <img src="{{ getBlogPhoto($row->image) }}" width="100" alt="Featured Image">
                                                                </a>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Post Title : </label>
                                                                <span class="text-dark">{{ $row->title }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Category : </label>
                                                                <span class="text-dark">{{ $row->category->name }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Tags : </label>
                                                                <span class="text-dark">
                                                                    @php
                                                                        $tags = json_decode($row->tags, true);
                                                                    @endphp

                                                                    @foreach ($tags as $tag)
                                                                    {{$tag}}
                                                                    @endforeach
                                                                </span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Date : </label>
                                                                <span class="text-dark"> {{ date('d M Y',strtotime($row->created_at)) }} </span>
                                                            </div>

                                                            <div class="message_content">
                                                                <label>Description : </label>
                                                                <span class="text-dark"> {!! $row->details !!} </span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Status : </label>
                                                                @if($row->status == 1)
                                                                    <span class="text-success">Active</span>
                                                                @else
                                                                   <span class="text-danger">Active</span>
                                                                @endif
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


@endsection

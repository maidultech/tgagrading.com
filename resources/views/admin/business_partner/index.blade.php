@extends('admin.layouts.master')
@section('title') Business Partner @endsection
@section('settings_menu', 'menu-open')
@section('business_partner', 'active')


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
                                        <h3 class="card-title">Manage Business Partner</h3>
                                    </div>
                                    <div class="col-6">
                                        @can('admin.business-partner.create')
                                        <div class="float-right">
                                            <a href="{{ route('admin.business-partner.create') }}" class="btn btn-primary btn-sm">Add New</a>
                                        </div>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                           <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>SN</th>
                                            <th>Logo</th>
                                            <th>Company Name</th>
                                            <th>Company Url</th>
                                            <th>Description</th>
                                            <th>Status</th>
                                            <th>Order </th>
                                            @if (Auth::user()->can('admin.business-partner.edit')||Auth::user()->can('admin.business-partner.delete') )
                                            <th style="width:15%;" class="text-center">Action</th>
                                            @endcan
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($bussiPartners as $key => $partner)
                                            <tr>
                                                <td>{{ $key + 1 }}</td>
                                                <td>
                                                    @if($partner->logo)
                                                        <img src="{{ asset($partner->logo) }}" alt="Logo" style="width:70px; height: auto;">
                                                    @else
                                                        N/A
                                                    @endif
                                                </td>
                                                <td>{{ $partner->company_name ?? 'N/A' }}</td>
                                                <td>
                                                    <a href="{{ $partner->company_url }}" target="_blank">
                                                        {{ $partner->company_url  }}
                                                    </a>
                                                </td>

                                                <td>{{ Str::limit($partner->details, 50) ?? 'N/A' }}</td>
                                                <td>
                                                    @if($partner->status == 1)
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ $partner->order_id}}</td>
                                                @if (Auth::user()->can('admin.business-partner.edit')||Auth::user()->can('admin.business-partner.delete') )
                                                    <td class="text-center">
                                                        <div class="dropdown">
                                                                <button
                                                                    class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient"
                                                                    type="button" data-toggle="dropdown" aria-expanded="false">
                                                                Action
                                                                </button>
                                                                <div class="dropdown-menu">
                                                                    @can('admin.business-partner.edit')
                                                                    <a href="{{ route('admin.business-partner.edit',$partner->id) }}"
                                                                        class="dropdown-item"><i class="fa fa-pencil"></i> Edit
                                                                    </a>
                                                                    @endcan
                                                                    @can('admin.business-partner.delete')
                                                                    <a href="{{ route('admin.business-partner.delete',$partner->id) }}"
                                                                        class="dropdown-item"><i class="fa-solid fa-trash-can"></i> Delete
                                                                    </a>
                                                                    @endcan
                                                                </div>
                                                            </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No Business partner found</td>
                                            </tr>
                                        @endforelse
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

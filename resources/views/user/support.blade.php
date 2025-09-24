@extends('frontend.layouts.app')

@section('title')
    {{ 'My Orders' }}
@endsection



@push('style')

@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User') }}</li>
        <li class="breadcrumb-item">{{ __('Support Ticket') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->

    <div class="account_seciton pb-5 pt-3">
        <div class="container">
            <div class="section_heading mb-4">
                <h1>Support Tickets</h1>
            </div>
            <div class="row gy-4 gy-lg-0">
                <div class="col-lg-3">
                    @section('user_support','active')
                    @include('user.sidebar')
                </div>
                <div class="col-lg-9">
                    <div class="user_dashboard p-3 p-xl-4 rounded border border-light">
                        <div class="header mb-4">
                            <div class="title">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="title">
                                        <h4>Support Tickets</h4>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('user.support.create') }}" class="btn btn-light p-2 px-3">Create Ticket</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="orders_table table-responsive">
                            <table class="table m-0 min_width align-middle text-center">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tickets as $ticket)
                                    <tr>
                                        <td>{{ $ticket->ticket_number }}</td>
                                        <td>{{ $ticket->subject }}</td>
                                        <td>
                                            @if($ticket->status == '1')
                                                <span class="d-inline-flex  px-2 fw-semibold text-success-emphasis bg-success-subtle border border-success-subtle rounded-2">Open</span>
                                            @elseif($ticket->status == '0')
                                                <span class="d-inline-flex  px-2 fw-semibold text-warning-emphasis bg-warning-subtle border border-warning-subtle rounded-2">Pending</span>
                                            @else
                                                <span class="d-inline-flex  px-2 fw-semibold text-danger-emphasis bg-danger-subtle border border-danger-subtle rounded-2">Closed</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($ticket->priority == 1)
                                                Low
                                            @elseif($ticket->priority == 2)
                                                Medium
                                            @else
                                                High
                                            @endif
                                        </td>
                                        <td>{{ $ticket->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ route('user.ticket.show',['id' => $ticket->id]) }}" title="View">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                    class="icon icon-tabler icon-tabler-eye" width="24"
                                                    height="24" viewBox="0 0 24 24" stroke-width="2"
                                                    stroke="currentColor" fill="none" stroke-linecap="round"
                                                    stroke-linejoin="round">
                                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                    <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
                                                    <path
                                                        d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
                                                </svg>
                                            </a>
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
@endsection
@push('script')
@endpush

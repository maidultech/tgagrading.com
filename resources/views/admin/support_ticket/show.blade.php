@extends('admin.layouts.master')

@section('support', 'active')

@section('title') Support Ticket @endsection

@push('style')
    <style>
        .status-select {
            padding: 2px 6px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: transparent;
            color: #FFFFFF !important;
        }
        .status-select:focus-visible {
            outline: none;
        }
        .direct-chat-text {
            width: fit-content !important;
        }
        .direct-chat-name {
            display: block;
            text-align: end;
            margin-right: 50px;
        }
        .direct-chat-primary .right>.direct-chat-text {
            width: fit-content !important;
            margin-left: auto !important;
        }
        .direct-chat-messages {
            min-height: 450px;
        }
    </style>
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            
                            <div class="card-header">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-md-6">
                                        <h3 class="card-title mb-2">Manage Support Ticket </h3>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-lg-end mb-2">
                                        <form action="{{ route('admin.support-ticket.update', $data->id) }}" method="post">
                                            @csrf
                                            <label for="status" style="font-weight: 600 !important;">Status : </label>
                                            <select name="status" class="status-select bg-{{ $data->status == 0 ? 'warning' : ($data->status == 1 ? 'success' : 'danger') }}" onchange="this.form.submit()">
                                                <option value="1" {{ $data->status == 1 ? 'selected' : '' }}>Open</option>
                                                <option value="2" {{ $data->status == 2 ? 'selected' : '' }}>Close</option>
                                            </select>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <div class="user_dashboard p-3 p-xl-4 rounded border border-light">
                                    <div class="header mb-4">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="title">
                                                <h4>View Ticket</h4>
                                            </div>
                                            <div>
                                                <a href="{{ route('admin.support-ticket.index') }}" class="btn btn-light rounded-pill p-2">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                        stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon icon-tabler icons-tabler-outline icon-tabler-arrow-left">
                                                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                        <path d="M5 12l14 0" />
                                                        <path d="M5 12l6 6" />
                                                        <path d="M5 12l6 -6" />
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="user_info">
                                        <div class="d-flex justify-content-center">
                                            <div class="card w-100">
                                                <div
                                                    class="card card-primary card-outline direct-chat direct-chat-primary shadow-none mb-0">
                                                    <div class="card-header">
                                                        <div class="d-flex align-items-center py-2">
                                                            <img class="direct-chat-img mr-2 img-fluid rounded-pill"
                                                                src="{{ getProfile(Auth::user()->image) }}" alt="Message User Image">
                                                            <h3 class="card-title ms-2 mb-0 align-self-center pb-0">
                                                                {{ auth()->user()->name }}
                                                            </h3>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="direct-chat-messages">
                
                                                            @foreach ($data->messages as $msg)
                                                                @if ($msg->msg_from == 1)
                                                                    <div class="direct-chat-msg">
                                                                        <div class="direct-chat-infos clearfix">
                                                                            <span
                                                                                class="direct-chat-name mb-2 float-left">{{ $msg->sender->name }}</span>
                                                                        </div>
                                                                        <img class="direct-chat-img rounded-pill"
                                                                            src="{{ getProfile($msg->sender->image) }}"
                                                                            alt="User Image">
                
                                                                        <div class="direct-chat-text">
                                                                            {{ $msg->message }}
                                                                            @php
                                                                                $file_count = count(
                                                                                    json_decode($msg->attachment, true),
                                                                                );
                                                                            @endphp
                                                                            @if ($file_count > 0)
                                                                                <p class="text-muted mb-0 ">
                                                                                    <span class="font-weight-bold ">Attachments:
                                                                                    </span>
                                                                                    @foreach (json_decode($msg->attachment) as $key => $file)
                                                                                        <a target="_blank"
                                                                                            href="{{ asset($file) }}">{{ $key }}</a>
                                                                                    @endforeach
                                                                                </p>
                                                                            @endif
                
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    <div class="direct-chat-msg right">
                
                
                                                                        <div class="direct-chat-infos clearfix">
                                                                            <span class="direct-chat-name mb-2 float-end">Support
                                                                                Team</span>
                                                                        </div>
                                                                        <img class="direct-chat-img rounded-pill"
                                                                            src="{{ getProfile($msg->sender->image) }}"
                                                                            alt="User Image">
                                                                        <div class="direct-chat-text">
                                                                            {{ $msg->message }}
                                                                            {{-- @dd(json_decode($msg->attachment,true)) --}}
                                                                            @php
                                                                                $file_count = count(
                                                                                    json_decode($msg->attachment, true),
                                                                                );
                                                                            @endphp
                                                                            @if ($file_count > 0)
                                                                                <p class="text-white mb-0 ">
                                                                                    <span class="font-weight-bold">Attachments:</span>
                                                                                    @foreach (json_decode($msg->attachment) as $key => $file)
                                                                                        <a class="text-white" target="_blank"
                                                                                            href="{{ asset($file) }}">{{ $key }}</a>
                                                                                    @endforeach
                                                                                </p>
                                                                            @endif
                
                                                                        </div>
                                                                    </div>
                                                                @endif
                
                                                                {{-- <div class="direct-chat-msg right">
                                                                        <img class="direct-chat-img"
                                                                            src="{{ asset('assets/images/agents.png') }}"
                                                                            alt="User Image">
                                                                        <div class="direct-chat-text">
                                                                            Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                                                        </div>
                                                                    </div> --}}
                                                            @endforeach
                
                                                        </div>
                                                    </div>
                
                                                    @if ($data->status != 2)
                                                        <div class="card-footer">
                                                            <form action="{{ route('admin.support-ticket.reply', ['id' => $id]) }}"
                                                                method="post" enctype="multipart/form-data">
                                                                @csrf

                                                                <div class="input-group">
                                                                    <span class="input-group-prepend">
                                                                        <label for="attachments" type="button"
                                                                            class="btn btn-primary mb-0 rounded-end-0"
                                                                            style="padding-top: 11px;">
                                                                            Upload Files
                                                                        </label>
                                                                        <input type="file" name="attachment[]"
                                                                            class="d-none form-control" id="attachments">
                
                                                                    </span>
                                                                    <input type="text" name="message" placeholder="Type Message ..."
                                                                        class="form-control" required>
                                                                    <span class="input-group-append">
                                                                        <button type="submit" class="btn btn-primary rounded-start-0">
                                                                            Send
                                                                            <svg xmlns="http://www.w3.org/2000/svg"
                                                                                class="icon icon-tabler icon-tabler-send" width="20"
                                                                                height="20" viewBox="0 0 24 24" stroke-width="2"
                                                                                stroke="currentColor" fill="none"
                                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                                <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                                                                <path d="M10 14l11 -11" />
                                                                                <path
                                                                                    d="M21 3l-6.5 18a.55 .55 0 0 1 -1 0l-3.5 -7l-7 -3.5a.55 .55 0 0 1 0 -1l18 -6.5" />
                                                                            </svg>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                            </form>
                                                            <div class="attachment-div" style="display: none;">
                                                                <strong class="text-primray">Attached file :</strong> 
                                                                <span class="filename"></span> 
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-info mb-0">This Ticket Has been closed</div>
                                                    @endif
                
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection

@push('script')
<script>
    $(document).on('change', '#attachments', function(event) {
        var fileName = $(this).val().split('\\').pop();
        
        if (fileName) {
            $('.attachment-div').show();
            $('.filename').text(fileName);
            toastr.info('Attachment attached');
        } else {
            $('.attachment-div').hide();
        }
    });
</script>
<script>
    $(document).ready(function() {
        var chatMessagesDiv = $('.direct-chat-messages');
        chatMessagesDiv.scrollTop(chatMessagesDiv[0].scrollHeight);
    });
</script>
@endpush

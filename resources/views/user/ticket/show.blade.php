@extends('frontend.layouts.app')

@section('title')
    {{ 'Support Ticket' }}
@endsection
@section('user_support_ticket', 'active')

@push('style')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@24.6.0/build/css/intlTelInput.css">
    <style>
        .direct-chat .card-body {
            overflow-x: hidden;
            padding: 0;
            position: relative;
        }

        .direct-chat.chat-pane-open .direct-chat-contacts {
            -webkit-transform: translate(0, 0);
            transform: translate(0, 0);
        }

        .direct-chat.timestamp-light .direct-chat-timestamp {
            color: #30465f;
        }

        .direct-chat.timestamp-dark .direct-chat-timestamp {
            color: #cccccc;
        }

        .direct-chat-messages {
            -webkit-transform: translate(0, 0);
            transform: translate(0, 0);
            height: 250px;
            overflow: auto;
            padding: 10px;
        }

        .direct-chat-msg,
        .direct-chat-text {
            display: block;
        }

        .direct-chat-msg {
            margin-bottom: 10px;
        }

        .direct-chat-msg::after {
            display: block;
            clear: both;
            content: "";
        }

        .direct-chat-messages,
        .direct-chat-contacts {
            transition: -webkit-transform .5s ease-in-out;
            transition: transform .5s ease-in-out;
            transition: transform .5s ease-in-out, -webkit-transform .5s ease-in-out;
        }

        .direct-chat-text {
            border-radius: 0.3rem;
            background-color: #d2d6de;
            border: 1px solid #d2d6de;
            color: #444;
            margin: 5px 0 0 50px;
            padding: 5px 10px;
            position: relative;
        }

        .direct-chat-text::after,
        .direct-chat-text::before {
            border: solid transparent;
            border-right-color: #d2d6de;
            content: " ";
            height: 0;
            pointer-events: none;
            position: absolute;
            right: 100%;
            top: 15px;
            width: 0;
        }

        .direct-chat-text::after {
            border-width: 5px;
            margin-top: -5px;
        }

        .direct-chat-text::before {
            border-width: 6px;
            margin-top: -6px;
        }

        .right .direct-chat-text {
            margin-left: 0;
            margin-right: 50px;
        }

        .right .direct-chat-text::after,
        .right .direct-chat-text::before {
            border-left-color: #d2d6de;
            border-right-color: transparent;
            left: 100%;
            right: auto;
        }

        .direct-chat-img {
            background: #e7e7e7;
            border-radius: 50%;
            float: left;
            height: 40px;
            width: 40px;
        }

        .right .direct-chat-img {
            float: right;
        }

        .direct-chat-infos {
            display: block;
            font-size: 0.875rem;
            margin-bottom: 2px;
        }

        .direct-chat-name {
            font-weight: 600;
        }

        .direct-chat-timestamp {
            color: #697582;
        }

        .direct-chat-contacts-open .direct-chat-contacts {
            -webkit-transform: translate(0, 0);
            transform: translate(0, 0);
        }

        .direct-chat-contacts {
            -webkit-transform: translate(101%, 0);
            transform: translate(101%, 0);
            background-color: #343a40;
            bottom: 0;
            color: #fff;
            height: 250px;
            overflow: auto;
            position: absolute;
            top: 0;
            width: 100%;
        }

        .direct-chat-contacts-light {
            background-color: #f8f9fa;
        }

        .direct-chat-contacts-light .contacts-list-name {
            color: #495057;
        }

        .direct-chat-contacts-light .contacts-list-date {
            color: #6c757d;
        }

        .direct-chat-contacts-light .contacts-list-msg {
            color: #545b62;
        }
    </style>
    <style>
        .iti.iti--allow-dropdown.iti--show-flags.iti--inline-dropdown {
            width: 100% !important;
        }

        input#attachment {
            line-height: 33px;
        }

        img.direct-chat-img {
            max-width: 40px;
        }
    </style>
    <style>
        .direct-chat-messages {
            max-height: 500px;
            height: 500px;
            overflow: hidden;
            overflow-y: auto;
        }

        .direct-chat-text {
            border-radius: 0.3rem;
            background-color: #d2d6de;
            border: 1px solid #d2d6de;
            color: #444;
            margin: 5px 0 0 10px;
            padding: 5px 10px;
            position: relative;
            display: inline-block;
        }

        .right .direct-chat-text {
            float: right;
        }

        .right .direct-chat-text {
            margin-right: 10px;
        }

        .card-header {
            background-color: rgb(255 255 255 / 3%) !important;
        }
        .zd-comment {
            text-align: right;
        }
        .signature {
            margin-top: 10px;
            font-size: 14px;
            color: #646464;
        }
    </style>
@endpush

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
@section('breadcrumb')
<li class="breadcrumb-item">{{ __('User') }}</li>

    <li class="breadcrumb-item">{{ __('Support Ticket') }}</li>
@endsection

<!-- ======================= breadcrumb end  ============================ -->
<!-- ======================= my account start  ============================ -->
<div class="account_seciton pb-5 pt-3">
    <div class="container">
        <div class="section_heading mb-4">
            <h1>Support Ticket</h1>
        </div>
        <div class="row gy-4 gy-lg-0">
            <div class="col-lg-3">
                @include('user.sidebar')
            </div>
            <div class="col-lg-9">
                <div class="user_dashboard p-3 p-xl-4 rounded border border-light">
                    <div class="header mb-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="title">
                                <h4>View Ticket</h4>
                            </div>
                            <div>
                                <a href="{{ route('user.ticket.index') }}" class="btn btn-light rounded-pill p-2">
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
                                            {{-- <img class="direct-chat-img mr-2 img-fluid rounded-pill"
                                                src="{{ getProfile(Auth::user()->image) }}" alt="Message User Image"> --}}
                                            <h4 class="card-title ms-2 mb-0 align-self-center pb-0">
                                                {{ $ticket['subject'] }}
                                            </h4>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="direct-chat-messages p-3">
                                            @foreach ($messages as $msg)
                                                <div class="direct-chat-msg {{ $msg['author_id'] === $ticket['requester_id'] ? '' : 'right' }}">
                                                    <img class="direct-chat-img rounded-pill"
                                                        src="{{ $msg['author_id'] === $ticket['requester_id'] ? getProfile($user->image) : getProfile('assets/logo/support-logo.png') }}"
                                                        alt="User Image">
                                            
                                                    <div class="direct-chat-text" style="min-width: 255px;">
                                                        <div class="direct-chat-infos mb-3 d-flex align-items-center justify-content-between" style="font-size: 13px;">
                                                            <span class="direct-chat-name {{ $msg['author_id'] === $ticket['requester_id'] ? 'order-2' : '' }}">
                                                                {{ \Carbon\Carbon::parse($msg['created_at'])->format('jS M, Y H:i') }}
                                                            </span>
                                                            <span class="direct-chat-name">
                                                                {{ $msg['author_id'] === $ticket['requester_id'] ? $user->name.' '.$user->last_name : 'Support Team' }}
                                                            </span>
                                                        </div>
                                                        <div class="{{ $msg['author_id'] === $ticket['requester_id'] ? 'float-left' : '' }}">
                                                            {!! $msg['html_body'] !!}
                                                        </div>
                                                        @if (!empty($msg['attachments']))
                                                            <p class="text-muted mb-0">
                                                                <span class="fw-bold">Attachments:</span>
                                                                @foreach ($msg['attachments'] as $attachment)
                                                                    <a target="_blank" href="{{ $attachment['content_url'] }}">{{ $attachment['file_name'] }}</a>
                                                                @endforeach
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        
                                        </div>
                                    </div>

                                    @if ($ticket['status'] != 'solved')
                                        <div class="card-footer">
                                            <form action="{{ route('user.ticket.reply', ['id' => $ticket['id'], 'author_id' => $ticket['requester_id']]) }}"
                                                method="post" enctype="multipart/form-data" onsubmit="handleSubmit(this)">
                                                @csrf
                                                <div class="input-group">
                                                    <span class="input-group-prepend">
                                                        <label for="attachments" type="button"
                                                            class="btn btn-primary mb-0 rounded-end-0"
                                                            style="padding-top: 11px;">
                                                            Upload File
                                                        </label>
                                                        <input type="file" name="attachment[]"
                                                            class="d-none form-control" id="attachments">

                                                    </span>
                                                    <input type="text" name="message" placeholder="Type Message ..."
                                                        class="form-control" required style="height: 45px;">
                                                    <span class="input-group-append">
                                                        <button type="submit" id="submitBtn" class="btn btn-primary rounded-start-0">
                                                            <span id="btnSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                                            <span id="btnText">
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
                                                            </span>
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
                                        <div class="alert alert-secondary mb-0" style="border-radius: 0px; border: 0px;">This Ticket Has been closed</div>
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
<!--======================= my account end ============================ -->

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
<script>
    function handleSubmit(form) {
        const btn = form.querySelector('#submitBtn');
        const text = form.querySelector('#btnText');
        const spinner = form.querySelector('#btnSpinner');

        btn.disabled = true;
        text.classList.add('d-none');
        spinner.classList.remove('d-none');
    }
</script>
@endpush

@extends('admin.layouts.master')


@section('settings_menu', 'menu-open')
@section('certificate', 'active')

@section('title') Certificate Verification @endsection

@push('style')
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Certificate Verification</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.nav.dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">Certificate Verification</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    @if (Session::get('success'))
                        <div class="col-lg-12">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ Session::get('success') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        </div>
                    @endif
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="m-0">Certificate Verification</h5>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('admin.settings.certificate_store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group ">
                                                <img src="{{ asset($certificate_verification->image ?? 'assets/default.png') }}" alt="Image" class="mt-2" width="100">
                                            </div>
                                        </div>
                                        <!-- Image -->
                                        <div class="mb-3 col-md-6">
                                            <label for="image" class="form-label">Image</label>
                                            <input type="file" class="form-control" id="image" name="image">
                                        </div>
                                        <!-- Starting Text -->
                                        <div class="mb-3 col-md-6">
                                            <label for="starting_text" class="form-label">Starting Text</label>
                                            <input type="text" class="form-control" id="starting_text" name="starting_text"
                                                   value="{{ old('starting_text', $certificate_verification->starting_text ?? '') }}">
                                        </div>

                                        <!-- Title -->
                                        <div class="mb-3 col-md-6">
                                            <label for="title" class="form-label">Title</label>
                                            <input type="text" class="form-control" id="title" name="title" value="{{ old('title', $certificate_verification->title ?? '') }}">
                                        </div>


                                        <!-- Certification Number -->
                                        <div class="mb-3 col-md-6">
                                            <label for="certification_number" class="form-label">Certification Number</label>
                                            <input type="text" class="form-control" id="certification_number" name="certification_number" value="{{ old('certification_number', $certificate_verification->certification_number ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Year -->
                                        <div class="mb-3 col-md-6">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="text" class="form-control" id="year" name="year" value="{{ old('year', $certificate_verification->year ?? '') }}">
                                        </div>

                                        <!-- Brand -->
                                        <div class="mb-3 col-md-6">
                                            <label for="brand" class="form-label">Brand</label>
                                            <input type="text" class="form-control" id="brand" name="brand" value="{{ old('brand', $certificate_verification->brand ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Sport -->
                                        <div class="mb-3 col-md-6">
                                            <label for="sport" class="form-label">Sport</label>
                                            <input type="text" class="form-control" id="sport" name="sport" value="{{ old('sport', $certificate_verification->sport ?? '') }}">
                                        </div>

                                        <!-- Card Number -->
                                        <div class="mb-3 col-md-6">
                                            <label for="card_number" class="form-label">Card Number</label>
                                            <input type="text" class="form-control" id="card_number" name="card_number" value="{{ old('card_number', $certificate_verification->card_number ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Player -->
                                        <div class="mb-3 col-md-6">
                                            <label for="player" class="form-label">Player</label>
                                            <input type="text" class="form-control" id="player" name="player" value="{{ old('player', $certificate_verification->player ?? '') }}">
                                        </div>

                                        <!-- Variety -->
                                        <div class="mb-3 col-md-6">
                                            <label for="variety" class="form-label">Variety</label>
                                            <input type="text" class="form-control" id="variety" name="variety" value="{{ old('variety', $certificate_verification->variety ?? '') }}">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- Grade -->
                                        <div class="mb-3 col-md-6">
                                            <label for="grade" class="form-label">Grade</label>
                                            <input type="text" class="form-control" id="grade" name="grade" value="{{ old('grade', $certificate_verification->grade ?? '') }}">
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Update</button>
                                </form>
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

@endpush

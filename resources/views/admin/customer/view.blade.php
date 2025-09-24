@extends('admin.layouts.master')
@section('customer', 'active')
@section('title') {{ $data['title'] ?? '' }} @endsection

@push('style')
    <style>
        td {
            width: 0;
        }
    </style>
@endpush
@php
    $user = $data['user'];
    $address = $data['address'];
    // $cards = $data['cards'];
    // $localLanguage = Session::get('languageName');

    // $today = \Carbon\Carbon::today();
    // $validDate = \Carbon\Carbon::parse($user->current_pan_valid_date);
    // $day = $today->diffInDays($validDate, false);
    // $day = $day < 0 ? 0 : $day;
@endphp

@section('content')
    <div class="content-wrapper">


        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-6">
                        <div class="card">
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title"> {{ __('messages.crud.customer_view') }}</h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            <a href="{{ route('admin.customer.index') }}" class="btn btn-primary btn-gradient btn-sm">{{ __('messages.common.back') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12 ">
                                    <div class="card-body table-responsive p-4">
                                        <table class="table">
                                            <tr>
                                                <td style="width:10%;">{{__('messages.common.image')}} :</td>
                                                <td><img src="{{ getProfile($user->image) }}" width="100" alt="Profile Image"></td>
                                            </tr>
                                            <tr>
                                                <td>{{__('messages.common.name')}} : </td>
                                                <td>{{ $user->name }}&nbsp;{{ $user->last_name }}</td>
                                            </tr>

                                            <tr>
                                                <td>{{__('messages.common.email')}} : </td>
                                                <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                            </tr>
                                            @if ($user->phone)
                                                <tr>
                                                    <td>{{__('messages.common.phone')}} : </td>
                                                    <td> <a href="tel:{{ $user->phone }}">+{{$user->dial_code}}{{ $user->phone }}</a></td>
                                                </tr>
                                            @endif
                                            @if ($user->dob)
                                            <tr>
                                                <td>{{__('Date of Birth')}} : </td>
                                                <td>
                                                    {{ date('d M, Y', strtotime($user->dob)) }}
                                                </td>
                                            </tr>
                                            @endif
                                            @if ($user->dob)
                                            <tr>
                                                <td>{{__('Registration At')}} : </td>
                                                <td>
                                                    <div>

                                                        {{ date('d M, Y', strtotime($user->created_at)) }}
                                                        {{ date('h:i A', strtotime($user->created_at)) }}

                                                    </div>
                                                </td>
                                            </tr>
                                            @endif
                                            <tr>
                                                <td>{{__('messages.common.status')}} :</td>
                                                <td>
                                                    @if ($user->status == 1)
                                                        <span class="text-success">Active</span>
                                                    @else
                                                        <span class="text-danger">Active</span>
                                                    @endif

                                                </td>
                                            </tr>
                                            @if ($address)
                                                <tr>
                                                    <td>{{ __('messages.common.address') }} :</td>
                                                    <td>
                                                        {{ collect([
                                                            $address->street,
                                                            $address->apt_unit,
                                                            $address->city,
                                                            $address->zip_code,
                                                            $address->state,
                                                            $address->country,
                                                        ])->filter()->implode(', ') }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($user->current_plan_id)
                                                <tr>
                                                    <td> {{__('messages.customer.current_plan')}} :</td>
                                                    <td>{{ $localLanguage == 'en' ? $user->userPlan->name : $user->userPlan->name_de }}
                                                    </td>
                                                </tr>
                                            @endif
                                            @if ($user->current_pan_valid_date)
                                                <tr>
                                                    <td> {{__('messages.customer.plan_validty')}} :</td>
                                                    <td>{{ $day}} Days
                                                    </td>
                                                </tr>
                                            @endif

                                            {{-- @if (isset($user->card) && count($user->card) > 0)
                                            <tr>
                                                <td> {{__('messages.customer.total_card')}} :</td>
                                                <td> {{ count($user->card)}}
                                                </td>
                                            </tr>
                                        @endif --}}

                                        </table>
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

@extends('admin.layouts.master')

@section('customer', 'active')
@section('title') {{ $data['title'] ?? __('messages.common.user') }} @endsection

@push('style')
@endpush
@php
    $localLanguage = Session::get('languageName');
    $category = config('static_array.categories');
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
                                        <h3 class="card-title">{{ __('messages.common.manage') }}
                                            {{ $data['title'] ?? __('messages.common.user') }} </h3>
                                    </div>
                                    <div class="col-6">
                                        <div class="float-right">
                                            @if (Auth::user()->can('admin.customer.create'))
                                                <a href="{{ route('admin.customer.create') }}"
                                                    class="btn btn-primary btn-gradient btn-sm">{{ __('messages.common.add_new') }}</a>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                 <table id="dataTables" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th width="5%">Customer ID</th>
                                            <th width="20%">{{ __('messages.common.image') }}</th>
                                            <th width="15%">Date Created</th> <!-- New Column -->
                                            <th width="20%">{{ __('messages.common.name') }}</th>
                                            <th width="20%">{{ __('messages.customer.contact_info') }}</th>
                                            <th width="20%">Plan</th>
                                            <th width="15%">{{ __('messages.common.status') }}</th>
                                            <th width="15%" class="text-center">{{ __('messages.common.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="5%">Customer ID</th>
                                            <th width="15%">{{ __('messages.common.image') }}</th>
                                            <th width="15%">Date Created</th> <!-- New Column -->
                                            <th width="15%">{{ __('messages.common.name') }}</th>
                                            <th width="15%">{{ __('messages.customer.contact_info') }}</th>
                                            <th width="15%">Plan</th>
                                            <th width="15%">{{ __('messages.common.status') }}</th>
                                            <th width="15%" class="text-center">{{ __('messages.common.action') }}</th>
                                        </tr>
                                    </tfoot>

                                    <tbody>
                                        @if (isset($data['rows']) && count($data['rows']) > 0)
                                            @foreach ($data['rows'] as $key => $row)
                                                <tr>
                                                    <td>{{ $row->user_code }}</td>
                                                    <td>
                                                        <a href="{{ getProfile($row->image) }}" target="__blank">
                                                            <img src="{{ getProfile($row->image) }}" alt="{{ $row->name }}" title="{{ $row->name }}" height="70" width="70">
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <span class="d-none">{{ $row->created_at }}</span> 
                                                        {{ \Carbon\Carbon::parse($row->created_at)->format('d M Y h:i A') }}
                                                    </td>
                                                    <td>
                                                        <p class="mb-0">{{ $row->name }}&nbsp;{{ $row->last_name }}</p>
                                                        {{-- <div>   
                                                            <small>
                                                                {{ date('d M, Y', strtotime($row->created_at)) }}
                                                                {{ date('h:i A', strtotime($row->created_at)) }}
                                                            </small>
                                                        </div> --}}
                                                    </td>
                                                    <td>
                                                         <a href="mailto:{{$row->email}}">{{ $row->email }} ({{ $row->email_verified_at ? 'Verified' : 'Not Verified' }})</a><br>
                                                         <a href="tel:{{$row->phone}}">{{$row->dial_code ? '+'.''.$row->dial_code : ''}}{{ $row->phone ? $row->phone : ''}}</a>
                                                    </td>
                                                    <td>
                                                        @if($row->is_subscriber && $row->subscription_start < now() && $row->subscription_end > now())
                                                        @php
                                                            $available_card_limit = $row->getAvailableCardLimit();
                                                        @endphp
                                                        {{-- @if($row->current_plan_name)
                                                        <span><strong>Plan</strong> - {{$row->current_plan_name}}</span><br>
                                                        @endif --}}
                                                        {{-- <span><strong>Plan Validity</strong> - {{ \Carbon\Carbon::parse($row->subscription_end)->format('d F Y') }}</span><br> --}}
                                                        <span><strong>{{$row->current_plan_name}} <br> End Date</strong> - {{ \Carbon\Carbon::parse($row->subscription_end)->format('d F Y') }}</span><br>
                                                        <span><strong>Remaining Cards This Year</strong> - {{$available_card_limit > 0 ? $available_card_limit : 'No cards remaining'}}</span>
                                                        @endif
                                                   </td>
                                                    <td>
                                                        @if ($row->status == 1)
                                                            <span class="text-success">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="text-danger">{{ __('Inactive') }}</span>
                                                        @endif
                                                    </td>

                                                    <td class="text-center">
                                                        <div class="dropdown">
                                                            <button class="btn btn-xs btn-secondary dropdown-toggle btn-sm btn-gradient"
                                                                type="button" data-toggle="dropdown" aria-expanded="false">
                                                                {{ __('messages.common.actions') }}
                                                            </button>
                                                            <div class="dropdown-menu">
                                                                <a href="{{ route('admin.customer.login', $row->id) }}" class="dropdown-item" target="_blank"><i class="fa fa-solid fa-user-shield"></i> Login As Customer</a>
                                                                @if (Auth::user()->can('admin.card.view'))
                                                                    <a href="{{ route('admin.card.index') }}?customer={{ $row->id }}"
                                                                        class="cards dropdown-item"
                                                                        data-id="{{ $row->id }}"
                                                                        title="{{__('Cards')}}"><i class="nav-icon fa fa-address-card"></i>
                                                                        {{ __('Cards') }}
                                                                    </a>
                                                                @endif
                                                                @if (Auth::user()->can('admin.order.view'))
                                                                    <a href="{{ route('admin.order.index') }}?customer={{ $row->id }}"
                                                                        class="cards  dropdown-item"
                                                                        data-id="{{ $row->id }}"
                                                                        title="{{__('Orders')}}"><i class="nav-icon fa fa-address-card"></i>
                                                                        {{ __('Orders') }}
                                                                    </a>
                                                                @endif
                                                                @if (Auth::user()->can('admin.customer.edit'))
                                                                    <a href="javascript:void(0)"
                                                                        class="pass_change  dropdown-item"
                                                                        data-id="{{ $row->id }}"
                                                                        title="Password Edit"><i class="fa fa-key" aria-hidden="true"></i>
                                                                        {{ __('messages.common.change_password') }}
                                                                    </a>
                                                                @endif
                                                                
                                                                @if (Auth::user()->can('admin.customer.view'))

                                                                <button type="button" data-toggle="modal" data-target="#view{{ $row->id }}" class="view dropdown-item">
                                                                    <i class="fa fa-eye"></i> {{ __('messages.common.view') }}</button>

                                                                @endif

                                                                @if (Auth::user()->can('admin.customer.edit'))
                                                                    <a href="{{ route('admin.customer.edit', $row->id) }}"
                                                                        class="edit dropdown-item"><i class="fa fa-pencil"></i> {{ __('messages.common.edit') }}</a>
                                                                @endif

                                                                @if (Auth::user()->can('admin.customer.edit'))
                                                                    @if($row->status == 1)
                                                                    <a href="{{ route('admin.customer.disable', $row->id) }}"
                                                                        class="edit dropdown-item"><i class="text-danger fa fa-solid fa-user-slash"></i> Disable</a>
                                                                    @else 
                                                                    <a href="{{ route('admin.customer.disable', $row->id) }}"
                                                                        class="edit dropdown-item"><i class="text-success fa fa-solid fa-user-check"></i> Active</a>
                                                                    @endif 
                                                                @endif
                                                                @if (Auth::user()->can('admin.customer.delete'))
                                                                    <a href="{{ route('admin.customer.delete', $row->id) }}" id="deleteData" class="dropdown-item">
                                                                        <i class="fa fa-trash"></i> {{ __('messages.common.delete') }}</a>
                                                                @endif
                                                                
                                                            </div>
                                                        </div>

                                                    </td>
                                                </tr>

                                                <!-- View Modal -->

                                            <div class="modal fade px-0" id="view{{ $row->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">

                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-primary">
                                                            <h5 class="modal-title">View {{ $row->name }}'s Info</h5>
                                                            <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        </div>

                                                        <div class="modal-body">

                                                            <div class="view_modal_content">
                                                                <label>Image : </label>
                                                                <a href="{{ getProfile($row->image) }}" target="__target">
                                                                    <img src="{{ getProfile($row->image) }}" width="100" alt="Profile Image">
                                                                </a>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Customer Number : </label>
                                                                <span class="text-dark">{{ $row->user_code }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Full Name : </label>
                                                                <span class="text-dark">{{ $row->name . $row->last_name }}</span>
                                                            </div>

                                                            <div class="view_modal_content">
                                                                <label>Email : </label>
                                                                <a href="mailto: {{ $row->email }}" class="text-info">{{ $row->email }}</a>
                                                            </div>

                                                            @if ($row->category_id)
                                                            <div class="view_modal_content">
                                                                <label>Favorite Category : </label>
                                                                <span class="text-dark">{{ $category[$row->category_id] }}</span>
                                                            </div>
                                                            @endif

                                                            @if($row->phone)
                                                            <div class="view_modal_content">
                                                                <label>Phone : </label>
                                                                <a href="tel:+{{ $row->dial_code }}{{ $row->phone }}" class="text-info">+{{ $row->dial_code }} {{ $row->phone }}</a>
                                                            </div>
                                                            @endif

                                                            @if ($row->defaultAddress)
                                                                <div class="view_modal_content">
                                                                    <label>Address : </label>
                                                                    <span class="text-dark">
                                                                        {{ collect([
                                                                            $row->defaultAddress?->street,
                                                                            $row->defaultAddress?->apt_unit,
                                                                            $row->defaultAddress?->city,
                                                                            $row->defaultAddress?->zip_code,
                                                                            $row->defaultAddress?->state,
                                                                            $row->defaultAddress?->country,
                                                                        ])->filter()->implode(', ') }}
                                                                    </span>
                                                                </div>
                                                            @endif
{{-- 
                                                            <div class="view_modal_content">
                                                                <label>Address : </label>
                                                                <span class="text-dark">{{ $row->address }}</span>
                                                            </div> --}}

                                                            <div class="view_modal_content">
                                                                <label>Date : </label>
                                                                <span class="text-dark"> {{ date('d M, Y', strtotime($row->created_at)) }} </span>
                                                            </div>
                                                            @if($row->is_subscriber && $row->subscription_start < now() && $row->subscription_end > now())
                                                                <div class="view_modal_content">
                                                                    <label>Plan : </label>
                                                                    <div>
                                                                        @php
                                                                            $subscriptions = $row->subscriptions->where('year_start', '<', now());
                                                                        @endphp
                                                                        @if($row->current_plan_name)
                                                                        <span><strong>Name</strong> : {{$row->current_plan_name}}</span><br>
                                                                        @endif
                                                                        <span><strong>Validity</strong> : {{ \Carbon\Carbon::parse($row->subscription_end)->format('d F Y') }}</span><br>
                                                                        @foreach ($subscriptions as $subs)
                                                                            @php
                                                                                $available_card_limit = $subs ? $subs->subscription_card_peryear - $subs->order_card_peryear : 0;
                                                                            @endphp
                                                                            <span>
                                                                                <span>
                                                                                    Remaining Cards of 
                                                                                    {{ \Carbon\Carbon::parse($subs->year_start)->format('d M, Y') }} - 
                                                                                    {{ \Carbon\Carbon::parse($subs->year_end)->format('d M, Y') }}
                                                                                </span> : {{$available_card_limit > 0 ? $available_card_limit : '0'}}
                                                                            </span> <br>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            <div class="view_modal_content">
                                                                <label>Status : </label>
                                                                @if ($row->status == 1)
                                                                    <span class="text-success">Active</span>
                                                                @else
                                                                    <span class="text-danger">Inactive</span>
                                                                @endif
                                                            </div>
                                                            <div class="view_modal_content">
                                                                <label>Balance : </label>
                                                                {{ getDefaultCurrencySymbol().' '.$row->wallet_balance }}
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

    {{-- Password edit modal --}}
    <div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPasswordModalLabel">{{ __('messages.common.change_password') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.customer.password.change') }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="user_id" id="user_id">
                        <div class="form-group">
                            <label for="code" class="form-label">{{__('messages.common.password')}}</label>
                            <div class="input-group input-group-flat">
                                <input type="password" name="password" id="password-field" class="form-control"
                                    placeholder="{{__('messages.placeholder.your_password')}}" autocomplete="off" required>
                                <span class="input-group-text" style="border-top-left-radius: 0;border-bottom-left-radius: 0;">
                                    <a href="javascript:void(0)"
                                        class="link-secondary fa fa-fw fa-eye field-icon toggle-password"
                                        toggle="#password-field">
                                    </a>
                                </span>
                            </div>
                            <div class="password_validation"></div>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-danger"
                                data-dismiss="modal">{{ __('messages.common.cancel') }}</button>
                            <button type="submit" class="btn btn-success">{{ __('messages.common.update') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Plan edit modal --}}
    <div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlanModalLabel">{{ __('messages.plan.change_plan') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="modal-body">

                </div>
            </div>
        </div>
    </div>

@endsection
@push('script')

    @if(isset($openModal))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            $('#view{{ $openModal }}').modal('show');
        });
    </script>
    @endif
    <script type="text/javascript">
        $(document).on('click', '.plan_change', function() {
            let user_id = $(this).data('id');
            $.get('/admin/customer/' + user_id + '/plan', function(data) {
                $('#editPlanModal').modal('show');
                $('#modal-body').html(data);
            });
        });

        $(document).on('click', '.pass_change', function() {
            let user_id = $(this).data('id');
            $('#user_id').val(user_id);
            $('#editPasswordModal').modal('show');
        });


        $(document).ready(function() {
            // Frontend Password Validation
            $(document).on('input', '[name="password"]', function() {
                var password = $(this).val();
                var regex = /^(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/;

                // Find the .password_validation div inside the parent .form-group
                var validationContainer = $(this).closest('.form-group').find('.password_validation');

                if (!regex.test(password)) {
                    $(this).addClass('is-invalid');

                    // Add error message only if it doesn't already exist
                    if (validationContainer.find('#password-error').length === 0) {
                        validationContainer.html(
                            '<small id="password-error" class="text-danger">Password must contain at least one uppercase letter, one special character, and be at least 8 characters long.</small>'
                        );
                    }
                } else {
                    $(this).removeClass('is-invalid');
                    validationContainer.html(''); // Clear the error message
                }
            });
        });
        // password show hide
        $(".toggle-password").click(function() {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $($(this).attr("toggle"));
            if (input.attr("type") == "password") {
                input.attr("type", "text");
            } else {
                input.attr("type", "password");
            }
        });
    </script>







@endpush

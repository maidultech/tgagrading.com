@extends('frontend.layouts.app')

@section('title')
    {{ 'My Account' }}
@endsection

@push('style')
    <style>
    /* .account-section {
        background-color: #f8f9fa;
    } */

    .user-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .user-card:hover {
        transform: translateY(-2px);
    }

    .info-item {
        margin-bottom: 14px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee !important;
    }

    .info-items > div:last-child .info-item,
    .info-items > div:nth-last-child(2) .info-item {
        padding-bottom: 0 !important;
        border-bottom: none !important;
    }

    .subscription-card .info-item {
        border: 0 !important;
    }

    .subscription-card .progress {
        background: #dcdbe7;
    }

    .subscription-card .progress-bar {
        background: #c4c4cc;
    }

    .profile-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    .edit-icon {
        transition: all 0.2s ease;
    }

    .edit-icon:hover {
        transform: scale(1.1);
        color: #0d6efd !important;
    }

    .subscription-card {
        border-left: 4px solid #dcdbe7;
        background: linear-gradient(to right, #ffffff, rgba(13, 110, 253, 0.05));
    }

    .address-card {
        min-height: 200px;
    }

    .address-card .info-item {
        border-bottom-color: #ffffff00 !important;
    }

    .user-card .titlebar {
        background-color: #dcdbe7 !important;
    }

    .address-actions a.btn-outline-primary {
        color: #747478 !important;
        transition: all 0.2s ease;
        border-color: #dcdbe7 !important;
    }

    .address-actions a.btn-outline-primary:hover {
        color: #ffffff !important;
        border-color: #747478 !important;
        background-color: #747478 !important;
    }

    @media (max-width: 768px) {
        .profile-img {
            width: 60px;
            height: 60px;
        }

        .mobile-stack {
            flex-direction: column;
        }

        .mobile-stack .col-md-8,
        .mobile-stack .col-md-4 {
            width: 100%;
            text-align: left !important;
        }

        .mobile-stack .address-actions {
            margin-top: 1rem;
            justify-content: flex-start !important;
        }
    }
    </style>
@endpush

@php
    $category = config('static_array.categories');
@endphp

@section('content')
    <!-- ======================= breadcrumb start  ============================ -->
    @section('breadcrumb')
    <li class="breadcrumb-item">{{ __('User') }}</li>
        <li class="breadcrumb-item">{{ __('My Account') }}</li>
    @endsection
    <!-- ======================= breadcrumb end  ============================ -->
    <!-- ======================= my account start  ============================ -->
    <div class="account-section py-3 py-lg-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-12 d-none d-lg-block">
                    <h4 class="m-0 p-0"> My Accounts </h4>
                </div>
                <!-- Sidebar -->
                <div class="col-lg-3">
                    @section('user_dashboard','active')
                    @include('user.sidebar')
                </div>

                <!-- Main Content -->
                <div class="col-lg-9">
                    <div class="row g-4">
                        <!-- Profile Card -->
                        <div class="col-12">
                            <div class="user-card overflow-hidden">
                                <div class="titlebar d-flex justify-content-between align-items-center pb-3 px-4 pt-3 text-dark">
                                    <h4 class="mb-0 pb-0">Profile Information</h4>
                                    <a href="{{ route('user.edit.profile') }}" class="edit-icon" title="Edit Profile">
                                        <i class="fas fa-pencil-alt fa-lg text-dark"></i>
                                    </a>
                                </div>

                                <div class="p-4">
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ getPhoto($user->image) }}" class="profile-img rounded me-3" alt="{{ $user->name }}">
                                        <div>
                                            <h5 class="mb-0 pb-1">{{ $user->name }} {{ $user->last_name }}</h5>
                                            <p class="text-muted mb-0">{{ $user->email }}</p>
                                        </div>
                                    </div>

                                    <div class="row info-items">
                                        <div class="col-6 col-md-6">
                                            <div class="info-item">
                                                <h6 class="text-muted small mb-0 pb-1">Account</h6>
                                                <p class="mb-0">#{{ $user->user_code }}</p>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-6">
                                            <div class="info-item">
                                                <h6 class="text-muted small mb-0 pb-1">Phone</h6>
                                                <p class="mb-0">{{ $user->dial_code && !empty($user->phone) ? "+$user->dial_code" : '' }}{{ $user->phone ?? 'N/A' }}</p>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-6">
                                            <div class="info-item">
                                                <h6 class="text-muted small mb-0 pb-1">Favorite Category</h6>
                                                <p class="mb-0">{{ $user->category_id ? $category[$user->category_id] : 'Not specified' }}</p>
                                            </div>
                                        </div>

                                        <div class="col-6 col-md-6">
                                            <div class="info-item">
                                                <h6 class="text-muted small mb-0 pb-1">Wallet Balance</h6>
                                                <p class="mb-0">{{ getDefaultCurrencySymbol().' '.$user->wallet_balance }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription Card -->
                        @if($user->is_subscriber && $user->subscription_start < now() && $user->subscription_end > now())
                            @php
                                $available_card_limit = $user->getAvailableCardLimit();
                                $years_remaining = \Carbon\Carbon::parse($user->subscription_start)->diffInYears(\Carbon\Carbon::parse($user->subscription_end));
                            @endphp

                            <div class="col-12">
                                <div class="user-card subscription-card p-4">
                                    <div class="d-block d-md-flex justify-content-between align-items-center mb-3">
                                        <h4 class="mb-0">Subscription Details</h4>
                                        <span class="badge bg-primary">{{ $user->current_plan_name }}</span>
                                    </div>

                                    <div class="row g-2 g-md-3 g-lg-4">
                                        <div class="col-md-6">
                                            <div class="info-item mb-0 pb-0">
                                                <h6 class="text-muted small mb-0">Subscription Period</h6>
                                                <p class="mb-0">
                                                    {{ \Carbon\Carbon::parse($user->subscription_start)->format('M d, Y') }} -
                                                    {{ \Carbon\Carbon::parse($user->subscription_end)->format('M d, Y') }}
                                                </p>
                                                <small class="text-muted">{{ $years_remaining }} year{{ $years_remaining > 1 ? 's' : '' }} remaining</small>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="info-item mb-0 pb-0">
                                                <h6 class="text-muted small mb-0 mb-md-1">Cards This Year</h6>
                                                <div class="progress flex-grow-1 mb-0 mb-md-2" style="height: 17px">
                                                    <div class="progress-bar" role="progressbar"
                                                        style="width: {{ ($user->subscription_card_peryear - $available_card_limit) / $user->subscription_card_peryear * 100 }}%"
                                                        aria-valuenow="{{ $user->subscription_card_peryear - $available_card_limit }}"
                                                        aria-valuemin="0"
                                                        aria-valuemax="{{ $user->subscription_card_peryear }}">
                                                    </div>
                                                </div>
                                                <small class="text-muted">{{ $available_card_limit }}/{{ $user->subscription_card_peryear }} Remaining</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Address Card -->
                        <div class="col-12">
                            <div class="user-card address-card overflow-hidden">
                                <div class="titlebar d-flex justify-content-between align-items-center pb-3 px-4 pt-3 text-dark">
                                    <h5 class="mb-0 pb-0">Address Book</h5>
                                    @if (!$addresses->count())
                                        <a href="#" class="addAddressModal" title="Add Address">
                                            <i class="fas fa-plus fa-lg text-dark"></i>
                                        </a>
                                    @endif
                                </div>

                                @if($addresses->count())
                                    @foreach($addresses as $address)
                                    <div class="info-item p-4 mb-0 border-0">
                                        <div class="row mobile-stack">
                                            <div class="col-md-8">
                                                <h5 class="mb-1">{{ $address->first_name }} {{ $address->last_name }}</h5>
                                                <p class="mb-1">{{ $address->street }}</p>
                                                @if($address->apt_unit)
                                                <p class="mb-1">{{ $address->apt_unit }}</p>
                                                @endif
                                                <p class="mb-1">{{ $address->city }}, {{ $address->state }} {{ $address->zip_code }}</p>
                                                <p class="mb-0">{{ $address->country }}</p>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="d-flex address-actions justify-content-md-end">
                                                    <a href="#"
                                                    data-edit_route="{{ route('user.address.edit', $address->id) }}"
                                                    data-update_route="{{ route('user.address.update', $address->id) }}"
                                                    class="btn btn-sm btn-outline-primary me-2 editAddress text-nowrap px-4 py-2">
                                                        <i class="fas fa-edit me-1"></i> Edit
                                                    </a>
                                                    {{-- <a href="{{ route('user.address.delete', $address->id) }}"
                                                    onclick="return confirm('Are you sure you want to remove this address?')"
                                                    class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash me-1"></i> Remove
                                                    </a> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-map-marker-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No Address Saved</h5>
                                        <p class="text-muted">You haven't added any addresses yet.</p>
                                        <a href="#" class="btn btn-primary addAddressModal">
                                            <i class="fas fa-plus me-1"></i> Add Your First Address
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--======================= my account end ============================ -->

    <!-- Add Address Modal -->

    <div class="modal fade" id="addAddressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="addAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="addAddressModalLabel">Address Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.address.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        @include('user.address-book-form', ['address' => null ])
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary bg-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Address Modal -->

    <div class="modal fade" id="editAddressModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
         aria-labelledby="editAddressModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="editAddressModalLabel">Address Information</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="#" method="POST">
                    @csrf
                    <div class="modal-body editFormData">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary bg-danger" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function () {
            $('.addAddressModal').on('click', function (e) {
                e.preventDefault();
                $('#addAddressModal form').trigger('reset');
                $('#addAddressModal').modal('show');
            });
            $('.editAddress').on('click', function (e) {
                e.preventDefault();
                let editRoute = $(this).data('edit_route');
                console.log(editRoute);
                let updateRoute = $(this).data('update_route');
                $('.editFormData').html('');
                $('#editAddressModal form').attr('action', updateRoute);

                fetch(editRoute)
                    .then(response => response.json())
                    .then(data => {
                        $('.editFormData').html(data.html);
                    });
                $('#editAddressModal').modal('show');
            });
        });
        function ucwords(str) {
            return str.replace(/\b\w/g, function(char) {
                return char.toUpperCase();
            });
        }
        var states = {
            "Canada" : @json(getStateCodeMap('Canada')),
            "United States" : @json(getStateCodeMap('United States'))
        };
        $(document).on('change', '.country', function() {
            const country = $(this).val();

            // Find the corresponding `.state` dropdown in the same group
            const stateSelect = $(this).closest('.address-block').find('.state');

            stateSelect.empty(); // Clear previous options
            const currentStates = states[country] || {};

            stateSelect.append(`<option value="">Select State/Province</option>`);

            $.each(currentStates, function(key, value) {
                stateSelect.append(`<option value="${ucwords(key)}">${ucwords(key)}</option>`);
            });
        });

    </script>
@endpush

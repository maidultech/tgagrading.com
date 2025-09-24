@extends('admin.layouts.master')
@section('plan', 'active')

@section('title') {{ $title ?? '' }} @endsection

@push('style')
    <style>
        .removeOldFeature {
            border-radius: 1px 6px 6px 0;
        }

        .removeFeature {
            border-radius: 1px 6px 6px 0;
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
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="">
                                        <h4 class="card-title">{{ $title ?? __('messages.plan.edit_plan') }}</h4>
                                    </div>
                                    <div class="">
                                        <a href="{{ route('admin.plan.index') }}"
                                            class="btn btn-sm btn-primary btn-gradient">{{ __('messages.common.back') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.plan.update', $row->id) }}" class="pt-3 pb-3">
                                    @csrf
                                    <div class="row d-flex align-items-center justify-content-center">
                                        <div class="col-xl-8">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for=""
                                                            class="form-label">{{ __('messages.plan.plan_name') }} <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" name="name" id=""
                                                            placeholder="{{ __('messages.plan.plan_name') }}"
                                                            class="form-control" value="{{ old('name', $row->name) }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-6" id="minimum_card_div">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Minimum Cards <span
                                                                class="text-danger">*</span> <span class="text-info">(Save 0
                                                                for no minimum)</span></label>
                                                        <input type="number" name="minimum_card" id="minimum_card"
                                                            placeholder="Minimum Cards" class="form-control"
                                                            value="{{ old('minimum_card', $row->minimum_card) }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for=""
                                                            class="form-label">{{ __('messages.plan.plan_price') }} <span
                                                                class="text-danger">*</span> <span class="text-info">(Save 0
                                                                for contact)</span></label>
                                                        <input type="number" step="0.01" name="price" id=""
                                                            placeholder="{{ __('messages.plan.plan_price') }}"
                                                            class="form-control" value="{{ old('price', $row->price) }}"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for="" class="form-label">Badge <span class="text-danger">*</span></label>
                                                        <select name="is_badge" id="is_badge" class="form-control" required>
                                                            <option value="" class="d-none">Select</option>
                                                            <option value="popular" {{ old('is_badge', $row->is_badge) == 'popular' ? 'selected' : '' }}>Most Popular</option>
                                                            <option value="custom" {{ old('is_badge', $row->is_badge) == 'custom' ? 'selected' : '' }}>Custom</option>
                                                            <option value="none" {{ old('is_badge', $row->is_badge) == 'none' ? 'selected' : '' }}>No</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6" id="custom_text_container" style="display: none;">
                                                    <div class="form-group">
                                                        <label for="custom_text" class="form-label">Custom Badge Text <span class="text-danger">*</span></label>
                                                        <input type="text" name="custom_text" id="custom_text" placeholder="Custom Badge Text"
                                                               class="form-control" value="{{ old('custom_text', $row->custom_text) }}">
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for=""
                                                            class="form-label">{{ __('messages.common.status') }}</label>
                                                        <select name="status" id="" class="form-control">
                                                            <option value="1"
                                                                {{ old('status', $row->status) == '1' ? 'selected' : '' }}>
                                                                {{ __('messages.common.active') }}</option>
                                                            <option value="0"
                                                                {{ old('status', $row->status) == '0' ? 'selected' : '' }}>
                                                                {{ __('messages.common.deactive') }}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="type" class="form-label">Type</label>
                                                        <select name="type" id="type" class="form-control">
                                                            <option value="single" {{ old('type', $row->type) == 'single' ? 'selected' : '' }}>Single</option>
                                                            <option value="general" {{ old('type', $row->type) == 'general' ? 'selected' : '' }}>General</option>
                                                            <option value="bulk" {{ old('type', $row->type) == 'bulk' ? 'selected' : '' }}>Bulk</option>
                                                            <option value="subscription" {{ old('type', $row->type) == 'subscription' ? 'selected' : '' }}>Subscription</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-12" id="subscription_div" style="display: none;">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="subscription_year" class="form-label">Year <span class="text-danger">*</span></label>
                                                                <input type="number" name="subscription_year" id="subscription_year" min="1" 
                                                                    placeholder="Number of Years" class="form-control" 
                                                                    value="{{ old('subscription_year', $row->subscription_year) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="subscription_peryear_card" class="form-label">No of Cards Per Year <span class="text-danger">*</span></label>
                                                                <input type="number" name="subscription_peryear_card" id="subscription_peryear_card" min="1"
                                                                    placeholder="Number of Cards Per Year" class="form-control"
                                                                    value="{{ old('subscription_peryear_card', $row->subscription_peryear_card) }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-group">
                                                        <label for=""
                                                            class="form-label">{{ __('messages.plan.order_number') }}</label>
                                                        <input type="number" name="order_number" id=""
                                                            placeholder="{{ __('messages.plan.order_number') }}"
                                                            class="form-control"
                                                            value="{{ old('order_number', $row->order_number) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="form-group" id="oldFeature">
                                                        @foreach ($features as $feature)
                                                            <div class="externalField mb-3">
                                                                <div class="form-group">
                                                                    <label for="feature_name"
                                                                        class="form-label">{{ __('messages.plan.feature') }}</label>
                                                                    <div class="input-group">
                                                                        <input type="text" name="feature_name[]"
                                                                            value="{{ old('feature_name.' . $loop->index, $feature->feature_name) }}"
                                                                            id="feature_name" required class="form-control"
                                                                            placeholder="{{ __('messages.plan.feature') }}">
                                                                        <button type="button"
                                                                            class="removeOldFeature btn btn-danger btn-sm">
                                                                            <i class="fa fa-times"></i>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                                {{-- <label for="" class="form-label w-100 d-flex justify-content-between align-items-center" style="margin-top: 2.5px;">
                                                                    <span>{{ __('messages.plan.feature') }} </span>
                                                                    <button type="button" class="removeOldFeature btn rounded-0 border-0 btn-danger btn-sm py-0 px-2">
                                                                        <i class="fa fa-times"></i>
                                                                    </button>
                                                                </label>
                                                                <input type="text" name="feature_name[]" value="{{ old('feature_name.' . $loop->index, $feature->feature_name) }}"
                                                                       id="feature_name" required class="form-control" placeholder="{{ __('messages.plan.feature') }}"> --}}
                                                            </div>
                                                        @endforeach
                                                        <div class="text-right mt-1">
                                                            <button type="button"
                                                                class="addMoreFeature btn btn-xs btn-primary">{{ __('messages.plan.add_more_feature') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="">
                                                <div id="plan_field" class="row"></div>
                                            </div>
                                            <div class="">
                                                <div class="mt-4">
                                                    <button type="submit"
                                                        class="btn btn-success">{{ __('messages.common.update') }}</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>

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
        $(document).ready(function() {
            $('body').on('click', '.addMoreFeature', function() {
                var input = `
                   <div class="col-12 externalField mb-3">
                        <label for="feature_name" class="form-label">{{ __('messages.plan.feature') }}</label>
                        <div class="input-group">
                            <input type="text" name="feature_name[]" id="feature_name" required class="form-control" placeholder="{{ __('messages.plan.feature') }}">
                            <button type="button" class="removeFeature btn btn-danger btn-sm">
                                <i class="fa fa-times"></i>
                            </button>
                        </div>
                    </div>
                    `;
                // var input = `
            //     <div class="col-12 externalField mb-3">
            //         <div class="">
            //             <label for="" class="form-label w-100 d-flex justify-content-between align-items-center" style="margin-top: 2.5px;">
            //                 <span>{{ __('messages.plan.feature') }} </span>
            //                     <button type="button" class="removeFeature btn rounded-0 border-0 btn-danger btn-sm py-0 px-2">
            //                         <i class="fa fa-times"></i>
            //                     </button>
            //             </label>
            //             <input type="text" name="feature_name[]" id="feature_name" required class="form-control" placeholder="{{ __('messages.plan.feature') }}">
            //         </div>
            //     </div>
            //     `;
                $('#plan_field').append(input);
            });
            $('#plan_field').on('click', '.removeFeature', function() {
                $(this).closest('.externalField').remove();
            });

            $('#oldFeature').on('click', '.removeOldFeature', function() {
                $(this).closest('.externalField').remove();
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelect = document.getElementById('type');
            const subscriptionDiv = document.getElementById('subscription_div');
            const subscriptionFields = ['subscription_year', 'subscription_peryear_card'];
            const minimumCardDiv = document.getElementById('minimum_card_div');
            const hideField = ['minimum_card'];

            function toggleSubscriptionDiv() {
                if (typeSelect.value === 'subscription') {
                    subscriptionDiv.style.display = 'block';
                    subscriptionFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        field.setAttribute('required', 'required');
                    });
                    minimumCardDiv.style.display = 'none';
                    hideField.forEach(field_Id => {
                        const _field = document.getElementById(field_Id);
                        _field.removeAttribute('required');
                        _field.value = '';
                    });
                } else {
                    subscriptionDiv.style.display = 'none';
                    subscriptionFields.forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        field.removeAttribute('required');
                        field.value = '';
                    });
                    minimumCardDiv.style.display = 'block';
                    hideField.forEach(field_Id => {
                        document.getElementById(field_Id).setAttribute('required', 'required');
                    });
                }
            }

            toggleSubscriptionDiv();
            typeSelect.addEventListener('change', toggleSubscriptionDiv);
        });
    </script>
    <script>
        const badgeSelect = document.getElementById('is_badge');
        const customTextContainer = document.getElementById('custom_text_container');
        const customTextInput = document.getElementById('custom_text');
    
        function toggleCustomText() {
            if (badgeSelect.value === 'custom') {
                customTextContainer.style.display = 'block';
                customTextInput.setAttribute('required', 'required');
            } else {
                customTextContainer.style.display = 'none';
                customTextInput.removeAttribute('required');
            }
        }
    
        badgeSelect.addEventListener('change', toggleCustomText);
        document.addEventListener('DOMContentLoaded', toggleCustomText);
    </script>
@endpush

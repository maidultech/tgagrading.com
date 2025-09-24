@extends('admin.layouts.master')


@section('settings_menu', 'menu-open')
@section('general', 'active')

@section('title') {{ $data['title'] ?? '' }} @endsection

@push('style')
@endpush

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
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
                        <form action="{{ route('admin.settings.general_store') }}" method="post"
                            enctype="multipart/form-data" id="settingUpdate">
                            @csrf
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="m-0">{{ __('messages.common.settings') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ __('messages.settings.site_settings') }}</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">

                                                        <div class="col-lg-4">
                                                            <img src="{{ getLogo($setting->site_logo) }}" height="50px" />
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.site_logo') }}
                                                                    <br><small
                                                                        class="text-info fw-bold"><strong>({{ __('messages.settings_home_content.recommended_size') }}
                                                                            180x60px)</strong></small>
                                                                </label>
                                                                <input type="file" class="form-control" name="site_logo"
                                                                    placeholder="{{ __('Site Logo') }}..."
                                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4">

                                                            <img src="{{ getSeoImage($setting->seo_image) }}"
                                                                height="50px" />

                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.seo_image') }}
                                                                    <br><small
                                                                        class="text-info fw-bold"><strong>({{ __('messages.settings_home_content.recommended_size') }}
                                                                            728x680px)</strong></small>
                                                                </label>
                                                                <input type="file" class="form-control" name="seo_image"
                                                                    placeholder="{{ __('messages.settings.seo_image') }}..."
                                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4">
                                                            @if ($setting->favicon)
                                                                <img src="{{ getIcon($setting->favicon) }}"
                                                                    height="50px" />
                                                            @endif
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.favicon') }}
                                                                    <br><small
                                                                        class="text-info fw-bold"><strong>({{ __('messages.settings_home_content.recommended_size') }}
                                                                            200x200px)</strong></small>
                                                                </label>
                                                                <input type="file" class="form-control" name="favicon"
                                                                    placeholder="{{ __('messages.settings.favicon') }}..."
                                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.app_name') }}</label>
                                                                <input type="text" class="form-control" name="app_name"
                                                                    value="{{ config('app.name') }}"
                                                                    placeholder="{{ __('messages.settings.app_name') }}..."
                                                                    readonly>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.site_name') }}</label>
                                                                <input type="text" class="form-control" name="site_name"
                                                                    value="{{ $setting->site_name }}"
                                                                    placeholder="{{ __('messages.settings.site_name') }}..."
                                                                    required>
                                                            </div>
                                                        </div>


                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label class="form-label required">App Mode</label>
                                                                <select type="text" class="form-select form-control"
                                                                    name="app_mode" required>
                                                                    <option value="local"
                                                                        {{ $setting->app_mode == 'local' ? 'selected' : '' }}>
                                                                        {{ __('Local') }}</option>
                                                                    <option value="live"
                                                                        {{ $setting->app_mode == 'live' ? 'selected' : '' }}>
                                                                        {{ __('Live') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-4">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Email verification
                                                                </label>
                                                                <select type="text" class="form-select form-control"
                                                                    name="email_verification" required>
                                                                    <option value="1"
                                                                        {{ $setting->email_verification == 1 ? 'selected' : '' }}>
                                                                        YES</option>
                                                                    <option value="2"
                                                                        {{ $setting->email_verification == 2 ? 'selected' : '' }}>
                                                                        NO</option>

                                                                </select>
                                                            </div>
                                                        </div>

                                                        {{-- <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.site_title') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="site_name" value="{{ $setting->site_name }}"
                                                                    placeholder="{{ __('messages.settings.site_title') }}..." required>
                                                            </div>
                                                        </div> --}}
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.seo_meta_description') }}</label>
                                                                <textarea class="form-control" name="seo_meta_desc" rows="3"
                                                                    placeholder="{{ __('messages.settings.seo_meta_description') }}" style="height: 120px !important;" required>{{ $setting->seo_meta_description }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.seo_keywords') }}</label>
                                                                <textarea class="form-control required" name="meta_keywords" rows="3"
                                                                    placeholder="{{ __('SEO Keywords (Keyword 1, Keyword 2)') }}" style="height: 120px !important;" required>{{ $setting->seo_keywords }}</textarea>
                                                            </div>
                                                        </div>
                                                        {{-- Schema Markup --}}
                                                        <div class="col-12">
                                                            <div class="form-group">
                                                                <label for="schema_markup" class="form-label">Schema
                                                                    Markup Code <small class="text-primary">(Including
                                                                        script tag)</small></label>
                                                                <textarea name="schema_markup" id="schema_markup" class="form-control" placeholder="Enter Schema Markup Code">{{ old('schema_markup', $setting->schema_markup ?? '<script> </script>') }}</textarea>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.invoice_footer') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="invoice_footer"
                                                                    value="{{ $setting->invoice_footer }}"
                                                                    placeholder="{{ __('messages.settings.invoice_footer') }}..."
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Copyright Text</label>
                                                                <input type="text" class="form-control"
                                                                    name="copyright_text"
                                                                    value="{{ $setting->copyright_text ?? '' }}"
                                                                    placeholder="Copyright Text..." required>
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.tax') }}</label>
                                                                <input type="number" step="0.01" min="0" class="form-control"
                                                                    name="tax" value="{{ $setting->tax }}"
                                                                    placeholder="{{ __('messages.settings.tax') }}..." required>
                                                            </div>
                                                        </div> --}}

                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- General Settings --}}
                                        <div class="col-lg-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ __('messages.settings.general_settings') }}
                                                    </h3>
                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <img src="{{ getLogo($setting->order_confirmation_image) }}"
                                                                height="50px" />
                                                            <div class="mb-3">
                                                                <label class="form-label">Order Confirmation Image
                                                                    <br><small
                                                                        class="text-info fw-bold"><strong>({{ __('messages.settings_home_content.recommended_size') }}
                                                                            180x60px)</strong></small>
                                                                </label>
                                                                <input type="file" class="form-control"
                                                                    name="order_confirmation_image"
                                                                    placeholder="{{ __('Site Logo') }}..."
                                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.common.email') }}</label>
                                                                <input type="email" name="email" class="form-control"
                                                                    value="{{ $setting->email }}">
                                                                @error('email')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.support_email') }}</label>
                                                                <input type="support_email" name="support_email"
                                                                    class="form-control"
                                                                    value="{{ $setting->support_email }}">
                                                                @error('support_email')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.phone_no') }}</label>
                                                                <input type="phone_no" name="phone_no"
                                                                    class="form-control"
                                                                    value="{{ $setting->phone_no }}">
                                                                @error('phone_no')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.office_address') }}</label>
                                                                <textarea class="form-control" name="office_address" rows="3"
                                                                    placeholder="{{ __('messages.settings.office_address') }}" style="height: 75px !important;" required>{{ $setting->office_address }}</textarea>

                                                                @error('office_address')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('messages.settings.map_link') }}</label>
                                                                <input type="map_link" name="map_link"
                                                                    class="form-control"
                                                                    value="{{ $setting->map_link }}">
                                                                @error('map_link')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Social --}}
                                        <div class="col-lg-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h3 class="card-title">Social URL</h3>
                                                </div>
                                                <div class="card-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Facebook URL') }}</label>
                                                        <input type="url" class="form-control" name="facebook_url"
                                                            value="{{ $setting->facebook_url }}"
                                                            placeholder="{{ __('Facebook URL') }}...">
                                                        @error('facebook_url')
                                                            <span class="help-block text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Twitter Url') }}</label>
                                                        <input type="url" class="form-control" name="twitter_url"
                                                            value="{{ $setting->twitter_url }}"
                                                            placeholder="{{ __('Twitter Url') }}...">
                                                        @error('twitter_url')
                                                            <span class="help-block text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Linkedin url') }}</label>
                                                        <input type="url" class="form-control" name="linkedin_url"
                                                            value="{{ $setting->linkedin_url }}"
                                                            placeholder="{{ __('Linkedin url') }}...">
                                                        @error('linkedin_url')
                                                            <span class="help-block text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('Instagram url') }}</label>
                                                        <input type="url" class="form-control" name="instagram_url"
                                                            value="{{ $setting->instagram_url }}"
                                                            placeholder="{{ __('Instagram url') }}...">
                                                        @error('instagram_url')
                                                            <span class="help-block text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('WhatsApp Number') }}</label>
                                                        <input type="text" class="form-control" name="whatsapp_number"
                                                            value="{{ $setting->whatsapp_number }}"
                                                            placeholder="{{ __('WhatsApp Number') }}...">
                                                        @error('whatsapp_number')
                                                            <span class="help-block text-danger">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- paypal setting --}}
                                        <div class="col-lg-6 mb-3">
                                            <div class="card h-100">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ __('messages.settings.paypal_settings') }}
                                                    </h3>
                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Paypal Enabled?</label>
                                                                <select type="text" class="form-select form-control"
                                                                    placeholder="Select a payment mode"
                                                                    id="select-tags-advanced" name="paypal_enable"
                                                                    required>
                                                                    <option value="1"
                                                                        {{ $config[34]->config_value == '1' ? 'selected' : '' }}>
                                                                        {{ __('Yes') }}</option>
                                                                    <option value="0"
                                                                        {{ $config[34]->config_value == '0' ? 'selected' : '' }}>
                                                                        {{ __('No') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.mode') }}</label>
                                                                <select type="text" class="form-select form-control"
                                                                    placeholder="Select a payment mode"
                                                                    id="select-tags-advanced" name="paypal_mode" required>
                                                                    <option value="sandbox"
                                                                        {{ $config[3]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                                        {{ __('Sandbox') }}</option>
                                                                    <option value="live"
                                                                        {{ $config[3]->config_value == 'live' ? 'selected' : '' }}>
                                                                        {{ __('Live') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.client_key') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="paypal_client_key"
                                                                    value="{{ $config[4]->config_value }}"
                                                                    placeholder="{{ __('Client Key') }}..." required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label"
                                                                    required>{{ __('messages.settings.secret') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="paypal_secret"
                                                                    value="{{ $config[5]->config_value }}"
                                                                    placeholder="{{ __('Secret') }}..." required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- strip setting --}}
                                        <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">{{ __('messages.settings.stripe_settings') }}
                                                    </h3>
                                                </div>
                                                <div class="card-body">

                                                    <div class="row">

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label class="form-label required">Stripe Enabled?</label>
                                                                <select type="text" class="form-select form-control"
                                                                    placeholder="" id="select-tags-advanced"
                                                                    name="stripe_enable" required>
                                                                    <option value="1"
                                                                        {{ $config[33]->config_value == '1' ? 'selected' : '' }}>
                                                                        {{ __('Yes') }}</option>
                                                                    <option value="0"
                                                                        {{ $config[33]->config_value == '0' ? 'selected' : '' }}>
                                                                        {{ __('No') }}</option>
                                                                </select>
                                                            </div>
                                                        </div>

                                                        <!-- <div class="col-lg-12">
                                                                <div class="mb-3">
                                                                    <label
                                                                        class="form-label required">{{ __('messages.settings.mode') }}</label>
                                                                    <select type="text" class="form-select form-control"
                                                                        placeholder="Select a payment mode"
                                                                        id="select-tags-advanced" name="stripe_mode" required>
                                                                        <option value="sandbox"
                                                                            {{ $config[3]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                                            {{ __('Sandbox') }}</option>
                                                                        <option value="live"
                                                                            {{ $config[3]->config_value == 'live' ? 'selected' : '' }}>
                                                                            {{ __('Live') }}</option>
                                                                    </select>
                                                                </div>
                                                            </div> -->

                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.publishable_key') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="stripe_publishable_key"
                                                                    value="{{ $config[9]->config_value }}"
                                                                    placeholder="{{ __('Publishable Key') }}..."
                                                                    required>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label required">{{ __('messages.settings.secret') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="stripe_secret"
                                                                    value="{{ $config[10]->config_value }}"
                                                                    placeholder="{{ __('Secret') }}..." required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-12">
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Pricing Preview</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <img src="{{ getLogo($setting->flip_preview) }}"
                                                                        height="110px" />
                                                                    <div class="mb-3">
                                                                        <label class="form-label">Preview Image
                                                                            <br><small
                                                                                class="text-info fw-bold"><strong>({{ __('messages.settings_home_content.recommended_size') }}
                                                                                    360x110px)</strong></small>
                                                                        </label>
                                                                        <input type="file" class="form-control"
                                                                            name="flip_preview"
                                                                            accept=".png,.jpg,.jpeg,.gif,.svg" />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                {{-- Copyright --}}
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Zendesk Settings</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Zendesk
                                                                            Subdomain</label>
                                                                        <input type="text" class="form-control"
                                                                            name="zendesk_subdomain"
                                                                            value="{{ $setting->zendesk_subdomain ?? '' }}"
                                                                            placeholder="Zendesk Subdomain">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Zendesk
                                                                            Email</label>
                                                                        <input type="text" class="form-control"
                                                                            name="zendesk_email"
                                                                            value="{{ $setting->zendesk_email ?? '' }}"
                                                                            placeholder="Zendesk Email">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Zendesk
                                                                            Token</label>
                                                                        <input type="text" class="form-control"
                                                                            name="zendesk_token"
                                                                            value="{{ $setting->zendesk_token ?? '' }}"
                                                                            placeholder="Zendesk Token">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Cost Calculation</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                {{-- <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label required">GST (%)</label>
                                                                        <input type="number" class="form-control"
                                                                            name="gst_tax" step="0.01" min="0"
                                                                            value="{{$setting->gst_tax ?? ''}}"
                                                                            placeholder="Goods and Services Tax in percentage"
                                                                            required>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label required">PST (%)</label>
                                                                        <input type="number" class="form-control"
                                                                            name="pst_tax" step="0.01" min="0"
                                                                            value="{{$setting->pst_tax ?? ''}}"
                                                                            placeholder="Provincial Sales Tax in percentage"
                                                                            required>
                                                                    </div>
                                                                </div> --}}
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Shipping Cost
                                                                            Maximization</label>
                                                                        <input class="form-control"
                                                                            name="shipping_cost_maximization"
                                                                            type="number" step=".1" min="1"
                                                                            value="{{ $setting->shipping_cost_maximization ?? '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Insurance
                                                                            Cost</label>
                                                                        <input class="form-control" name="insurance_cost"
                                                                            type="number" step="0.01"
                                                                            value="{{ $setting->insurance_cost ?? '' }}">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Bulk Grading Cost (100-499 cards)</label>
                                                                        <input class="form-control" name="min_bulk_grading_cost"
                                                                            type="number" step="0.01"
                                                                            value="{{ $setting->min_bulk_grading_cost ?? '' }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Bulk Grading Cost (500+ cards)</label>
                                                                        <input class="form-control" name="max_bulk_grading_cost"
                                                                            type="number" step="0.01"
                                                                            value="{{ $setting->max_bulk_grading_cost ?? '' }}">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">ReCAPTCHA Settings</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label required">{{ __('Recaptcha Enable') }}</label>
                                                                        <select type="text"
                                                                            class="form-select form-control"
                                                                            id="select-tags-advanced"
                                                                            name="google_recaptcha">
                                                                            <option value="1"
                                                                                {{ $setting->google_recaptcha == '1' ? 'selected' : '' }}>
                                                                                Enable</option>
                                                                            <option value="0"
                                                                                {{ $setting->google_recaptcha == '0' ? 'selected' : '' }}>
                                                                                Disable</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">ReCAPTCHA Site
                                                                            Key</label>
                                                                        <input type="text" class="form-control"
                                                                            name="recaptcha_site_key"
                                                                            value="{{ $setting->recaptcha_site_key ?? '' }}"
                                                                            placeholder="ReCAPTCHA Site Key">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-12">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">ReCAPTCHA Site
                                                                            secret</label>
                                                                        <input type="text" class="form-control"
                                                                            name="recaptcha_site_secret"
                                                                            value="{{ $setting->recaptcha_site_secret ?? '' }}"
                                                                            placeholder="ReCAPTCHA Site secret">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">Canada Post Settings</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Canada Post
                                                                            Username</label>
                                                                        <input type="text" class="form-control"
                                                                            name="canadapost_username"
                                                                            value="{{ $setting->canadapost_username ?? '' }}"
                                                                            placeholder="Canada Post Username">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Canada Post
                                                                            Password</label>
                                                                        <input type="text" class="form-control"
                                                                            name="canadapost_password"
                                                                            value="{{ $setting->canadapost_password ?? '' }}"
                                                                            placeholder="Canada Post Password">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Canada Post
                                                                            Customer Number</label>
                                                                        <input type="text" class="form-control"
                                                                            name="canadapost_customer_number"
                                                                            value="{{ $setting->canadapost_customer_number ?? '' }}"
                                                                            placeholder="Canada Post Customer Number">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Canada Post
                                                                            Contract-id</label>
                                                                        <input type="text" class="form-control"
                                                                            name="canadapost_contact_id"
                                                                            value="{{ $setting->canadapost_contact_id ?? '' }}"
                                                                            placeholder="Canada Post Contract-id">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-4">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">Canada Post
                                                                            Mode</label>
                                                                        <select type="text"
                                                                            class="form-select form-control"
                                                                            placeholder="Select a payment mode"
                                                                            id="select-tags-advanced"
                                                                            name="canadapost_mode" required>
                                                                            <option value="live"
                                                                                {{ $setting->canadapost_mode == 'live' ? 'selected' : '' }}>
                                                                                {{ __('Live') }}</option>
                                                                            <option value="sandbox"
                                                                                {{ $setting->canadapost_mode == 'sandbox' ? 'selected' : '' }}>
                                                                                {{ __('Sandbox') }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="card">
                                                        <div class="card-header">
                                                            <h3 class="card-title">UPS Settings</h3>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">UPS Client
                                                                            ID</label>
                                                                        <input type="text" class="form-control"
                                                                            name="ups_client_id"
                                                                            value="{{ $setting->ups_client_id ?? '' }}"
                                                                            placeholder="UPS Client ID">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">UPS Client
                                                                            Secret</label>
                                                                        <input type="text" class="form-control"
                                                                            name="ups_client_secret"
                                                                            value="{{ $setting->ups_client_secret ?? '' }}"
                                                                            placeholder="UPS Client Secret">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">UPS User
                                                                            ID</label>
                                                                        <input type="text" class="form-control"
                                                                            name="ups_user_id"
                                                                            value="{{ $setting->ups_user_id ?? '' }}"
                                                                            placeholder="UPS User ID">
                                                                    </div>
                                                                </div>

                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label class="form-label required">UPS Mode</label>
                                                                        <select type="text"
                                                                            class="form-select form-control"
                                                                            placeholder="Select a payment mode"
                                                                            id="select-tags-advanced" name="ups_mode"
                                                                            required>
                                                                            <option value="live"
                                                                                {{ $setting->ups_mode == 'live' ? 'selected' : '' }}>
                                                                                {{ __('Live') }}</option>
                                                                            <option value="sandbox"
                                                                                {{ $setting->ups_mode == 'sandbox' ? 'selected' : '' }}>
                                                                                {{ __('Sandbox') }}</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Google Settings --}}
                                        {{-- <div class="col-lg-6">
                                            <div class="card">
                                                <div class="card-header">
                                                    <h3 class="card-title">Google Login</h3>
                                                </div>
                                                <div class="card-body">

                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('Google client id') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="google_client_id"
                                                                    value="{{ $setting->google_client_id }}"
                                                                    placeholder="{{ __('Google client id') }}...">
                                                                @error('google_client_id')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label">{{ __('Google client secret') }}</label>
                                                                <input type="text" class="form-control"
                                                                    name="google_client_secret"
                                                                    value="{{ $setting->google_client_secret }}"
                                                                    placeholder="{{ __('Google client secret') }}...">
                                                                @error('google_client_secret')
                                                                    <span
                                                                        class="help-block text-danger">{{ $message }}</span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}

                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-12 text-center">
                                            <button type="submit" class="btn btn-success"
                                                id="updateButton">{{ __('messages.common.update') }}</button>
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
@endsection

@push('script')
    <script>
        const form = document.getElementById("settingUpdate");
        const submitButton = form.querySelector("button[type='submit']");

        form.addEventListener("submit", function() {

            $("#updateButton").html(`
                <span id="">
                    <span class="spinner-border spinner-border-sm text-white" role="status" aria-hidden="true"></span>
                    Updating Setting...
                </span>
            `);

            submitButton.disabled = true;

        });
    </script>
@endpush

@extends('admin.layouts.master')
@section('order', 'active')

@section('title') {{ $title ?? '' }} @endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

@push('style')
<style>
    .twitter-typeahead {
        flex: 1 0 0%;
    }
    .tt-menu{
        width: 100%;
        padding: .8rem;
        background: white;
        border: 1px solid #dce3ea;
        border-bottom-left-radius: 6px;
        border-bottom-right-radius: 6px;
    }
    .tt-suggestion:not(:first){
        border-top: 1px solid #dce3ea;
    }
    .tt-suggestion:not(:last-child){
        border-bottom: 1px solid #dce3ea;
    }

    .tt-suggestion:hover{
        background: var(--primary);
        color: #fff !important;
    }
    .tt-suggestion{
        padding: .8rem !important;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">

@endpush
@section('content')
    <div class="content-wrapper">    

        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title">{{ $title ?? '' }}</h4>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('admin.order.index') }}" class="btn btn-sm btn-primary btn-gradient">{{__('Back')}}</a>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-body p-0 table-responsive">
                                <form action="{{ route('admin.order.store') }}" method="post">
                                    @csrf
                                    <div class="container">
                                        <div class="row mx-md-4 mt-4">
                                            <div class="col">
                                                <div class="heading mb-4">
                                                    <h4>Select Customer</h4>
                                                </div>
        
                                                <div class="col-sm-6 col-lg-4 col-xl-4">
                                                    <select required name="user_id" class="form-control select2" required="">
                                                        @foreach ($customer as $item)
                                                            <option value="{{ $item->id }}">{{ $item->name }} {{ $item->last_name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="heading mb-4">
                                                    <h4>Enter Custom Unit Price</h4>
                                                </div>
                                                <input required type="number" class="form-control" name="custom_unit_price" id="custom_unit_price">
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Select Item Type</h4>
                                                    <p>Select the item type that you are submitting to TGA</p>
                                                </div>
                                                <div class="item_list">
                                                    @foreach (config('static_array.item_type') as $key => $item)
                                                    <div class="form-check mb-2 p-0">
                                                        <input class="form-check-input d-none" value="{{ $loop->iteration }}" @checked($loop->index==0) type="radio" name="item_type" id="item_{{ $key }}">
                                                        <label class="form-check-label p-3 rounded-4 border w-100" for="item_{{ $key }}">
                                                            <div class="d-flex align-items-center">
                                                                <div class="icon me-3">
                                                                    <i class="fa fa-id-card"></i>
                                                                </div>
                                                                <div class="ml-2">
                                                                    <div class="name fw-semibold">{{ $item }}</div>
                                                                    <div class="info">Standard size, Pokémon, thicker cards up to 180pt, and mini size sports cards</div>
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Select Submission Type</h4>
                                                </div>
                                                <div class="item_list">
                                                    @foreach (config('static_array.submission_type') as $key => $item)
                                                    <div class="form-check mb-2 p-0">
                                                        <input class="form-check-input d-none" value="{{ $loop->iteration }}" @checked($loop->index==0) type="radio" name="submission_type"
                                                            id="item_{{ $key }}">
                                                        <label class="form-check-label p-3 rounded-4 border w-100" for="item_{{ $key }}">
                                                            <div class="">
                                                                <div class="name fw-semibold">{{ $item }}</div>
                                                                <div class="info">The standard authentication and grading service for raw cards
                                                                </div>
                                                            </div>
                                                        </label>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Select Service Level</h4>
                                                    <p>Only one type of submission at one service level is permitted per submission form</p>
                                                </div>
                                                <div class="item_list rounded-top-3 border overflow-hidden">
                                                    <table class="table">
                                                        <thead class="">
                                                            <tr>
                                                                <th class="text-secondary">Service Level</th>
                                                                <th class="text-secondary text-center">Est Turnaround</th>
                                                                <th class="text-secondary text-center">Unit Price</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($serviceLevels as $serviceLevel)
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-check p-0">
                                                                            <input data-service-level="{{ $serviceLevel->name }}" data-price="" data-max-decl-value="{{ $serviceLevel->max_declare_value }}" class="form-check-input service-level-checkbox d-none" @checked($loop->first) type="radio" name="service_level" id="item_{{ $serviceLevel->id }}" value="{{ $serviceLevel->id }}">
                                                                            <label class="form-check-label service-levels w-100" for="item_{{ $serviceLevel->id }}">
                                                                                <div class="service_name">
                                                                                    <div class="name fw-semibold" style="">{{ $serviceLevel->name }}</div>
                                                                                </div>
                                                                            </label>
                                                                        </div>
                                                                    </td>
                                                                    <td class="text-center">{{ $serviceLevel->estimated_days }}<br>
                                                                        <span class="text-center" style="font-size: 0.85rem"> Business Days </span></td>
                                                                    
                                                                    <td class="text-center">{{ getDefaultCurrencySymbol() }} <span class="custom-price">0</span></td>
                                                                </tr>
                                                            @endforeach
    
                                                            {{-- <tr>
                                                                <td>
                                                                    <div class="form-check p-0">
                                                                        <input data-service-level="Value (1980-Present)" data-price="15" data-max-decl-value="500" class="form-check-input service-level-checkbox d-none" type="radio" name="service_level" id="item_14" value="14">
                                                                        <label class="form-check-label service-levels w-100" for="item_14">
                                                                            <div class="service_name">
                                                                                <div class="name fw-semibold">Value (1980-Present)</div>
                                                                            </div>
                                                                        </label>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">45<br>
                                                                    <span class="text-center" style="font-size: 0.85rem"> Business Days </span>
                                                                </td>
                                                                
                                                                <td class="text-center">$15</td>
                                                            </tr> --}}
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
    
                                    <div class="row">
                                        <div class="col-lg-8 offset-lg-2 mt-5">
                                            <div class="checkout_wrapper px-4">
                                                <div class="heading mb-4">
                                                    <h4>Enter Items</h4>
                                                    <p>Add items you want to submit to TGA for grading</p>
                                                </div>
    
                                                <div class="entry_form">
                                                    <div id="entry_input_form_wrapper">
                                                        <div class="row gy-3 gx-lg-2">
                                                            <div class="col-12">
                                                                <div class="input-group">
                                                                    <span class="input-group-text pe-0 bg-transparent border-end-0" style="border-top-right-radius: 0px; border-bottom-right-radius: 0px;">
                                                                        <i class="fa fa-search"></i>
                                                                    </span>
                                                                    <input type="text" name="item_search" id="item_search"
                                                                        class="form-control border-start-0" style="border-top-left-radius: 0px; border-bottom-left-radius: 0px;" placeholder="Search for your card to submit, if no card is found then please enter below">
                                                                </div>
                                                            </div>
                            
                                                            <div class="col-sm-6 col-lg-4 col-xl-2 mt-3">
                                                                <label for="year" class="form-label">Year</label>
                                                                <input type="text" class="form-control" id="year" placeholder="Year">
                                                            </div>
    
                                                            <div class="col-sm-6 col-lg-4 col-xl-2 mt-3">
                                                                <label for="brand" class="form-label">Brand</label>
                                                                <input type="text" class="form-control" id="brand" placeholder="Brand">
                                                            </div>
                                                            <div class="col-sm-6 col-lg-4 col-xl-2 mt-3">
                                                                <label for="cardNumber" class="form-label">Card #</label>
                                                                <input type="text" class="form-control" id="cardNumber" placeholder="Card #"
                                                                    onkeyup="this.value = this.value.replace(/\s+/g, '')">
                                                            </div>
                            
                                                            <div class="col-sm-6 col-lg-4 col-xl-3 mt-3">
                                                                <label for="playerName" class="form-label">Player/Card Name</label>
                                                                <input type="text" class="form-control" id="playerName" placeholder="Player/Card Name">
                                                            </div>
                                                            <div class="col-sm-6 col-lg-4 col-xl-3 mt-3">
                                                                <label for="notes" class="form-label">Notes</label>
                                                                <input type="text" class="form-control" id="notes" placeholder="Notes">
                                                            </div>
                            
                                                            <div class="col-sm-6 col-lg-4 col-xl-4 mt-3">
                                                                <label for="notes" class="form-label ">Quantity</label>
                                                                <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Quantity">
                                                            </div>
                            
                                                            <div class="col-12 mt-3">
                                                                <button type="button" class="btn btn-success itemAddButton">Save</button>
                                                                <button type="button" class="btn btn-light DiscardBtn">Discard</button>
                                                            </div>
                                                        </div>
                                                    </div>
    
                                                    <form action="{{ route('checkout.item.entry.store', $plan->id) }}" id="checkoutForm"
                                                        method="post">
                                                        <!-- items -->
                                                        @csrf
                                                        <div class="item_table mt-5">
                                                            <div class="table-responsive">
                                                                <table class="table align-middle mb-0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width:50%;">Items(<span
                                                                                    class="item-count">0</span>)
                                                                            </th>
                                                                            <th style="width:10%;">Qty.</th>
                                                                            <th style="width:10%;">Decl.Value</th>
                                                                            <th style="width:10%;"></th>
                                                                        </tr>
                                                                    </thead>
    
                                                                    <tbody id="item-entry-wrapper">
                                                                        
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                            
                                                        <!-- comments  -->
                                                        <div class=" mt-4">
                                                            <label for="comments" class="form-label mb-1"><strong>Comments or
                                                                    Requests</strong></label>
                                                            <p>Write anything you would like to share about your submission with us</p>
                                                            <textarea name="comments" id="comments" class="form-control" rows="4"></textarea>
                                                        </div>
                            
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 text-center">
                                            <button type="submit" class="btn btn-primary mt-2">
                                                Create Order
                                            </button>
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
@include('admin.order.edit.include.js')
@endpush




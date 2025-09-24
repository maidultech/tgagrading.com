@if($order->is_custom_order == 0)
<div class="row">
    <div class="col-lg-8 offset-lg-2 mt-5">
        <div class="checkout_wrapper px-4">
            <div class="heading mb-4">
                <h4>Items (<span class="item-count">{{count($order->details)}}</span>)</h4>
                {{-- <p>Add items you want to submit to TGA for grading</p> --}}
            </div>

            <div class="entry_form">
                {{-- <div id="entry_input_form_wrapper">
                    <div class="row gy-3 gx-lg-2">
                        <div class="col-12">
                            <div class="input-group">
                                <span
                                    class="input-group-text pe-0 bg-transparent border-end-0"
                                    style="border-top-right-radius: 0px; border-bottom-right-radius: 0px;">
                                    <i class="fa fa-search"></i>
                                </span>
                                <input type="text" name="item_search"
                                    id="item_search"
                                    class="form-control border-start-0"
                                    style="border-top-left-radius: 0px; border-bottom-left-radius: 0px;"
                                    placeholder="Search for your card to submit, if no card is found then please enter below">
                            </div>
                        </div>

                        <div class="col-sm-6 col-lg-4 col-xl-2 mt-3">
                            <label for="year" class="form-label">Year</label>
                            <input type="text" class="form-control" id="year"
                                placeholder="Year">
                        </div>

                        <div class="col-sm-6 col-lg-4 col-xl-2 mt-3">
                            <label for="brand" class="form-label">Brand</label>
                            <input type="text" class="form-control" id="brand"
                                placeholder="Brand">
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-2 mt-3">
                            <label for="cardNumber" class="form-label">Card #</label>
                            <input type="text" class="form-control"
                                id="cardNumber" placeholder="Card #">
                        </div>

                        <div class="col-sm-6 col-lg-4 col-xl-3 mt-3">
                            <label for="playerName" class="form-label">Player/Card
                                Name</label>
                            <input type="text" class="form-control"
                                id="playerName" placeholder="Player/Card Name">
                        </div>
                        <div class="col-sm-6 col-lg-4 col-xl-3 mt-3">
                            <label for="notes" class="form-label">Notes</label>
                            <input type="text" class="form-control" id="notes"
                                placeholder="Notes">
                        </div>

                        <div class="col-sm-6 col-lg-4 col-xl-4 mt-3 {{ $order->is_custom_order ?? 'd-none' }}">
                            <label for="notes" class="form-label ">Quantity</label>
                            <input type="number" id="quantity" name="quantity"
                                class="form-control" placeholder="Quantity">
                        </div>

                        <div class="col-12 mt-3">
                            <button type="button"
                                class="btn btn-success itemAddButton">Save</button>
                            <button type="button"
                                class="btn btn-light DiscardBtn">Discard</button>
                        </div>
                    </div>
                </div> --}}

                <div>
                    <div class="item_table">
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 15%">Year</th>
                                        <th style="width: 15%">Brand</th>
                                        <th style="width: 15%">Card#</th>
                                        <th style="width: 15%">Player/Card Name</th>
                                        <th style="width: 25%">Notes</th>
                                        <th style="width: 5%;">Qty.</th>
                                        <th class="text-end"  style="width: 5%;">Price</th>
                                        <th class="text-end"  style="width: 5%;">Amount</th>
                                        {{-- <th style="width:10%;">Action</th> --}}
                                    </tr>
                                </thead>

                                <tbody id="item-entry-wrapper">
                                    @foreach ($order->details as $key => $item)
                                        <tr>
                                            {{--<td>
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <img src="{{ asset('images/placeholder.jpg') }}"
                                                            class="img-fluid"
                                                            alt="">
                                                    </div>
                                                    <div>
                                                        {{ $item['item_name'] }}
                                                    </div>
                                                    --}}{{-- <input type="hidden"
                                                        name="year[{{ $item->id }}]" class="input-year"
                                                        value="{{ $item->year }}">
                                                    <input type="hidden"
                                                        name="brand[{{ $item->id }}]" class="input-brand"
                                                        value="{{ $item->brand_name }}">
                                                    <input type="hidden"
                                                        name="cardNumber[{{ $item->id }}]" class="input-cardNumber"
                                                        value="{{ $item->card }}">
                                                    <input type="hidden"
                                                        name="playerName[{{ $item->id }}]" class="input-playerName"
                                                        value="{{ $item->card_name }}">
                                                    <input type="hidden"
                                                        name="notes[{{ $item->id }}]" class="input-notes"
                                                        value="{{ $item->notes }}">
                                                    <input type="hidden"
                                                        name="quantity[{{ $item->id }}]" class="input-quantity"
                                                        value="{{ $item->qty }}"> --}}{{--
                                                </div>
                                            </td>--}}
                                            <input type="hidden" name="item_id[]" value="{{ $item->id }}">
                                            <td>
                                                <input type="text" name="year[]" class="input-year form-control" value="{{ $item->year }}">
                                            </td>
                                            <td>
                                                <input type="text" name="brand[]" class="input-brand form-control" value="{{ $item->brand_name }}">
                                            </td>
                                            <td>
                                                <input type="text" name="cardNumber[]" class="input-cardNumber form-control" value="{{ $item->card }}" onkeyup="this.value = this.value.replace(/\s+/g, '')">
                                            </td>
                                            <td>
                                                <input type="text" name="playerName[]" class="input-playerName form-control" value="{{ $item->card_name }}">
                                            </td>
                                            <td>
{{--                                                <input type="text" name="notes[]" class="input-notes form-control" value="{{ $item->notes }}">--}}
                                                <textarea name="notes[]" class="form-control" id="" cols="30" rows="1">{{ $item->notes }}</textarea>
                                            </td>
                                            <td>
                                                <span class="form-control border-0">{{ $item->qty }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="form-control border-0">{{ getDefaultCurrencySymbol() }}{{ $order->net_unit_price }}</span>
                                            </td>
                                            <td class="text-end">
                                                <span class="form-control border-0">{{ getDefaultCurrencySymbol() }}{{ $item->line_price }}</span>
                                            </td>
                                            {{-- <td>
                                                <div class="d-flex align-items-center">
                                                    <a href="#"
                                                        class="btn btn-outline-light btn-xs rounded-pill p-2 me-2 edit-item">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="#212121"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-pencil">
                                                            <path stroke="none"
                                                                d="M0 0h24v24H0z"
                                                                fill="none" />
                                                            <path
                                                                d="M4 20h4l10.5 -10.5a2.828 2.828 0 1 0 -4 -4l-10.5 10.5v4" />
                                                            <path d="M13.5 6.5l4 4" />
                                                        </svg>
                                                    </a>
                                                    <a href="#"
                                                        class="btn btn-outline-danger border-light btn-xs rounded-pill p-2 delete-item">
                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                            width="24"
                                                            height="24"
                                                            viewBox="0 0 24 24"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            stroke-width="2"
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            class="icon icon-tabler icons-tabler-outline icon-tabler-trash">
                                                            <path stroke="none"
                                                                d="M0 0h24v24H0z"
                                                                fill="none" />
                                                            <path d="M4 7l16 0" />
                                                            <path d="M10 11l0 6" />
                                                            <path d="M14 11l0 6" />
                                                            <path
                                                                d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
                                                            <path
                                                                d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
                                                        </svg>
                                                    </a>
                                                </div>
                                            </td> --}}
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- comments  -->
                    {{-- <div class=" mt-4">
                        <label for="comments" class="form-label mb-1"><strong>Comments
                                or
                                Requests</strong></label>
                        <p>Write anything you would like to share about your submission
                            with us</p>
                        <textarea name="comments" id="comments" class="form-control" rows="4">{{ $order->note }}</textarea>
                    </div> --}}

                </div>
            </div>
        </div>
    </div>
    {{-- <div class="col-12 text-center">
        <button type="submit" class="btn btn-primary mt-2">
            Edit Order
        </button>
    </div> --}}
</div>
@endif

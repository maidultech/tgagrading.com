<table class="table table-bordered">
    <thead>
        <tr>
            <th width="auto">SN</th>
            <th width="auto">Card</th>
            <th width="auto">Cert Number</th>
            <th width="auto">Centering</th>
            <th width="auto">Corners</th>
            <th width="auto">Edges</th>
            <th width="auto">Surface</th>
            <th width="auto">Final Grading</th>
            <th width="auto">NO Grade</th>
     
            <th width="auto">Reject Reason</th>
            <th width="auto">Authentic</th>
            <th width="auto">Uploaded</th>
            <th width="auto">Action</th>
        </tr>
    </thead>
    <tbody>
        
        @php
            $lastCertificateNo = null;
            $assignedCertificate = false;
            $button = false;
            $serial = 0;
        @endphp
        @foreach ($order->details as $detail)
            @foreach (range(1, $detail->qty) as $key => $item)
             
                @php
                    $cardDetails = $detail->cards()->take(1)->skip($loop->index)->first();
                    
                    if (!empty($cardDetails?->card_number) || $cardDetails?->is_no_grade == 1) {
                        $certNo = $cardDetails->card_number;
                        $hiddenCertNo = getNewCertificate2($lastCertificateNo);
                        $assignedCertificate = true;
                        $button = true;
                    } elseif ($assignedCertificate) {
                        $certNo = $lastCertificateNo = getNewCertificate2($lastCertificateNo);
                        $hiddenCertNo = null;
                        $button = true;
                        $assignedCertificate = false;
                    } elseif ($assignedCertificate == false && $loop->first) { 
                        $certNo = $lastCertificateNo = getNewCertificate2($lastCertificateNo);
                        $hiddenCertNo = null;
                        $button = true;
                        $assignedCertificate = false;
                    }else {
                        $hiddenCertNo = null;
                        $certNo = null;
                        $button = true;
                    }
                    $serial++;
                @endphp
                <div id="item{{ $serial }}">
                    <form id="certForm{{ $serial }}"
                        action="{{ route('admin.order.certificate.update', $order->id) }}"
                        method="post">   
                        @csrf   
                        <input type="hidden" name="item_id" value="item{{ $serial }}">
                        <input type="hidden" name="scrollToIndex" id="scrollToIndex" value="{{ $serial }}">
                        @php
                            $scrollToIndex = session('scrollToIndex');
                            $showOld = old() && $scrollToIndex !== null && $scrollToIndex == ($serial-1);
                        @endphp
                        <tr id="row-{{ $serial }}">
                            <td class="text-center">
                                {{ $serial }}
                            </td>
                            <td class="text-left">
                                {{ $cardDetails ? $cardDetails->item_name : $detail->item_name }}
                                <div class="my-2">
                                    <b>Year: </b>
                                    <input style="width: 10rem; height: 35px;"
                                        name="card_year" type="text" value="{{ $showOld ? old('card_year') : ($cardDetails ? $cardDetails->year : $detail->year) }}"
                                        class="form-control card_year">
                                </div>
                                <div class="my-2">
                                    <b>Brand: </b>
                                    <input style="width: 10rem; height: 35px;"
                                        name="card_brand_name" type="text" value="{{ $showOld ? old('card_brand_name') : ($cardDetails ? $cardDetails->brand_name : $detail->brand_name) }}"
                                        class="form-control card_brand_name">
                                </div>
                                <div class="my-2">
                                    <b>Card: </b>
                                    <input style="width: 10rem; height: 35px;"
                                        name="card_card" type="text" value="{{ $showOld ? old('card_card') : ($cardDetails ? $cardDetails->card : $detail->card) }}"
                                        class="form-control card_card">
                                </div>
                                <div class="my-2">
                                    <b>Player/Card Name: </b>
                                    <input style="width: 10rem; height: 35px;"
                                        name="card_card_name" type="text" value="{{ $showOld ? old('card_card_name') : ($cardDetails ? $cardDetails->card_name : $detail->card_name) }}"
                                        class="form-control card_card_name">
                                </div>
                                <div class="my-2">
                                    <b>Note: </b>
                                    <input style="width: 10rem; height: 35px;"
                                        name="card_notes" type="text" value="{{ $showOld ? old('card_notes') : ($cardDetails ? $cardDetails->notes : $detail->notes) }}"
                                        class="form-control card_notes">
                                </div>
                                 <div class="my-2">
                                    <b>Note 2: </b> <small>(4th line in label)</small><br>
                                    <input style="width: 10rem; height: 35px;"
                                        name="admin_card_notes_2" type="text" value="{{ $showOld ? old('admin_card_notes_2') : ($cardDetails ? $cardDetails->admin_notes_2 : $detail->admin_notes_2) }}"
                                        class="form-control admin_card_notes_2">
                                </div>
                                <div class="my-2">
                                    <b>Admin Note: </b>
                                    <input style="width: 10rem; height: 35px;"
                                        name="admin_card_notes" type="text" value="{{ $showOld ? old('admin_card_notes') : ($cardDetails ? $cardDetails->admin_notes : $detail->admin_notes) }}"
                                        class="form-control admin_card_notes">
                                </div>
                               
                                @if ($cardDetails?->final_grading)
                                    @php($avg_grade = collect([
                                        $cardDetails?->centering,
                                        $cardDetails?->corners,
                                        $cardDetails?->edges,
                                        $cardDetails?->surface,
                                    ])->filter()->avg())
                                    {{-- @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (int) str($cardDetails?->final_grading)->before('.')->value()) --}}
                                    @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (float) str($cardDetails?->final_grading)->value())
                                    @if($avg_grade == 10)
                                        {{ $finalGradings[(string)$grading][1] ?? '' }} 
                                    @else
                                        {{ $finalGradings[(string)$grading][0] ?? '' }}
                                    @endif
                                @endif

                                @if ($cardDetails && $cardDetails->is_no_grade != 1 && $cardDetails->is_graded != 0)
                                &nbsp;
                                <a href="{{ route('admin.order.certificate.label', ['order' => $cardDetails->order_id, 'id' => $cardDetails->id]) }}" type="print the label"  class="btn btn-sm btn-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                
                                
                                @if ($cardDetails)
                                    <input type="hidden" name="card_id" id="card_id" value="{{$cardDetails->id}}">
                                @endif
                            </td>

                            <input type="hidden" name="details_id" id="details_id" value="{{$detail->id}}">

                            <td class="text-center">
                                <input readonly name="card" type="text" data-cert-no="{{ $certNo }}" 
                                    value="{{ $cardDetails?->is_no_grade===1 ? null : $certNo }}"
                                    style="width:7rem" class="form-control card-number">
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                    name="centering" type="number" value="{{ $showOld ? old('centering') : $cardDetails?->centering }}"
                                    class="form-control">
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                    name="corners" type="number"  value="{{ $showOld ? old('corners') : $cardDetails?->corners }}"
                                    class="form-control">
                                
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5" 
                                    name="edges" type="number" value="{{ $showOld ? old('edges') : $cardDetails?->edges }}"
                                    class="form-control">
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                    name="surface" type="number" value="{{ $showOld ? old('surface') : $cardDetails?->surface }}"
                                    class="form-control">
                            </td>
                            <td>
                                <input readonly style="width:5rem" type="text" value="{{ $cardDetails?->final_grading }}" 
                                    class="form-control finalGradingNumber">
                            </td>
                            <td class="text-center">
                                <input type="hidden" name="cert_no_grade" 
                                    id="cert_no_grade_{{ $detail->id }}_{{ $key }}" value="{{$cardDetails?->is_no_grade}}">

                                <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" 
                                    data-related-id="cert_no_grade_{{ $detail->id }}_{{ $key }}" 
                                    data-existing-card="{{ isset($cardDetails) && !empty($cardDetails) ? 1 : 0 }}"
                                    class="cert_no_grade form-control" @checked($cardDetails?->is_no_grade) 
                                    name="cert_no_grade_check" type="checkbox" 
                                    data-hidden-certno="{{$hiddenCertNo}}" >
                                   
                            </td>
                            <td style="min-width:150px">
                                <textarea name="cert_no_grade_reason" class="form-control cert_no_grade_reason {{ $cardDetails?->no_grade_reason ? '' : 'd-none' }} ">{{ $cardDetails?->no_grade_reason }}</textarea>
                            </td>

                            <td>
                                <input type="hidden" name="is_authentic" 
                                    id="is_authentic_{{ $detail->id }}_{{ $key }}" value="{{$cardDetails?->is_authentic}}">

                                <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" @checked($cardDetails?->is_authentic) 
                                    data-related-isauthentic-id="is_authentic_{{ $detail->id }}_{{ $key }}" 
                                    data-existing-card="{{ isset($cardDetails) && !empty($cardDetails) ? 1 : 0 }}"
                                    type="checkbox" class="is_authentic form-control" 
                                    name="is_authentic_check" data-hidden-certno="{{$hiddenCertNo}}" >
                            </td>
                            <td>
                                @if(!empty($cardDetails?->final_grading) && $cardDetails?->final_grading > 0 && $cardDetails?->is_no_grade != 1)
                                    @if(!empty($cardDetails?->front_page))
                                        <div class="d-flex flex-column mb-3">
                                            <a href="{{ asset($cardDetails?->front_page) }}" target="_blank" class="text-center" title="Front Page">
                                                Front                                            
                                            </a>
                                        </div>
                                    @endif
                                    @if(!empty($cardDetails?->back_page))
                                        <div class="d-flex flex-column">                                        
                                            <a href="{{ asset($cardDetails?->back_page) }}" target="_blank" class="text-center" title="Back Page"> Back </a>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    @if ($button == true)
                                        <button type="submit" class="btn btn-sm btn-primary mb-2">Update</button>
                                    @endif

                                    @if(!empty($cardDetails?->final_grading) && $cardDetails?->final_grading > 0 && $cardDetails?->is_no_grade != 1)
                                        <button type="button"  data-toggle="modal"  class="browse-files-btn btn btn-sm btn-info mb-2" 
                                            data-target="#imageUploadModal" 
                                            data-card-id="{{ $cardDetails?->id }}"
                                            data-scroll-index="{{ $serial }}"
                                            data-type="front_page">Front</button>
                                        <button type="button" data-toggle="modal" class="browse-files-btn btn btn-sm btn-info mb-2" 
                                            data-target="#imageUploadModal" 
                                            data-card-id="{{ $cardDetails?->id }}"
                                            data-scroll-index="{{ $serial }}"
                                            data-type="back_page" >Back</button>
                                    @endif
                                    {{-- here order details id pass through as order, becasue maintaining the route and delete condition --}}
                                    <a id="deleteData" href="{{route('admin.order.certificate.delete', ['order' => $detail->id, 'id' => ($certNo ?? 0), 'scrollToIndex' => ($serial-1)])}}" class="btn btn-sm btn-danger mb-2">Delete</a>
                                </div>
                            </td>
                        </tr>
                    </form>
                </div>
            @endforeach
        @endforeach
    </tbody>
</table>

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
                        $button = false;
                    }
                    $serial++;
                @endphp
                <div id="item{{ $loop->iteration }}">
                    <form id="certForm{{ $loop->iteration }}"
                        action="{{ route('admin.order.certificate.update', $order->id) }}"
                        method="post">   
                        @csrf   
                        <input type="hidden" name="item_id" value="item{{ $loop->iteration }}">
                        <tr>
                            <td class="text-center">
                                {{ $serial }}
                            </td>
                            <td class="text-left">
                                {{ $detail->item_name }}

                                @if ($cardDetails?->final_grading)
                                    @php($avg_grade = collect([
                                        $cardDetails?->centering,
                                        $cardDetails?->corners,
                                        $cardDetails?->edges,
                                        $cardDetails?->surface,
                                    ])->filter()->avg())
                                    @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (int) str($cardDetails?->final_grading)->before('.')->value())

                                    @if($avg_grade == 10)
                                        {{ $finalGradings[$grading][1] ?? '' }} 
                                    @else
                                        {{ $finalGradings[$grading][0] ?? '' }}
                                    @endif
                                @endif

                                @if ($cardDetails && $cardDetails?->is_no_grade != 1)
                                &nbsp;
                                <a href="{{ route('admin.order.certificate.label', ['order' => $cardDetails->order_id, 'id' => $cardDetails->id]) }}" type="print the label"  class="btn btn-sm btn-primary mt-2">
                                    <i class="fas fa-download"></i>
                                </a>
                                @endif

                                
                                
                                @if ($cardDetails)
                                <input type="hidden" name="card_id" id="card_id" value="{{$cardDetails->id}}">
                                @endif
                            </td>

                        

                            <td class="text-center">
                                <input readonly
                                name="card[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                type="text"
                                style="width:7rem"
                                data-cert-no="{{ $certNo }}"
                                value="{{ $cardDetails?->is_no_grade===1 ? null : $certNo }}"
                                class="form-control card-number">
                                
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                name="centering[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                type="number" required
                                value="{{ old("centering.$detail->id.$cardDetails?->id",$cardDetails?->centering) }}"
                                class="form-control">
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                name="corners[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                type="number" required
                                value="{{ old("corners.$detail->id.$cardDetails?->id",$cardDetails?->corners) }}"
                                class="form-control">
                                
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                name="edges[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                type="number" required
                                value="{{ old("edges.$detail->id.$cardDetails?->id",$cardDetails?->edges) }}"
                                class="form-control">
                            </td>
                            <td>
                                <input max="10.00" style="width:5rem" min="1" step="0.5"
                                name="surface[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                type="number" required
                                value="{{ old("surface.$detail->id.$cardDetails?->id",$cardDetails?->surface) }}"
                                class="form-control">
                            </td>
                            <td>
                                <input readonly
                                style="width:5rem"
                                type="text"
                                value="{{ $cardDetails?->final_grading }}"
                                class="form-control finalGradingNumber">
                            </td>
                            <td class="text-center">
                                <input type="hidden" name="cert_no_grade[{{ $detail->id }}][{{ $cardDetails?->id }}]" 
                                    id="cert_no_grade_{{ $detail->id }}_{{ $key }}" value="{{$cardDetails?->is_no_grade}}">
                                <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" @checked($cardDetails?->is_no_grade) 
                                    data-related-id="cert_no_grade_{{ $detail->id }}_{{ $key }}" data-existing-card="{{ isset($cardDetails) && !empty($cardDetails) ? 1 : 0 }}"
                                    type="checkbox" class="cert_no_grade form-control" name="cert_no_grade_check[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                    data-hidden-certno="{{$hiddenCertNo}}" >
                                   
                            </td>
                            <td style="min-width:150px">
                                <textarea name="cert_no_grade_reason[{{ $detail->id }}][{{ $cardDetails?->id }}]" class="form-control  cert_no_grade_reason {{ $cardDetails?->no_grade_reason ? '' : 'd-none' }} ">{{ $cardDetails?->no_grade_reason }}</textarea>
                            </td>

                            <td>

                                <input type="hidden" name="is_authentic[{{ $detail->id }}][{{ $cardDetails?->id }}]" 
                                    id="is_authentic_{{ $detail->id }}_{{ $key }}" value="{{$cardDetails?->is_authentic}}">
                                <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" @checked($cardDetails?->is_authentic) 
                                    data-related-isauthentic-id="is_authentic_{{ $detail->id }}_{{ $key }}" data-existing-card="{{ isset($cardDetails) && !empty($cardDetails) ? 1 : 0 }}"
                                    type="checkbox" class="is_authentic form-control" name="is_authentic_check[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                                    data-hidden-certno="{{$hiddenCertNo}}" >
                            </td>
                            <td>
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
                                                data-type="front_page">Front</button>
                                        
                                            <button type="button" data-toggle="modal" class="browse-files-btn btn btn-sm btn-info mb-2" 
                                                data-target="#imageUploadModal" 
                                                data-card-id="{{ $cardDetails?->id }}" 
                                                data-type="back_page" >Back</button>
                                        
                                        
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </form>
                </div>
            @endforeach
        @endforeach
    </tbody>
</table>

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
        </tr>
    </thead>
    <tbody>
        @csrf
        @php
            $lastCertificateNo = null;
            $serial = 0;
        @endphp
        @foreach ($order->details as $detail)
            @foreach (range(1, $detail->qty) as $item)
                @php
                    $cardDetails = $detail->cards()->take(1)->skip($loop->index)->first();
                    $certNo = $cardDetails->card_number ?? ($lastCertificateNo = getNewCertificate2($lastCertificateNo));
                    $serial++;
                @endphp

                <tr>
                    <td class="text-center">
                        {{ $serial }}

                    </td>
                    <td>
                        {{ $detail->item_name }}
                        @if ($cardDetails)
                        &nbsp;
                        <a href="{{ route('admin.order.certificate.label', ['order' => $cardDetails->order_id, 'id' => $cardDetails->id]) }}" type="print the label"  class="btn btn-sm btn-primary mt-2">
                            <i class="fas fa-download"></i>
                        </a>
                        @endif

                        @if ($cardDetails?->final_grading)
                            @php($grading = $cardDetails?->is_authentic == 1 ? 'A' : (int) str($cardDetails?->final_grading)->before('.')->value())
                            <p class="mt-0"> {{ $finalGradings[$grading] ?? '' }}</p>
                        @endif
                    </td>
                    <td>
                        <input readonly
                        name="card[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                        type="text"
                        style="width:7rem"
                        data-cert-no="{{ $certNo }}"
                        value="{{ $cardDetails?->is_no_grade===1 ? null : $certNo }}"
                        class="form-control card-number">
                    </td>
                    <td>
                        <input max="10.00" style="width:5rem" min="1" step="0.5" required
                        name="centering[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                        type="number"
                        value="{{ old("centering.$detail->id.$cardDetails?->id",$cardDetails?->centering) }}"
                        class="form-control">
                    </td>
                    <td>
                        <input max="10.00" style="width:5rem" min="1" step="0.5" required
                        name="corners[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                        type="number"
                        value="{{ old("corners.$detail->id.$cardDetails?->id",$cardDetails?->corners) }}"
                        class="form-control">
                    </td>
                    <td>
                        <input max="10.00" style="width:5rem" min="1" step="0.5" required
                        name="edges[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                        type="number"
                        value="{{ old("edges.$detail->id.$cardDetails?->id",$cardDetails?->edges) }}"
                        class="form-control">
                    </td>
                    <td>
                        <input max="10.00" style="width:5rem" min="1" step="0.5" required
                        name="surface[{{ $detail->id }}][{{ $cardDetails?->id }}]"
                        type="number"
                        value="{{ old("surface.$detail->id.$cardDetails?->id",$cardDetails?->surface) }}"
                        class="form-control">
                    </td>
                    <td >

                        <input readonly
                        style="width:5rem"
                        type="number"
                        value="{{ $cardDetails?->final_grading }}"
                        class="form-control finalGradingNumber">



                    </td>
                    <td class="text-center">
                        <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" @checked($cardDetails?->is_no_grade) type="checkbox" class="cert_no_grade" name="cert_no_grade[{{ $detail->id }}][{{ $cardDetails?->id }}]">
                    </td>
                    <td style="min-width:150px">

                        <textarea name="cert_no_grade_reason[{{ $detail->id }}][{{ $cardDetails?->id }}]" class="form-control  cert_no_grade_reason {{ $cardDetails?->no_grade_reason ? '' : 'd-none' }} ">{{ $cardDetails?->no_grade_reason }}</textarea>
                    </td>
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

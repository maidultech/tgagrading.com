@extends('admin.layouts.master')
@section('title') {{ $title ?? '' }} @endsection
@section('card', 'active')

@section('content')
    <div class="content-wrapper">
        <div class="content">
            <div class="container-fluid pt-3">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row justify-content-between align-items-center">
                                    <div class="col-6">
                                        <h3 class="card-title">{{ $title ?? '' }}</h3>
                                    </div>
                                    {{-- <div class="col-6">
                                        WKHTML_PDF_BINARY : {{ env('WKHTML_PDF_BINARY') }}
                                        <br>
                                        WKHTML_IMG_BINARY : {{ env('WKHTML_IMG_BINARY') }}
                                    </div> --}}
                                </div>
                            </div>

                            <div class="card-body table-responsive p-0">
                                <table id="dataTables" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th width="auto">SN</th>
                                            <th width="auto">Card</th>
                                            <th width="auto">Order Number</th>
                                            <th width="auto">User</th>
                                            <th width="auto">Cert Number</th>
                                            <th width="auto">Centering</th>
                                            <th width="auto">Corners</th>
                                            <th width="auto">Edges</th>
                                            <th width="auto">Surface</th>
                                            <th width="auto">Final Grading</th>
                                            <th width="auto">Action</th>
                                            {{-- <th width="auto">NO Grade</th>
                                            <th width="auto">Reject Reason</th> --}}
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th width="auto">SN</th>
                                            <th width="auto">Card</th>
                                            <th width="auto">Order Number</th>
                                            <th width="auto">User</th>
                                            <th width="auto">Cert Number</th>
                                            <th width="auto">Centering</th>
                                            <th width="auto">Corners</th>
                                            <th width="auto">Edges</th>
                                            <th width="auto">Surface</th>
                                            <th width="auto">Final Grading</th>
                                            <th width="auto">Action</th>
                                            {{-- <th width="auto">NO Grade</th>
                                            <th width="auto">Reject Reason</th> --}}
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        @php
                                            $lastCertificateNo = null;
                                            $serial = 0;
                                        @endphp
                                        @foreach ($order as $order)
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
                                                            {{ $cardDetails?->item_name ?? $detail->item_name }}
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('admin.order.view',$order->id) }}">{{ $order->order_number }}</a>
                                                        </td>
                                                        <td>{{ $order->rUser?->name.' '.$order->rUser?->last_name }}</td>
                                                        <td>@if ($cardDetails?->final_grading) {{ $cardDetails?->is_no_grade===1 ? null : $certNo }} @endif</td>
                                                        <td>{{ $cardDetails?->centering }}</td>
                                                        <td>{{ $cardDetails?->corners }} </td>
                                                        <td>{{ $cardDetails?->edges }}</td>
                                                        <td>{{ $cardDetails?->surface }}</td>
                                                        <td>
                                                            @if ($cardDetails?->is_no_grade == 1)
                                                                No Grade
                                                                @if(!empty($cardDetails?->no_grade_reason))
                                                                    <br><small class="text-info">({{ $cardDetails?->no_grade_reason }})</small>
                                                                @endif
                                                            @else
                                                                {{ $cardDetails?->final_grading }}
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
                                                                    <small class="ml-2 text-info">( {{ $finalGradings[(string)$grading][1] ?? '' }} )</small>
                                                                @else
                                                                    <small class="ml-2 text-info">( {{ $finalGradings[(string)$grading][0] ?? '' }} )</small>
                                                                @endif
                                                                    
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if ($cardDetails && $cardDetails->is_no_grade != 1 && $cardDetails->is_graded != 0)
                                                            <div class="dropdown">
                                                                <a href="{{ route('admin.order.certificate.label', ['order' => $cardDetails->order_id, 'id' => $cardDetails->id]) }}" title="print the label" class="btn btn-xs btn-secondary  btn-sm btn-gradient"><i class="fas fa-download"></i> Download</a>
                                                            </div>
                                                            @endif
                                                        </td>
                                                        {{-- <td class="text-center">
                                                            <input data-mode="{{ $cardDetails ? 'edit' : 'add' }}" @checked($cardDetails?->is_no_grade) type="checkbox" class="cert_no_grade" name="cert_no_grade[{{ $detail->id }}][{{ $cardDetails?->id }}]">
                                                        </td>
                                                        <td style="min-width:150px">
                                    
                                                            <textarea name="cert_no_grade_reason[{{ $detail->id }}][{{ $cardDetails?->id }}]" class="form-control  cert_no_grade_reason {{ $cardDetails?->no_grade_reason ? '' : 'd-none' }} ">{{ $cardDetails?->no_grade_reason }}</textarea>
                                                        </td> --}}
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- gradingNameUpdateModal --}}
    <!-- Modal -->
<div class="modal fade" id="gradingNameUpdateModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Grading Name Update</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form action="{{ route('admin.finalgrading.update') }}" method="POST">
            @csrf
            <div class="modal-body">
                <input type="hidden" value="" name="grading_id" >
                <div class="form-group">
                    <label for="">Grading Name</label>
                    <input type="text" name="grading_name" class="form-control">
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Save changes</button>
              </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push(
'script'
)
    <script>
        
        $(document).on('click' ,'#gradingNameUpdateModalBtn', function (e) {
            $('#gradingNameUpdateModal').modal('show')
            var id = $(this).data('id');
            var name = $(this).data('name');
            $('#gradingNameUpdateModal').find('[name="grading_id"]').val(id)
            $('#gradingNameUpdateModal').find('[name="grading_name"]').val(name)
        })
    </script>
@endpush
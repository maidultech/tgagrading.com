@extends('admin.layouts.master')
@section('order', 'active')

@section('title') {{ $title ?? '' }} @endsection

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-typeahead/2.11.2/jquery.typeahead.min.css">
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .tga-printable-box img.background-image {
        position: absolute;
        opacity: .1;
        width: 90%;
        top: 0;
        left: 0;
        right: 0;
        margin: auto;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>
@push('style')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css">
    <style>
        .twitter-typeahead {
            flex: 1 0 0%;
        }


        .tt-menu {
            width: 100%;
            padding: .8rem;
            background: white;
            border: 1px solid #dce3ea;
            border-bottom-left-radius: 6px;
            border-bottom-right-radius: 6px;
        }

        .tt-suggestion:not(:first) {
            border-top: 1px solid #dce3ea;
        }

        .tt-suggestion:not(:last-child) {
            border-bottom: 1px solid #dce3ea;
        }

        .tt-suggestion:hover {
            background: var(--primary);
            color: #fff !important;
        }

        .tt-suggestion {
            padding: .8rem !important;
        }

        /*  */

    </style>
@endpush
@section('content')
    <div class="content-wrapper pb-5">
        <div class="content">
            <div class="container-fluid pt-3 pb-5">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-6">
                                        <h4 class="card-title">{{ $title ?? '' }}</h4>
                                    </div>
                                    <div class="col-6 text-right">
                                        <a href="{{ route('admin.order.index') }}"
                                            class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="row justify-content-center p-0 m-0">
                                    <div class="col-md-12 mt-4">
                                        <form id="certForm"
                                            action="{{ route('admin.order.certificate.update', $order->id) }}"
                                            method="post">
                                            @include('admin.order.certificate_body')
                                            <button class="btn btn-primary">Update</button>
                                        </form>
                                    </div>
                                    <div class="col-12">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div id="label-print-wrapper">

                        </div>
                    </div>
                </div>
            </div>

        </div>


    </div>


@endsection


@push('script')
    <script>
        $(document).on('click', '.cert_no_grade', function() {

            if ($(this).is(':checked')) {
                $(this).parents('tr').find('.card-number').val('');
                $(this).parents('tr').find('.cert_no_grade_reason').removeClass('d-none');
                // this sel col disable all input
                $(this).parents('tr').find('input').not(this).prop('readonly', true);
                if($(this).parents('tr').prevAll('tr').length!=0){
                    $(this).parents('tr').nextAll('tr').find('.card-number').map(function(index, elem) {
                        $(this).val(parseInt($(this).val()) - (1))
                    })
                }
                console.log('if');

            } else {
                $(this).parents('tr').find('.cert_no_grade_reason').addClass('d-none');
                // get this card number's pre rows checked cert_no_grade element count
                $(this).parents('tr').find('input').not(this).not('.finalGradingNumber,.card-number').prop('readonly', false);
                var sel = $(this).parents('tr').find('.card-number');
                var count = $(this).parents('tr').prevAll('tr').find('.cert_no_grade:checked').length
                sel.val(parseInt(sel.data('cert-no')) - count)
                // console.log($(this).parents('tr').prevAll('tr').length);

                if($(this).parents('tr').prevAll('tr').length!=0){
                    $(this).parents('tr').nextAll('tr').find('.card-number').map(function(index, elem) {
                        $(this).val(parseInt($(this).data('cert-no')) - count)
                    })
                }

            }
        })
        
        $(document).on('click', '.getLabelBtn', function() {
            var id = $(this).data('id');
            var order = $(this).data('order');

            $.ajax({
                url: '',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        if (response.data) {
                            $('#label-print-wrapper').html(response.data);

                            // Render PDF with html2canvas if needed, otherwise skip to server download
                            html2canvas(document.querySelector(".mini_card")).then(canvas => {
                                var image = canvas.toDataURL("image/jpeg", 1.0);
                                $.ajax({
                                    url: '',
                                    type: 'POST',
                                    data: {image: image},
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    success: function(res) {
                                        if (res.pdf) {
                                            window.open(res.pdf, '_blank');
                                        } else {
                                            toastr.error(res.message || 'Failed to generate PDF');
                                        }
                                    },
                                    error: function() {
                                        toastr.error('Error processing PDF');
                                    }
                                });
                            });
                        }
                    } else {
                        toastr.error(response.message);
                    }
                },
                error: function() {
                    toastr.error('Error fetching label');
                }
            });
        });

    </script>
@endpush

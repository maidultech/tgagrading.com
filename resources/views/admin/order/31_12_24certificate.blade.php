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

    td {
        vertical-align: middle !important;
    }
</style>
{{-- <script src="https://cdn.jsdelivr.net/npm/dwt@18.2.0/dist/dynamsoft.webtwain.min.js"></script> --}}
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
        .table td {
            padding: 0.25rem;
            text-align: center;

        }
        .table td input { display: inline;}

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
                                    <div class="col-6 d-flex justify-content-end align-items-center">
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-gradient mr-2" data-toggle="modal" data-target="#addNewCardModal">Add New Card</a>
                                        <a href="{{ route('admin.order.index') }}"
                                            class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" style="padding-left: 5px;padding-right: 5px;">
                                <div class="row justify-content-center p-0 m-0">
                                    <div class="col-md-12 table-responsive">
                                        @include('admin.order.certificate_body')
                                    </div>
                                    <div class="col-12">
                                        {{-- <div id="dwtcontrolContainer" style="width: 600px; height: 400px;"></div> --}}
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

    <div class="modal fade" id="imageUploadModal" tabindex="-1" aria-labelledby="imageUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageUploadModalLabel">Upload Image From Your Device</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('admin.order.certificate.upload.image', $order->id) }}" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="card_id" id="card_id">
                        <input type="hidden" name="page_type" id="page_type">
                        <div class="form-group">
                            <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" id="image" class="form-control form-control-file"
                                accept=".jpg, .jpeg, .png, .webp" required>
                        </div>
                        <div class="form-group float-right">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-success">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addNewCardModal" tabindex="-1" role="dialog" aria-labelledby="addNewCardModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addNewCardModalLabel">Add New Card</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{route('admin.order.certificate.card.create', $order->id)}}" method="post">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 col-lg-4 col-xl-2">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" class="form-control" id="year" name="year" placeholder="Year"
                                    required min="1900" max="2100">
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-2">
                                <label for="brand" class="form-label">Brand</label>
                                <input type="text" class="form-control" id="brand" name="brand"
                                    placeholder="Brand" required>
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-2">
                                <label for="cardNumber" class="form-label">Card #</label>
                                <input type="text" class="form-control" id="cardNumber" name="cardNumber"
                                    placeholder="Card #" onkeyup="this.value = this.value.replace(/\s+/g, '')">
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <label for="playerName" class="form-label">Player/Card Name</label>
                                <input type="text" class="form-control" id="playerName" name="playerName"
                                    placeholder="Player/Card Name" required>
                            </div>
                            <div class="col-sm-6 col-lg-4 col-xl-3">
                                <label for="notes" class="form-label">Notes</label>
                                <input type="text" class="form-control" id="notes" name="notes"
                                    placeholder="Notes">
                            </div>
                        </div>

                       
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Card</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    {{-- <script>
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
</script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            @if (session('scrollToBottom'))
                window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
            @endif
        });
    </script>
    <script>
        $(document).ready(function() {
            $('.card-number').each(function() {
                if (!$(this).data('initial-value')) {
                    $(this).data('initial-value', $(this).val());
                }
            });

            $('.cert_no_grade:not(:checked)').each(function() {
                let $row = $(this).closest('tr');
                $row.find('.cert_no_grade_reason').addClass('d-none');
            });

            $('.cert_no_grade:checked').each(function() {
                let $row = $(this).closest('tr');
                $row.find('.cert_no_grade_reason').removeClass('d-none');
                $row.find('input').not(this).prop('readonly', true);
            });
        });

        $(document).on('click', '.cert_no_grade', function() {
            let $row = $(this).closest('tr');
            let $cardNumberInput = $row.find('.card-number');

            if ($(this).is(':checked')) {
                $cardNumberInput.val('');
                $row.find('.cert_no_grade_reason').removeClass('d-none');
                $row.find('.cert_no_grade_reason').empty();

                $row.find('input').not(this).prop('readonly', true);
            } else {
                let isExistingCard = $(this).data('existing-card') == 1;
                if (isExistingCard) {
                    $cardNumberInput.val($(this).data('hidden-certno'));
                } else {
                    $cardNumberInput.val($cardNumberInput.data('initial-value'));
                }
                $row.find('.cert_no_grade_reason').addClass('d-none');
                $row.find('input').not(this).not('.finalGradingNumber, .card-number').prop('readonly', false);
            }
        });

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
                                    data: {
                                        image: image
                                    },
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]')
                                            .attr('content')
                                    },
                                    success: function(res) {
                                        if (res.pdf) {
                                            window.open(res.pdf, '_blank');
                                        } else {
                                            toastr.error(res.message ||
                                                'Failed to generate PDF');
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
    <script>
        $(document).on('change', '.cert_no_grade', function() {
            var relatedId = $(this).data('related-id');
            var hiddenInput = $(`#${relatedId}`);

            if ($(this).is(':checked')) {
                hiddenInput.val(1);
            } else {
                hiddenInput.val('');
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            const inputGroups = document.querySelectorAll("tr");
            inputGroups.forEach((group) => {
                const inputs = group.querySelectorAll("input[type='number']");

                inputs.forEach((input) => {
                    input.addEventListener("input", function() {
                        const isAnyFilled = Array.from(inputs).some((field) => field.value
                            .trim() !== "");

                        inputs.forEach((field) => {
                            if (isAnyFilled) {
                                field.setAttribute("required", "required");
                            } else {
                                field.removeAttribute("required");
                            }
                        });
                    });
                });
            });
        });

        function setPageType(pageType) {
            const browseFilesButtons = document.querySelectorAll('.browse-files-btn');
            const openScannerButtons = document.querySelectorAll('.open-scanner-btn');

            browseFilesButtons.forEach(button => {
                button.setAttribute('data-type', pageType);
            });

            openScannerButtons.forEach(button => {
                button.setAttribute('data-type', pageType);
            });
        }

        $(function() {
            $(document).on('click', '.browse-files-btn', function() {
                $('#imageUploadModal #card_id').val('');
                $('#imageUploadModal #page_type').val('');

                const cardId = $(this).data('card-id');
                const pageType = $(this).attr('data-type');
                console.log(pageType);

                $('#imageUploadModal #card_id').val(cardId);
                $('#imageUploadModal #page_type').val(pageType);
            });

            $(document).on('click', '.open-scanner-btn', function() {
                const cardId = $(this).data('card-id');
                const pageType = $(this).attr('data-type');
                console.log(pageType);
            });
        });
        $(document).on('click', '#noGrade', function () {
            let $modal = $(this).closest('#addNewCardModal');
            let $cardNumberInput = $modal.find('#certNo');
            let $reasonTextarea = $modal.find('#reason');
            let $reasonDiv = $modal.find('#noGradeReason');
            let $specificInputs = $modal.find('#surface, #edges, #corners, #centering');

            if ($(this).is(':checked')) {
                $cardNumberInput.val('');
                $reasonDiv.removeClass('d-none');
                $specificInputs.prop('readonly', true);
            } else {
                $cardNumberInput.val($cardNumberInput.data('initial-value'));
                $reasonDiv.addClass('d-none');
                $specificInputs.prop('readonly', false);
            }
        });
    </script>

    {{-- <script>
    Dynamsoft.WebTwainEnv.ProductKey = "t01878AUAAB/HoxClzvusLEDnYEMJtyAOxPgGdwahdOyKoWTSMAnrli25nmU/yO1fI0OOi5Qix9WoM30qrPaKGWRinV1jK1bbPyc7OK2906S9Ex2cvOQUicswrKetxQecgOcC2O86FMYaWGvZAK8h1wYPoAeYA5hXAzzgcBX7zadsA1K/PXOgs5MdnNbeWQekjRMdnLzkTAG5B9ExrXZcA4L65hQAPcAOAeQf2S4gsgXoAbYDVBUaJH4B4xArsA==";
    Dynamsoft.WebTwainEnv.ResourcesPath = "https://cdn.jsdelivr.net/npm/dwt@18.2.0/dist/";
</script>
<script>
    Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainReady', function () {
        console.log("Web TWAIN is ready.");
        const scanner = Dynamsoft.WebTwainEnv.GetWebTwain('dwtcontrolContainer');

        if (scanner) {
            $(document).on('click', '.open-scanner-btn', function () {
                const cardId = $(this).data('card-id');
                const pageType = $(this).data('type');

                console.log('Scanning with Card ID:', cardId, 'and Page Type:', pageType);

                scanner.AcquireImage({
                    IfShowUI: false,
                    IfFeederEnabled: true,
                    IfDuplexEnabled: false,
                    PixelType: scanner.EnumDWT_PixelType.TWPT_RGB,
                    Resolution: 200,
                    OnSuccess: function () {
                        const base64Image = scanner.GetImageAsBase64(0, Dynamsoft.EnumDWT_ImageType.IT_PNG);

                        const formData = new FormData();
                        formData.append('image', base64Image);
                        formData.append('cardId', cardId);
                        formData.append('pageType', pageType);

                        fetch('/upload', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                        })
                            .then((response) => response.json())
                            .then((data) => console.log('Upload successful:', data))
                            .catch((error) => console.error('Error:', error));
                    },
                    OnFailure: function (errorCode, errorString) {
                        console.error('Scan failed:', errorString);
                    },
                });
            });
        } else {
            console.error('Scanner instance not found.');
        }
    });

    Dynamsoft.WebTwainEnv.RegisterEvent('OnWebTwainNotReady', function () {
        console.error("Web TWAIN is not ready.");
    });
</script> --}}
@endpush

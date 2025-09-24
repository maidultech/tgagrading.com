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

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #e9ecef;
        opacity: 1;
        pointer-events: none;
        cursor: default;
    }
</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.4/jspdf.min.js"></script>

{{-- <script src="{{ asset('assets/scanner/resources/dynamsoft.webtwain.config.js') }}"></script>
<script src="{{ asset('assets/scanner/resources/dynamsoft.webtwain.initiate.js') }}"></script>
<link rel="stylesheet" href="{{ asset('assets/scanner/resources/src/dynamsoft.webtwain.css') }}">
<link rel="stylesheet" href="{{ asset('assets/scanner/resources/src/dynamsoft.webtwain.viewer.css') }}"> --}}
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

        .table td input {
            display: inline;
        }

        /*  */
        #imagePreviewModal .modal-body {
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: rgba(0, 0, 0, 0.9);
        }
        #imgPreviewWrapper .modal-content .modal-header{
            background-color: rgba(0, 0, 0, 0.9) !important;
        }
        #imagePreviewModal .modal-body img {
            max-width: 100%;
            height: auto;
        }
    </style>
@endpush
@section('orderDropdown', 'menu-open')

@if($order->status == 0 && !is_null($order->status))
    @section('order-pending', 'active')
@elseif($order->status == 10)
    @section('order-received', 'active')
@elseif($order->status == 15)
    @section('grading-processing', 'active')
@elseif($order->status == 20)
    @section('grading-complete', 'active')
@elseif($order->status == 25)
    @section('encapsulation-processing', 'active')
@elseif($order->status == 30)
    @section('encapsulation-complete', 'active')
@elseif($order->status == 35)
    @section('order-shipping', 'active')
{{-- @elseif(request()->status == 40)
    @section('order-shipped', 'active') --}}
@endif

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
                                        @if($order->status <= 30)
                                        <a href="javascript:void(0)" class="btn btn-sm btn-primary btn-gradient mr-2"
                                            data-toggle="modal" data-target="#addNewCardModal">Add New Card</a>
                                        @endif
                                        @if (str_contains(url()->previous(), 'outgoing-order'))
                                            <a href="{{ url()->previous() }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                        @else
                                            @if($order->status == 0 && !is_null($order->status))
                                                <a href="{{ route('admin.order.index', ['status' => 0]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @elseif($order->status == 10)
                                                <a href="{{ route('admin.order.index', ['status' => 10]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @elseif($order->status == 15)
                                                <a href="{{ route('admin.order.index', ['status' => 15]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @elseif($order->status == 20)
                                                <a href="{{ route('admin.order.index', ['status' => 20]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @elseif($order->status == 25)
                                                <a href="{{ route('admin.order.index', ['status' => 25]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @elseif($order->status == 30)
                                                <a href="{{ route('admin.order.index', ['status' => 30]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @elseif($order->status == 35)
                                                <a href="{{ route('admin.order.index', ['status' => 35]) }}"
                                                class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" style="padding-left: 5px;padding-right: 5px;">
                                <div class="row justify-content-center p-0 m-0">
                                    <div class="col-md-12 table-responsive">
                                        @include('admin.order.certificate_body')
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
                        <input type="hidden" name="scrollToIndex" id="scrollIndex">
                        <div class="form-group">
                            <label for="image" class="form-label">Image <span class="text-danger">*</span></label>
                            <input type="file" name="image" id="image" class="form-control form-control-file"
                                accept=".jpg, .jpeg, .png, .webp" required>
                        </div>
                        <div class="row justify-content-center imgPreviewWrapper mb-2">
                            <div class="col-12">
                                <img src="" class="img-fluid img-thumbnail preview-img" alt="Image">
                            </div>
                        </div>
                        <div class="form-group float-right button-group">
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
                <form action="{{ route('admin.order.certificate.card.create', $order->id) }}" method="post">
                    @csrf
                    <input type="hidden" name="order_id" id="order_id" value="{{ $order->id }}">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6 col-lg-4 col-xl-2">
                                <label for="year" class="form-label">Year</label>
                                <input type="number" class="form-control" id="year" name="year"
                                    placeholder="Year" required min="1900" max="2100">
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
    
    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style=" background-color: rgba(0, 0, 0, 0.9) !important; border: none;">
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <img id="modalImage" class="img-fluid" src="" alt="Image Preview">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="scanModal" tabindex="-1" role="dialog" aria-labelledby="scanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="scanModalLabel">Scan Image</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="{{ route('admin.order.certificate.upload.scanned.image', $order->id) }}" method="post"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="page_type" value="" id="modalPageType">
                        <input type="hidden" name="card_id" value="" id="modalCardId">
                        <input type="hidden" name="image" id="scannedImage">
                        <div class="row">
                            <div class="col-md-4">
                                <h6>Select Device</h6>
                                <select class="form-control mb-3" id="devicesSelect">
                                    <option class="d-none">Please select a device</option>
                                </select>
                                <button class="btn btn-primary btn-block" id="scanImagesButton" type="button" onclick="acquireImageFromTwainScanner()">Scan Images</button>
                            </div>
                            <div class="col-md-8 text-center">
                                <h6>Preview Image</h6>
                                <div class="border p-3 bg-light" style="max-width: 730px; height: 535px;">
                                    <img id="previewImage" src="" class="img-fluid" alt="Scanned Image" style="width: 500px; height: 500px; display: none;">
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 text-center">
                            <button type="button" class="btn btn-success" id="downloadButton">Download</button>
                            <button type="submit" class="btn btn-primary">Upload to Server</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('scrollToIndex'))
                let currentIndex = {{ session('scrollToIndex') }};
                let nextRow = document.querySelector(`#row-${currentIndex + 1}`);
                let currentRow = document.querySelector(`#row-${currentIndex}`);

                if (nextRow) {
                    nextRow.scrollIntoView({ behavior: 'smooth' });
                } else if (currentRow) {
                    currentRow.scrollIntoView({ behavior: 'smooth' });
                }
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
                if ($row.find('.card_notes, .admin_card_notes, .admin_card_notes_2, .card_year, .card_brand_name, .card_card, .card_card_name').length > 0) {
                    $row.find('input').not(this).not('.card_notes, .admin_card_notes, .admin_card_notes_2, .card_year, .card_brand_name, .card_card, .card_card_name').prop('readonly', true);
                } else {
                    $row.find('input').not(this).prop('readonly', true);
                }
            });

            $('.is_authentic:checked').each(function() {
                let $row = $(this).closest('tr');
                if ($row.find('.card_notes, .admin_card_notes, .admin_card_notes_2, .card_year, .card_brand_name, .card_card, .card_card_name').length > 0) {
                    $row.find('input').not(this).not('.card_notes, .admin_card_notes, .admin_card_notes_2, .card_year, .card_brand_name, .card_card, .card_card_name').prop('readonly', true);
                } else {
                    $row.find('input').not(this).prop('readonly', true);
                }
            });
        });

        $(document).on('click', '.cert_no_grade', function() {
            let $row = $(this).closest('tr');
            let $cardNumberInput = $row.find('.card-number');

            if ($(this).is(':checked')) {
                $cardNumberInput.val('');
                $row.find('.cert_no_grade_reason').removeClass('d-none');
                $row.find('.cert_no_grade_reason').empty();
                if ($row.find('.card_notes, .admin_card_notes, .admin_card_notes_2, .card_year, .card_brand_name, .card_card, .card_card_name').length > 0) {
                    $row.find('input').not(this).not('.card_notes, .admin_card_notes, .admin_card_notes_2, .card_year, .card_brand_name, .card_card, .card_card_name').prop('readonly', true);
                } else {
                    $row.find('input').not(this).prop('readonly', true);
                }
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

        $(document).on('click', '.is_authentic', function() {
            let $row = $(this).closest('tr');
            if (!$row.data('initialValues')) {
                let initialValues = {};
                $row.find(
                    'input[name*="centering"], input[name*="corners"], input[name*="edges"], input[name*="surface"]'
                    ).each(function() {
                    initialValues[$(this).attr('name')] = $(this).val();
                });
                $row.data('initialValues', initialValues);
            }

            let inputs = $row.find(
                'input[name*="centering"], input[name*="corners"], input[name*="edges"], input[name*="surface"], input[name*="cert_no_grade"]'
                );
            let initialValues = $row.data('initialValues');

            if ($(this).is(':checked')) {
                inputs.val('');
                inputs.prop('readonly', true);
            } else {
                inputs.each(function() {
                    let name = $(this).attr('name');
                    if (initialValues[name] !== undefined) {
                        $(this).val(initialValues[name]);
                    }
                });
                inputs.prop('readonly', false);
            }
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

        $(document).on('change', '.is_authentic', function() {
            var relatedId = $(this).data('related-isauthentic-id');
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

            browseFilesButtons.forEach(button => {
                button.setAttribute('data-type', pageType);
            });

            openScannerButtons.forEach(button => {
                button.setAttribute('data-type', pageType);
            });
        }

        $(document).on('change', '#image', function() {
            const reader = new FileReader();
            
            reader.addEventListener("load", function(evt) {
                document.querySelector(".imgPreviewWrapper img.preview-img").src = evt.target.result;
                $('.imgPreviewWrapper').show();
            }); 
                
            reader.readAsDataURL(this.files[0]);
        })
        
        $(function() {
            $(document).on('click', '.browse-files-btn', function() {
                $('#imageUploadModal #card_id').val('');
                $('#imageUploadModal #page_type').val('');
                $('.imgPreviewWrapper').hide();
                const cardId = $(this).data('card-id');
                const scrollIndex = $(this).data('scroll-index');
                const pageType = $(this).attr('data-type');

                $('#imageUploadModal #card_id').val(cardId);
                $('#imageUploadModal #page_type').val(pageType);
                $('#imageUploadModal #scrollIndex').val(scrollIndex);
                
                // Clear any existing buttons inside .button-group
                $('.scan_front, .scan_back').remove();

                // Append the appropriate button based on pageType
                if (pageType === 'front_page') {
                    $('.button-group').append(`<button type="button" class="btn btn-info scan_front scan_btn" data-page-type="front_page" data-scroll-index="${scrollIndex}" data-card-id="${cardId}">Front Scan</button>`);
                } else if (pageType === 'back_page') {
                    $('.button-group').append(`<button type="button" class="btn btn-info scan_back scan_btn" data-page-type="back_page" data-scroll-index="${scrollIndex}" data-card-id="${cardId}">Back Scan</button>`);
                }
            });
        });

        $(document).on('click', '#noGrade', function() {
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

        document.addEventListener('DOMContentLoaded', function() {
            var modal = $('#imagePreviewModal');
            var modalImg = $('#modalImage');

            $('.preview-img').on('click', function() {
                modalImg.attr('src', $(this).attr('src'));
                modal.modal('show');
            });

            modal.on('hidden.bs.modal', function() {
                modalImg.attr('src', '');
            });
        });
    </script>
    <script>
        // $(document).on('click', '.scan_btn', function() {
        //     let pageType = $(this).data('page-type');
        //     let cardId = $(this).data('card-id');
        //     $('#modalPageType').val(pageType);
        //     $('#modalCardId').val(cardId);
        //     $('#imageUploadModal').modal('hide');
        //     $('#scannedImage').val('');
        //     $('#previewImage').attr('src', '').css('display', 'none');
        // });
        $(document).on('click', '.scan_btn', function() {
            let pageType = $(this).data('page-type');
            let cardId = $(this).data('card-id');
            let scrollIndex = $(this).data('scroll-index');

            // Send values to Laravel via AJAX
            $.ajax({
                url: '/admin/set-scan-session', // A new route to store session data
                method: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content'), // CSRF token
                    order_id: '{{$order->id}}',
                    page_type: pageType,
                    card_id: cardId,
                    scroll_index: scrollIndex,
                    is_manual: 0
                },
                success: function() {
                    // Redirect to the clean URL after session is set
                    window.location.href = '/admin/scan-card';
                }
            });
        });
    </script>
@endpush

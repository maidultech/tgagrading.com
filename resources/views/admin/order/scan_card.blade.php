@extends('admin.layouts.master')
@section('order', 'active')

@section('title') {{ $title ?? '' }} @endsection

@push('style')
<style>
    .progress {
        border-radius: 10px;
    }
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
                                        @if($is_manual == 1)
                                        <a href="{{  route('admin.manual-label.index') }}"
                                            class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                        @else
                                        <a href="{{  route('admin.order.certificate.index', $order_id) }}"
                                            class="btn btn-sm btn-primary btn-gradient">{{ __('Back') }}</a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="card-body" style="padding-left: 5px;padding-right: 5px;">
                                <div class="row justify-content-center p-0 m-0">
                                    <div class="col-md-12 table-responsive">
                                            @if($is_manual == 1)
                                                <form action="{{ route('admin.manual-label.upload.scanned.image') }}" method="post"
                                                    enctype="multipart/form-data">
                                            @else
                                                <form action="{{ route('admin.order.certificate.upload.scanned.image', $order_id) }}" method="post"
                                                    enctype="multipart/form-data">
                                            @endif
                                            @csrf
                                            <div>
                                                <input type="hidden" name="page_type" value="{{$pageType}}">
                                                <input type="hidden" name="card_id" value="{{$cardId}}">
                                                <input type="hidden" name="scrollToIndex" value="{{$scrollToIndex}}">
                                                <input type="hidden" name="image" id="scannedImage">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <h6>Select Device</h6>
                                                        <select class="form-control mb-3" id="devicesSelect">
                                                            <option class="d-none">Please select a device</option>
                                                        </select>
                                                        <button class="btn btn-primary btn-block" id="scanImagesButton" type="button" onclick="acquireImageFromTwainScanner()">Scan Images</button>
                                                        <table class="table table-bordered mt-4">
                                                            <tbody>
                                                                <tr>
                                                                    <td><strong>Label</strong></td>
                                                                    <td>
                                                                        @if($is_manual == 1)
                                                                            Manual Label
                                                                        @else
                                                                            Order Card Label
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @if($is_manual == 1)
                                                                    <tr>
                                                                        <td><strong>Manual Label ID</strong></td>
                                                                        <td>
                                                                            <a href="{{ route('admin.manual-label.edit', $cardId) }}" class="text-primary font-weight-bold">
                                                                                {{$cardId}}
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @else
                                                                    <tr>
                                                                        <td><strong>Order ID</strong></td>
                                                                        <td>
                                                                            <a href="{{ route('admin.order.certificate.index', $order_id) }}" class="text-primary font-weight-bold">
                                                                                {{$order_id}}
                                                                            </a>
                                                                        </td>
                                                                    </tr>
                                                                @endif
                                                                @if($is_manual != 1)
                                                                <tr>
                                                                    <td><strong>Card ID</strong></td>
                                                                    <td>{{$cardId}}</td>
                                                                </tr>
                                                                @endif
                                                                <tr>
                                                                    <td><strong>Page Type</strong></td>
                                                                    <td>
                                                                        {{ $pageType == 'back_page' ? 'Back Page' : 'Front Page' }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Card</strong></td>
                                                                    @if($is_manual == 1)
                                                                        <td>
                                                                            {{ $card->item_name }}
                                                                            @if ($card->notes)
                                                                                <div class="my-2">
                                                                                <b>Note: </b>
                                                                                    {{$card->notes}}
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                    @else
                                                                        <td>
                                                                            {{ $card->item_name ?? $card->details->item_name }}
                                                                            @if ($card->notes)
                                                                                <div class="my-2">
                                                                                <b>Note: </b>
                                                                                    {{$card->notes}}
                                                                                </div>
                                                                            @elseif($card->details->notes)
                                                                                <div class="my-2">
                                                                                <b>Note: </b>
                                                                                    {{$card->details->notes}}
                                                                                </div>
                                                                            @else
                                                                            @endif
                                                                        </td>
                                                                    @endif
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Cert Number</strong></td>
                                                                    <td>
                                                                        {{ $card->card_number }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Centering</strong></td>
                                                                    <td>
                                                                        {{ $card->centering }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Corners</strong></td>
                                                                    <td>
                                                                        {{ $card->corners }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Edges</strong></td>
                                                                    <td>
                                                                        {{ $card->edges }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Surface</strong></td>
                                                                    <td>
                                                                        {{ $card->surface }}
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td><strong>Final Grading</strong></td>
                                                                    <td>
                                                                        @if($is_manual == 1)
                                                                            {{ $card->grade }} ({{ $card->grade_name }})
                                                                        @else
                                                                            @php($avg_grade = collect([
                                                                                $card->centering,
                                                                                $card->corners,
                                                                                $card->edges,
                                                                                $card->surface,
                                                                            ])->filter()->avg())
                                                                            @php($grading = $card->is_authentic == 1 ? 'A' : (float) str($card->final_grading)->value())
                                                                            @if($avg_grade == 10)
                                                                                {{ $card->final_grading }} ({{ $finalGradings[(string)$grading][1] ?? '' }})
                                                                            @else
                                                                                {{ $card->final_grading }} ({{ $finalGradings[(string)$grading][0] ?? '' }})
                                                                            @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>                                                        
                                                    </div>
                                                    <div class="col-md-8 d-flex flex-column align-items-center">
                                                        <h6>Preview Image</h6>
                                                        <div class="border p-3 bg-light text-center" style="height: 730px; width: 100%;">
                                                            <img id="previewImage" src="" class="img-fluid" alt="Scanned Image" 
                                                                style="max-width: 600px; height: 700px; width: 100%; object-fit: contain; display: none;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-3 text-center">
                                                    <button type="button" class="btn btn-success" id="downloadButton">Download</button>
                                                    {{-- <button type="submit" class="btn btn-primary">Upload to Server</button> --}}
                                                    <button type="button" id="uploadBtn" class="btn btn-primary">Upload to Server</button>
                                                </div>
                                                <div style="width: 60%; margin: auto;">
                                                    <div class="progress mt-3" style="display: none;">
                                                        <div id="uploadProgress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 1%;">1%</div>
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
            </div>
        </div>
    </div>
@endsection


@push('script')
<script src="{{ asset('assets/vintasoft/Vintasoft.Shared.js') }}"></script>
<script src="{{ asset('assets/vintasoft/Vintasoft.Twain.js') }}"></script>

<script>
    // Global variables
    var _devices = [];
    var _twainService;
    var _deviceManager;
    var _deviceManagerInitSettings;

    // Initialize TWAIN SDK and device manager
    function __initializeTwainService() {
        // Register VintaSoft Web TWAIN SDK
        console.log(Vintasoft.Twain.WebTwainGlobalSettingsJS.getUserOS());

        // register the evaluation version of VintaSoft Web TWAIN service
        Vintasoft.Twain.WebTwainGlobalSettingsJS.register(
            "True Grade Authentication Inc.",
            "tgagrading.com",
            "vUvYyrF0k8Zy9HZRnRN5f3durUVA8xTZkuCZ3wtJ7bMfnRTq7cERdpdi8P1NeNYqZMRIkmncIyyBmZWoQun7zKy8qUJwT8JX1LshotDEqu3YicVXvfb9wTq2YqZz4JHiV4DaqTijP3LKeClXc3C5TOsLk9IN+crtiMreRBvm8B1U"
        );


        // Set the correct URL of the TWAIN service
        let serviceUrl = "https://localhost:25329/api/VintasoftTwainApi";
        _twainService = new Vintasoft.Shared.WebServiceControllerJS(serviceUrl);
        _deviceManager = new Vintasoft.Twain.WebTwainDeviceManagerJS(_twainService);
        _deviceManagerInitSettings = new Vintasoft.Twain.WebTwainDeviceManagerInitSettingsJS();
    }

    // Function to open TWAIN device manager and get the device list
    function __openTwainDeviceManagerAndGetDeviceList() {
        // Ensure device manager is initialized
        if (!_deviceManager) {
            alert("TWAIN device manager is not initialized!");
            return;
        }

        // Set TWAIN 2 compatibility
        _deviceManagerInitSettings.set_IsTwain2Compatible(true);

        // Get the device list element
        var deviceSelectElement = document.getElementById('devicesSelect');
        // Clear the device list
        deviceSelectElement.options.length = 0;

        // Disable "Scan images" button
        document.getElementById('scanImagesButton').disabled = true;

        try {
            // Open TWAIN device manager
            if (_deviceManager && !_deviceManager.get_IsOpened()) {
                _deviceManager.open(_deviceManagerInitSettings);
            }
            console.log('VintaSoft Web TWAIN service version: ' + _deviceManager.get_TwainServiceVersion());
        } catch (ex) {
            __displayErrorMessage(ex.message + "\n\nPlease ensure VintaSoft Web TWAIN service is installed and running.");
            return;
        }

        try {
            // Get available TWAIN devices
            _devices = _deviceManager.get_Devices();
            var defaultDevice = _deviceManager.get_DefaultDevice();

            // Populate the select dropdown
            for (var i = 0; i < _devices.length; i++) {
                var option = document.createElement("option");
                option.text = _devices[i].get_DeviceName();
                option.value = i;
                deviceSelectElement.add(option);

                if (_devices[i].get_DeviceName() === defaultDevice.get_DeviceName()) {
                    deviceSelectElement.selectedIndex = i;
                }
            }

            // Enable "Scan images" button if devices are found
            document.getElementById('scanImagesButton').disabled = _devices.length === 0;
        } catch (ex) {
            __displayErrorMessage(ex);
        }
    }

    // Event listener for device selection
    document.getElementById('devicesSelect').addEventListener('change', function() {
        var deviceIndex = document.getElementById('devicesSelect').selectedIndex;
        if (deviceIndex === -1) return;

        var device = _devices[deviceIndex];
        console.log("Selected device:", device.get_DeviceName());
    });

    // Function to acquire an image from the selected TWAIN scanner
    function acquireImageFromTwainScanner() {
        if (!_deviceManager) {
            alert("TWAIN device manager is not initialized!");
            return;
        }

        if (!_deviceManager.get_IsOpened()) {
            try {
                _deviceManager.open(_deviceManagerInitSettings);
            } catch (ex) {
                alert("Could not re-open TWAIN device manager:\n\n" + ex.message);
                return;
            }
        }

        let device = null;
        let deviceIndex = document.getElementById('devicesSelect').selectedIndex;
		
	    var acquiredImages = new Vintasoft.Twain.WebAcquiredImageCollectionJS(_deviceManager);
        try {
            // Select device or fallback to default
            if (deviceIndex === -1) {
                device = _deviceManager.get_DefaultDevice();
            } else {
                device = _devices[deviceIndex];
            }

            if (!device) {
                alert("No scanner found!");
                return;
            }

            // Open the scanner with UI
            device.open(true, true);
            
            var pixelType = new Vintasoft.Twain.WebPixelTypeEnumJS('RGB');
            console.log(pixelType);
            
            device.set_PixelType(pixelType);

            device.set_XResolution(150);
            device.set_YResolution(150);

            let acquireModalState;
            do {
                let acquireModalResult = device.acquireModalSync();
                acquireModalState = acquireModalResult.get_AcquireModalState().valueOf();

                switch (acquireModalState) {
                    case 2:   // Image acquired
                        var acquiredImage = acquireModalResult.get_AcquiredImage(); // get acquired image

                        // add acquired image to the image collection
                        acquiredImages.add(acquiredImage);

                        var bitmapAsBase64String = acquiredImage.getAsBase64String(); // get image as Base64 string
      
                        var previewImageElement = document.getElementById('previewImage');
                        previewImageElement.style.display = "inline";
                        previewImageElement.src = bitmapAsBase64String;

                        document.getElementById('scannedImage').value = bitmapAsBase64String;
                        break;
                    case 4:   // Image scan failed
                        alert("Scan Error: " + acquireModalResult.get_ErrorMessage());
                        break;
                    case 9:   // Scan finished
                        break;
                }
            } while (acquireModalState !== 0);
        } catch (ex) {
            alert("Scanning Error: " + ex.message + "\n\n" + ex.stack);
        } finally {
		// clear images
		acquiredImages.clear();
			
		if (device) {
			device.close();
		}
		// Close the device manager after scan
		if (_deviceManager) {
			_deviceManager.close();
		}
        }
    }

    // Display error messages
    function __displayErrorMessage(errorMessage) {
        console.log(errorMessage);
        alert(errorMessage);
    }



    // Initialize TWAIN service on page load
    document.addEventListener("DOMContentLoaded", function () {
        __initializeTwainService();
        __openTwainDeviceManagerAndGetDeviceList();
    });
</script>

<script>
    document.getElementById('downloadButton').addEventListener('click', function() {
        var previewImageElement = document.getElementById('previewImage');
        var imageSrc = previewImageElement.src;
    
        if (imageSrc && imageSrc.startsWith("data:image/")) {
            // Create a temporary anchor element
            var downloadLink = document.createElement('a');
            downloadLink.href = imageSrc;
            downloadLink.download = "scanned_image.png"; // Set filename
    
            // Append to body, trigger download, then remove
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        } else {
            alert("No scanned image found!");
        }
    });
</script>

<script>
    document.getElementById('uploadBtn').addEventListener('click', async function () {
        const base64Data = document.getElementById('scannedImage').value;
        const chunkSize = 500000; // Reduce to 500KB
        const totalSize = base64Data.length;
        const totalChunks = Math.ceil(totalSize / chunkSize);

        let start = 0;
        let chunkIndex = 0;

        if (!base64Data || totalSize === 0) {
            toastr.error('Image is required to upload.', 'Error');
            return;
        }

        // Show progress bar
        document.querySelector('.progress').style.display = 'flex';

        const uploadRoute = "{{ $is_manual == 1 ? route('admin.manual-label.upload.scanned.image.chunk') : route('admin.order.certificate.upload.scanned.image.chunk', $order_id) }}";
        
        try {
            while (start < totalSize) {
                const chunk = base64Data.substring(start, start + chunkSize);
                const formData = new FormData();
                formData.append('chunk', chunk);
                formData.append('chunk_index', chunkIndex);
                formData.append('total_chunks', totalChunks);
                formData.append('page_type', '{{$pageType}}');
                formData.append('card_id', '{{$cardId}}');
                formData.append('scroll_index', '{{$scrollToIndex}}');

                const response = await fetch(uploadRoute, {
                    method: "POST",
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();

                if (!response.ok) {
                    throw new Error(result.message || "Something went wrong while uploading.");
                }

                start += chunkSize;
                chunkIndex++;

                // Update progress bar
                let progress = Math.round((chunkIndex / totalChunks) * 100);
                document.getElementById('uploadProgress').style.width = progress + "%";
                document.getElementById('uploadProgress').innerText = progress + "%";
            }

            // Hide progress bar after completion
            document.querySelector('.progress').style.display = 'none';

            // Show success Toastr notification
            toastr.success('Image uploaded successfully', 'Success');

            const redirectRoute = "{{ $is_manual == 1 ? route('admin.manual-label.index') : route('admin.order.certificate.index', $order_id) }}";
            
            // Redirect after success
            setTimeout(() => {
                window.location.href = redirectRoute;
            }, 1000);

        } catch (error) {
            // Hide progress bar if error occurs
            document.querySelector('.progress').style.display = 'none';

            // Show error message
            toastr.error(error.message, 'Error');
        }
    });
</script>
@endpush

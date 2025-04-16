{{-- Modal - Confirm using security code --}}
<div class="modal fade" id="tag-bill-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header px-4">
                <strong>
                    <span class="text-xl font-bold">Tag Bill</span>
                </strong>
            </div>
            <div class="modal-body px-3 pb-1">
                <form class="mb-0" method="post" id="bill-tagging-form"  enctype='multipart/form-data'>
                    @csrf
                    <div class="row align-items-center px-3">
                        <table class="table table-bordered table-hover m-0" id="bill-tagging-table">
                            <thead>
                                <tr>
                                    <th class="text-black text-xs whitespace-nowrap p-1 text-center font-bold">Currency</th>
                                    <th class="text-black text-xs whitespace-nowrap p-1 text-center font-bold">Bill Amount</th>
                                    <th class="text-black text-xs whitespace-nowrap p-1 text-center font-bold">Serial</th>
                                    <th class="text-black text-xs whitespace-nowrap p-1 text-center font-bold">Tags</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td hidden>
                                        <input class="form-control" name="selected-id" id="selected-id" type="hidden">
                                        <input class="form-control" name="currency" id="currency" type="hidden">
                                        <input class="form-control" name="bill-amount" id="bill-amount" type="hidden">
                                        <input class="form-control" name="selling-rate" id="selling-rate" type="hidden">
                                        <input class="form-control" name="serial" id="serial" type="hidden">
                                        <input class="form-control" name="IDs" id="IDs" type="hidden">
                                        <input class="form-control" name="BFID" id="BFID" type="hidden">
                                        <input class="form-control" name="STMDID" id="STMDID" type="hidden">
                                        <input class="form-control" name="source-type" id="source-type" type="hidden">
                                    </td>
                                    <td class="text-sm text-center whitespace-nowrap p-1"><span id="currency-cell"></span>
                                    </td>
                                    <td class="text-sm text-right whitespace-nowrap py-1 pe-3"><span id="bill-amount-cell"></span></td>
                                    <td class="text-sm text-center whitespace-nowrap p-1"><span id="serial-cell"></span></td>
                                    <td class="text-sm text-center whitespace-nowrap p-1 w-50">
                                        <select class="select2 form-select h-100" id="tag-selection" multiple></select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="table table-bordered table-hover mt-3">
                            <thead>
                                <tr>
                                    <th class="text-black text-xs whitespace-nowrap p-1 text-center font-bold" colspan="4">Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-sm text-center whitespace-nowrap p-1" colspan="4">
                                        <textarea class="form-control" id="remarks" name="remarks" rows="2"></textarea>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row justify-content-center mt-4">
                        <div class="col-3">
                            <div class="row">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Basic radio toggle button group">
                                    <input type="radio" class="btn-check" name="radio-image-input" id="type-scan" value="1" checked>
                                    <label class="btn btn-outline-primary" for="type-scan">
                                        <strong>Scan</strong>
                                    </label>

                                    <input type="radio" class="btn-check" name="radio-image-input" id="type-upload" value="2">
                                    <label class="btn btn-outline-primary" for="type-upload">
                                        <strong>Upload</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row p-1">
                        <div class="col-6 py-2" id="id_scan_front_id_field">
                            <div class="col-lg-12 text-center">
                                <div class="form-group" id="r_ScanID">
                                    <div class="col-12">
                                        <span class="text-lg font-bold">Front</span>
                                    </div>
                                    <div id="money-front-face">
                                        <div class="row justify-content-center">
                                            <div class="col-5 border border-3 border-gray-300 rounded-3 my-2 p-2" id='money-front-image'>
                                                <img src="{{ asset('uploads/images/default-img.png') }}" alt="Bill (Front)" id="bill-front-default">
                                            </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-6">
                                                <button type='button' class='btn btn-primary text-white btn-md dropzone btn-sm' id='scanid'>Scan</button>
                                            </div>
                                        </div>

                                        {{-- <div class="row justify-content-center mt-2">
                                            <div class="col-6">
                                                <a href='javascript:clearScans();'>
                                                    <span style='font-weight: normal; text-decoration: none; color: gray; font-weight: 600; hover: color: black;'>Clear Scan</span>
                                                </a>
                                            </div>
                                        </div> --}}

                                        <div id='server_response'></div>
                                        {{-- <input type="text" data-table="tblonboarding" class="form-control" data-field="x_ScanID" name="x_ScanID" id="x_ScanID" size="30"maxlength="255"> --}}
                                    </div>

                                    <div class="mt-2 d-none" id="money-front-face-upload">
                                        <div class="row justify-content-center">
                                            <div class="col-10">
                                                <input type='file' class='form-control money-front-scanned-file' id='money-front-face-field-test' name="money-front-scanned-file" accept="images/jpeg, image/png, image/jpg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-6 py-2" id="id_scan_back_id_field">
                            <div class="col-lg-12 text-center">
                                <div class="form-group" id="scan-back-money">
                                    <div class="col-12">
                                        <span class="text-lg font-bold">Back</span>
                                    </div>
                                    <div id="money-back-face">
                                        <div class="row justify-content-center">
                                            <div class="col-5 border border-3 border-gray-300 rounded-3 my-2 p-2" id='money-back-image'>
                                                <img src="{{ asset('uploads/images/default-img.png') }}" alt="Bill (Back)" id="bill-back-default">
                                            </div>
                                        </div>

                                        <div class="row justify-content-center">
                                            <div class="col-6">
                                                <button type='button' class='btn btn-primary text-white btn-md dropzone btn-sm' id='scan-back-face'>Scan</button>
                                            </div>
                                        </div>

                                        {{-- <div class="row justify-content-center mt-2">
                                            <div class="col-6">
                                                <a href='javascript:clearScans();'>
                                                    <span style='font-weight: normal; text-decoration: none; color: gray; font-weight: 600; hover: color: black;'>Clear Scan</span>
                                                </a>
                                            </div>
                                        </div> --}}

                                        <div id='server_response'></div>
                                        {{-- <input type="text" data-table="tblonboarding" class="form-control" data-field="x_ScanID" name="x_ScanID" id="x_ScanID" size="30"maxlength="255"> --}}
                                    </div>

                                    <div class="mt-2 d-none" id="money-back-face-upload">
                                        <div class="row justify-content-center">
                                            <div class="col-10">
                                                <input type='file' class='form-control money-back-scanned-file' id='money-back-face-field-test' name="money-back-scanned-file" accept="images/jpeg, image/png, image/jpg">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">{{ trans('labels.cancel_action') }}</button>
                <button type="button" class="btn btn-primary btn-sm" id="tag-bills">{{ trans('labels.proceed_action') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $("#tag-selection").select2();

        $('[name="radio-image-input"]').change(function() {
            if ($(this).val() == '1') {
                $('#money-back-face').removeClass('d-none');
                $('#money-front-face').removeClass('d-none');

                $('#money-front-face-upload').addClass('d-none');
                $('#money-back-face-upload').addClass('d-none');
            } else if ($(this).val() == '2') {
                $('#money-back-face').addClass('d-none');
                $('#money-front-face').addClass('d-none');

                $('#money-front-face-upload').removeClass('d-none');
                $('#money-back-face-upload').removeClass('d-none');
            }
        });

        $('#scanid').click(function() {
            initScan();

            function blockSpecialChar(e) {
                var k = e.keyCode;

                return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
            }

            function initScan() {
                scanSimple();
            }

            function scanSimple() {
                scanner.scan(displayImagesOnPage, {
                    "prompt_scan_more": true,
                    "output_settings": [{
                        "type": "return-base64",
                        "format": "jpg"
                    }]
                }, false, false);
            }

            /** Initiates a scan */
            function scanToJpg() {
                scanner.scan(displayImagesOnPage, {
                    "output_settings": [{
                        "type": "return-base64",
                        "format": "jpg",
                    }]
                });
            }

            /** Processes the scan result */
            function displayImagesOnPage(successful, mesg, response) {
                $('#bill-front-default').fadeOut(10);
                $('#money-front-image').empty();

                if (!successful) {
                    console.error('Failed: ' + mesg);
                    return;
                }

                if (successful && mesg != null && mesg.toLowerCase().indexOf('user cancel') >= 0) {
                    console.info('User cancelled');
                    return;
                }

                var scannedImages = scanner.getScannedImages(response, true, false);

                for (var i = 0; (scannedImages instanceof Array) && i < scannedImages.length; i++) {
                    var scannedImage = scannedImages[i];

                    processScannedImage(scannedImage);
                }
            }

            var imagesScanned = [];

            // function processScannedImage(scannedImage) {
            //     imagesScanned.push(scannedImage);

            //     // Create image element
            //     var elementImg = scanner.createDomElementFromModel({
            //         'name': 'img',
            //         'attributes': {
            //             'class': 'scanned',
            //             'src': scannedImage.src, // Set the src attribute directly
            //             'style': 'height: 200px!important;'
            //         }
            //     });

            //     document.getElementById('images').appendChild(elementImg);
            // }

            function processScannedImage(scannedImage) {
                imagesScanned.push(scannedImage);

                // Create an HTMLImageElement from the scanned image source
                var img = new Image();
                img.src = scannedImage.src;

                img.onload = function() {
                    // Create a canvas element to crop and resize the image
                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');

                    // Set the original dimensions
                    var originalWidth = img.width;
                    var originalHeight = img.height;

                    // Set the desired crop dimensions (adjust these to your needs)
                    var cropWidth = 2480;
                    var cropHeight = 3507;

                    // Calculate the cropping coordinates (crop from the top-left corner)
                    var cropX = 0;
                    var cropY = 0;

                    // Set the canvas dimensions to the crop dimensions
                    canvas.width = cropWidth;
                    canvas.height = cropHeight;

                    // Draw the cropped image onto the canvas
                    context.drawImage(img, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight);

                    // Create a new image element with the cropped data
                    var croppedImage = new Image();
                    croppedImage.src = canvas.toDataURL();

                    croppedImage.onload = function() {
                        // Create the DOM element for the cropped image
                        var elementImg = scanner.createDomElementFromModel({
                            'name': 'img',
                            'attributes': {
                                'class': 'scanned',
                                'name': 'money-front-face-field',
                                'id': 'money-front-face-field',
                                'src': croppedImage.src,
                                'style': 'height: 300px!important;' // Set this to the desired display height
                            }
                        });

                        // Create an input field of type 'file'
                        var inputFile = document.createElement('input');
                        inputFile.type = 'file';
                        inputFile.className = 'money-front-scanned-file';
                        inputFile.name = 'money-front-scanned-file';
                        inputFile.id = 'money-front-face-field-test';
                        inputFile.style.display = 'none';

                        document.getElementById('money-front-image').appendChild(elementImg);
                        document.getElementById('money-front-image').appendChild(inputFile);

                        inputFile.files = createFileListFromBlob(dataURLToBlob(croppedImage.src));
                    };
                };
            }

            function createFileListFromBlob(blob) {
                // Create a FileList from a Blob object
                const dataTransfer = new DataTransfer();
                const file = new File([blob], 'scanned-image.jpg', { type: 'image/jpeg' });
                dataTransfer.items.add(file);
                return dataTransfer.files;
            }

            function dataURLToBlob(dataURL) {
                const byteString = atob(dataURL.split(',')[1]);
                const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ab], { type: mimeString });
            }

            /** Upload scanned images by submitting the form */
            function submitFormWithScannedImages() {
                if (scanner.submitFormWithImages('form1', imagesScanned, function(xhr) {
                // if (scanner.submitFormWithImages(function(xhr) {
                    if (xhr.readyState == 4) { // 4: request finished and response is ready
                        document.getElementById('server_response').innerHTML = xhr.responseText;
                        document.getElementById('money-front-image').innerHTML = ''; // clear images

                        var filename = document.getElementById("file_front_id").innerHTML;

                        console.log(filename);

                        document.getElementById("x_ScanID").value = filename;
                        imagesScanned = [];
                    }
                })) {
                    document.getElementById('server_response').innerHTML = "Submitting, please stand by ...";
                } else {
                    document.getElementById('server_response').innerHTML =
                        "Form submission cancelled. Please scan first.";
                }
            }

            function clearScans() {
                if ((imagesScanned instanceof Array) && imagesScanned.length > 0) {
                    if (window.confirm("Are you sure that you want to clear scanned images?")) {
                        imagesScanned = [];

                        document.getElementById('money-front-image').innerHTML = '';
                        document.getElementById("scanid").disabled = false;
                    }
                }
            }
        });

        $('#scan-back-face').click(function() {
            initScan();

            function blockSpecialChar(e) {
                var k = e.keyCode;

                return ((k > 64 && k < 91) || (k > 96 && k < 123) || k == 8 || k == 32 || (k >= 48 && k <= 57));
            }

            function initScan() {
                scanSimple();
            }

            function scanSimple() {
                scanner.scan(displayImagesOnPage, {
                    "prompt_scan_more": true,
                    "output_settings": [{
                        "type": "return-base64",
                        "format": "jpg"
                    }]
                }, false, false);
            }

            /** Initiates a scan */
            function scanToJpg() {
                scanner.scan(displayImagesOnPage, {
                    "output_settings": [{
                        "type": "return-base64",
                        "format": "jpg",
                    }]
                });
            }

            /** Processes the scan result */
            function displayImagesOnPage(successful, mesg, response) {
                $('#bill-back-default').fadeOut(10);
                $('#money-back-image').empty();

                if (!successful) {
                    console.error('Failed: ' + mesg);
                    return;
                }

                if (successful && mesg != null && mesg.toLowerCase().indexOf('user cancel') >= 0) {
                    console.info('User cancelled');
                    return;
                }

                var scannedImages = scanner.getScannedImages(response, true, false);

                for (var i = 0; (scannedImages instanceof Array) && i < scannedImages.length; i++) {
                    var scannedImage = scannedImages[i];

                    processScannedImage(scannedImage);
                }
            }

            var imagesScanned = [];

            // function processScannedImage(scannedImage) {
            //     imagesScanned.push(scannedImage);

            //     // Create image element
            //     var elementImg = scanner.createDomElementFromModel({
            //         'name': 'img',
            //         'attributes': {
            //             'class': 'scanned',
            //             'src': scannedImage.src, // Set the src attribute directly
            //             'style': 'height: 200px!important;'
            //         }
            //     });

            //     document.getElementById('images').appendChild(elementImg);
            // }

            function processScannedImage(scannedImage) {
                imagesScanned.push(scannedImage);

                // Create an HTMLImageElement from the scanned image source
                var img = new Image();
                img.src = scannedImage.src;

                img.onload = function() {
                    // Create a canvas element to crop and resize the image
                    var canvas = document.createElement('canvas');
                    var context = canvas.getContext('2d');

                    // Set the original dimensions
                    var originalWidth = img.width;
                    var originalHeight = img.height;

                    // Set the desired crop dimensions (adjust these to your needs)
                    var cropWidth = 2480;
                    var cropHeight = 3507;

                    // Calculate the cropping coordinates (crop from the top-left corner)
                    var cropX = 0;
                    var cropY = 0;

                    // Set the canvas dimensions to the crop dimensions
                    canvas.width = cropWidth;
                    canvas.height = cropHeight;

                    // Draw the cropped image onto the canvas
                    context.drawImage(img, cropX, cropY, cropWidth, cropHeight, 0, 0, cropWidth, cropHeight);

                    // Create a new image element with the cropped data
                    var croppedImage = new Image();
                    croppedImage.src = canvas.toDataURL();

                    croppedImage.onload = function() {
                        // Create the DOM element for the cropped image
                        var elementImg = scanner.createDomElementFromModel({
                            'name': 'img',
                            'attributes': {
                                'class': 'scanned',
                                'name': 'money-front-face-field',
                                'id': 'money-front-face-field',
                                'src': croppedImage.src,
                                'style': 'height: 300px!important;' // Set this to the desired display height
                            }
                        });

                        // Create an input field of type 'file'
                        var inputFile = document.createElement('input');
                        inputFile.type = 'file';
                        inputFile.className = 'money-back-scanned-file';
                        inputFile.name = 'money-back-scanned-file';
                        inputFile.id = 'money-back-face-field-test';
                        inputFile.style.display = 'none';

                        document.getElementById('money-back-image').appendChild(elementImg);
                        document.getElementById('money-back-image').appendChild(inputFile);

                        inputFile.files = createFileListFromBlob(dataURLToBlob(croppedImage.src));
                    };
                };
            }

            function createFileListFromBlob(blob) {
                // Create a FileList from a Blob object
                const dataTransfer = new DataTransfer();
                const file = new File([blob], 'scanned-image.jpg', { type: 'image/jpeg' });
                dataTransfer.items.add(file);
                return dataTransfer.files;
            }

            function dataURLToBlob(dataURL) {
                const byteString = atob(dataURL.split(',')[1]);
                const mimeString = dataURL.split(',')[0].split(':')[1].split(';')[0];
                const ab = new ArrayBuffer(byteString.length);
                const ia = new Uint8Array(ab);
                for (let i = 0; i < byteString.length; i++) {
                    ia[i] = byteString.charCodeAt(i);
                }
                return new Blob([ab], { type: mimeString });
            }

            /** Upload scanned images by submitting the form */
            function submitFormWithScannedImages() {
                if (scanner.submitFormWithImages('form1', imagesScanned, function(xhr) {
                // if (scanner.submitFormWithImages(function(xhr) {
                    if (xhr.readyState == 4) { // 4: request finished and response is ready
                        document.getElementById('server_response').innerHTML = xhr.responseText;
                        document.getElementById('money-back-image').innerHTML = ''; // clear images

                        var filename = document.getElementById("file_front_id").innerHTML;

                        console.log(filename);

                        document.getElementById("x_ScanID").value = filename;
                        imagesScanned = [];
                    }
                })) {
                    document.getElementById('server_response').innerHTML = "Submitting, please stand by ...";
                } else {
                    document.getElementById('server_response').innerHTML =
                        "Form submission cancelled. Please scan first.";
                }
            }

            function clearScans() {
                if ((imagesScanned instanceof Array) && imagesScanned.length > 0) {
                    if (window.confirm("Are you sure that you want to clear scanned images?")) {
                        imagesScanned = [];

                        document.getElementById('money-back-image').innerHTML = '';
                        document.getElementById("scanid").disabled = false;
                    }
                }
            }
        });
    });

    $("#x_ScanID").css("display", "none");
</script>

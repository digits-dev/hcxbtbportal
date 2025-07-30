<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Uploader</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="min-h-screen bg-gray-50 py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8">
                 {{-- Rejected - reupload --}}
                @if($order->status != 4)
                    <!-- Already Uploaded State -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Proof Already Uploaded</h3>
                        <p class="text-gray-600">You have already uploaded your payment proof for this order.</p>
                    </div>
                @else
                <div class="text-center mb-8">
               <h1 class="text-2xl font-bold text-gray-900 mb-2">
                        {{ $order->status != 4 ? 'Upload' : 'Reupload' }} Proof of Payment
                    </h1>
                    <p class="text-gray-600">Order #{{ $order->reference_number }} - Please upload your payment receipt or proof of downpayment</p>
                </div>
                    <!-- Success State -->
                    <div id="success-state" class="text-center" style="display: none;">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Upload Successful!</h3>
                        <p class="text-gray-600 mb-6">Your payment proof images have been submitted successfully.</p>
                        <button onclick="location.reload()" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Done
                        </button>
                    </div>

                    <!-- Upload State -->
                    <div id="upload-state">
                        <form id="upload-form-element" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="encrypted_id" value="{{ $encryptedId }}">
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-gray-400 transition-colors" id="drop-zone">
                                <input type="file" id="file-upload" name="payment_proof[]" accept="image/*" multiple class="hidden" />
                                <label for="file-upload" class="cursor-pointer flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-lg font-medium text-gray-700 mb-2">Click to upload or drag and drop</span>
                                    <span class="text-sm text-gray-500">PNG, JPG, JPEG images up to 10MB each</span>
                                    <span class="text-xs text-gray-400 mt-1">You can select multiple images at once</span>
                                </label>
                            </div>

                            <!-- Selected Files Preview -->
                            <div id="files-preview" class="mt-6" style="display: none;">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <h4 class="font-medium text-gray-900">Selected Images (<span id="file-count">0</span>)</h4>
                                        <button type="button" id="clear-all-btn" class="text-red-600 hover:text-red-800 transition-colors text-sm">
                                            Clear All
                                        </button>
                                    </div>
                                    
                                    <!-- Files Grid -->
                                    <div id="files-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                                        <!-- File items will be inserted here -->
                                    </div>
                                    
                                    <button type="button" id="upload-btn" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                                        <span id="upload-text">Upload Payment Proof Images</span>
                                        <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Error State -->
                        <div id="error-message" class="mt-4 bg-red-50 border border-red-200 rounded-md p-4" style="display: none;">
                            <div class="flex">
                                <svg class="w-5 h-5 text-red-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <p id="error-text" class="text-sm text-red-700">Upload failed. Please try again.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-8 pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-600">
                        <h4 class="font-medium mb-2">Upload Guidelines:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Only image files: PNG, JPG, JPEG</li>
                            <li>Maximum file size: 10MB per image</li>
                            <li>You can upload multiple images at once</li>
                            <li>Make sure images are clear and readable</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let selectedFiles = [];
            let uploadStatus = 'idle';
            let previewUrls = [];

            // File selection handler
            $('#file-upload').on('change', function(event) {
                const files = Array.from(event.target.files);
                if (files.length > 0) {
                    handleFilesSelect(files);
                }
            });

            // Drag and drop handlers
            $('#drop-zone').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-blue-400 bg-blue-50');
            });

            $('#drop-zone').on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-400 bg-blue-50');
            });

            $('#drop-zone').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-400 bg-blue-50');
                const files = Array.from(e.originalEvent.dataTransfer.files);
                if (files.length > 0) {
                    // Filter only image files
                    const imageFiles = files.filter(file => file.type.startsWith('image/'));
                    if (imageFiles.length > 0) {
                        handleFilesSelect(imageFiles);
                    } else {
                        showError('Please select only image files (PNG, JPG, JPEG)');
                    }
                }
            });

            // Upload button handler
            $('#upload-btn').on('click', function() {
                if (selectedFiles.length > 0 && uploadStatus !== 'uploading') {
                    handleUpload();
                }
            });

            // Clear all files button handler
            $('#clear-all-btn').on('click', function() {
                handleReset();
            });

            function handleFilesSelect(files) {
                let validFiles = [];
                let errors = [];

                files.forEach(file => {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        errors.push(`${file.name} is not an image file`);
                        return;
                    }

                    // Validate file size (10MB limit)
                    if (file.size > 10 * 1024 * 1024) {
                        errors.push(`${file.name} is larger than 10MB`);
                        return;
                    }

                    validFiles.push(file);
                });

                if (errors.length > 0) {
                    showError(errors.join(', '));
                }

                if (validFiles.length > 0) {
                    selectedFiles = validFiles;
                    displayFilePreviews();
                    $('#files-preview').show();
                    $('#error-message').hide();
                }
            }

            function displayFilePreviews() {
                // Clear existing previews
                previewUrls.forEach(url => URL.revokeObjectURL(url));
                previewUrls = [];
                $('#files-grid').empty();

                // Update file count
                $('#file-count').text(selectedFiles.length);

                selectedFiles.forEach((file, index) => {
                    const previewUrl = URL.createObjectURL(file);
                    previewUrls.push(previewUrl);

                    const fileItem = $(`
                        <div class="bg-white border border-gray-200 rounded-lg p-3 relative">
                            <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors remove-file-btn" data-index="${index}">
                                ×
                            </button>
                            <div class="aspect-square mb-2 overflow-hidden rounded-md">
                                <img src="${previewUrl}" alt="Preview" class="w-full h-full object-cover" />
                            </div>
                            <div class="text-xs text-gray-600 truncate" title="${file.name}">
                                ${file.name}
                            </div>
                            <div class="text-xs text-gray-400">
                                ${(file.size / 1024 / 1024).toFixed(2)} MB
                            </div>
                        </div>
                    `);

                    $('#files-grid').append(fileItem);
                });

                // Bind remove file handlers
                $('.remove-file-btn').on('click', function() {
                    const index = parseInt($(this).data('index'));
                    removeFile(index);
                });
            }

            function removeFile(index) {
                // Revoke the preview URL
                if (previewUrls[index]) {
                    URL.revokeObjectURL(previewUrls[index]);
                }

                // Remove from arrays
                selectedFiles.splice(index, 1);
                previewUrls.splice(index, 1);

                if (selectedFiles.length === 0) {
                    $('#files-preview').hide();
                    $('#file-upload').val('');
                } else {
                    displayFilePreviews();
                }
            }

            function handleUpload() {
                if (selectedFiles.length === 0) return;
                
                uploadStatus = 'uploading';
                
                // Update UI for uploading state
                $('#upload-btn').prop('disabled', true);
                $('#loading-spinner').show();
                $('#upload-text').text(`Uploading ${selectedFiles.length} image(s)...`);

                // Create FormData for file upload
                const formData = new FormData();
                selectedFiles.forEach((file, index) => {
                    formData.append('payment_proof[]', file);
                });
                formData.append('encrypted_id', '{{ $encryptedId }}');
                formData.append('_token', $('input[name="_token"]').val());
                
                // Actual AJAX upload
                $.ajax({
                    url: '{{ route("payment.upload.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        uploadStatus = 'success';
                        showSuccessState();
                    },
                    error: function(xhr, status, error) {
                        uploadStatus = 'error';
                        let errorMessage = 'Upload failed. Please try again.';
                        
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage = errors.join(', ');
                        }
                        
                        showError(errorMessage);
                    }
                });
            }

            function showSuccessState() {
                $('#upload-state').hide();
                $('#success-state').show();
            }

            function showError(message) {
                uploadStatus = 'error';
                
                // Reset upload button
                $('#upload-btn').prop('disabled', false);
                $('#loading-spinner').hide();
                $('#upload-text').text('Upload Payment Proof Images');
                
                // Show error message
                $('#error-text').text(message);
                $('#error-message').show();
            }

            function handleReset() {
                selectedFiles = [];
                uploadStatus = 'idle';
                
                // Revoke all preview URLs
                previewUrls.forEach(url => URL.revokeObjectURL(url));
                previewUrls = [];
                
                // Reset form
                $('#file-upload').val('');
                $('#files-preview').hide();
                $('#files-grid').empty();
                $('#error-message').hide();
                
                // Reset upload button
                $('#upload-btn').prop('disabled', false);
                $('#loading-spinner').hide();
                $('#upload-text').text('Upload Payment Proof Images');
            }
        });
    </script>
</body>
</html>
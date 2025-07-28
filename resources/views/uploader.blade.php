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
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8">
         
                @if($order->payment_proof)
                    <!-- Already Uploaded State -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Payment Proof Already Uploaded</h3>
                        <p class="text-gray-600">You have already uploaded your payment proof for this order.</p>
                    </div>
                @else

                    <div class="text-center mb-8">
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Upload Proof of Payment</h1>
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
                        <p class="text-gray-600 mb-6">Your proof of payment has been submitted successfully.</p>
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
                                <input type="file" id="file-upload" name="payment_proof" accept="image/*,.pdf,.doc,.docx" class="hidden" />
                                <label for="file-upload" class="cursor-pointer flex flex-col items-center">
                                    <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    <span class="text-lg font-medium text-gray-700 mb-2">Click to upload or drag and drop</span>
                                    <span class="text-sm text-gray-500">PNG, JPG, PDF, DOC up to 10MB</span>
                                </label>
                            </div>

                            <!-- File Preview Section -->
                            <div id="file-preview" class="mt-6" style="display: none;">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                            <div>
                                                <p id="file-name" class="font-medium text-gray-900"></p>
                                                <p id="file-size" class="text-sm text-gray-500"></p>
                                            </div>
                                        </div>
                                        <button type="button" id="remove-file-btn" class="text-red-600 hover:text-red-800 transition-colors">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Image Preview -->
                                    <div id="image-preview" class="mb-4" style="display: none;">
                                        <img id="preview-image" src="/placeholder.svg" alt="Preview" class="max-w-full h-48 object-contain rounded-md border" />
                                    </div>
                                    
                                    <button type="button" id="upload-btn" class="w-full bg-blue-600 text-white py-3 px-4 rounded-md hover:bg-blue-700 disabled:bg-blue-400 disabled:cursor-not-allowed transition-colors flex items-center justify-center">
                                        <span id="upload-text">Upload Proof of Payment</span>
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
                        <h4 class="font-medium mb-2">Accepted file types:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Images: PNG, JPG, JPEG</li>
                            <li>Documents: PDF, DOC, DOCX</li>
                            <li>Maximum file size: 10MB</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Only run JavaScript if payment proof doesn't exist
            @if(!$order->payment_proof)
            let selectedFile = null;
            let uploadStatus = 'idle';
            let previewUrl = null;

            // File selection handler
            $('#file-upload').on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    handleFileSelect(file);
                }
            });

            // Drag and drop handlers
            $('#drop-zone').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('border-blue-400');
            });

            $('#drop-zone').on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-400');
            });

            $('#drop-zone').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('border-blue-400');
                const file = e.originalEvent.dataTransfer.files[0];
                if (file) {
                    $('#file-upload')[0].files = e.originalEvent.dataTransfer.files;
                    handleFileSelect(file);
                }
            });

            // Upload button handler
            $('#upload-btn').on('click', function() {
                if (selectedFile && uploadStatus !== 'uploading') {
                    handleUpload();
                }
            });

            // Remove file button handler
            $('#remove-file-btn').on('click', function() {
                handleReset();
            });

            function handleFileSelect(file) {
                selectedFile = file;
                
                // Validate file size (10MB limit)
                if (file.size > 10 * 1024 * 1024) {
                    showError('File size must be less than 10MB');
                    return;
                }
                
                // Display file info
                $('#file-name').text(file.name);
                $('#file-size').text((file.size / 1024 / 1024).toFixed(2) + ' MB');
                
                // Show file preview section
                $('#file-preview').show();
                
                // Handle image preview
                if (file.type.startsWith('image/')) {
                    if (previewUrl) {
                        URL.revokeObjectURL(previewUrl);
                    }
                    previewUrl = URL.createObjectURL(file);
                    $('#preview-image').attr('src', previewUrl);
                    $('#image-preview').show();
                } else {
                    $('#image-preview').hide();
                    previewUrl = null;
                }
                
                // Hide error message if visible
                $('#error-message').hide();
            }

            function handleUpload() {
                if (!selectedFile) return;
                
                uploadStatus = 'uploading';
                
                // Update UI for uploading state
                $('#upload-btn').prop('disabled', true);
                $('#loading-spinner').show();
                $('#upload-text').text('Uploading...');
                
                // Create FormData for file upload
                const formData = new FormData();
                formData.append('payment_proof', selectedFile);
                formData.append('encrypted_id', '{{ $encryptedId }}');
                formData.append('_token', $('input[name="_token"]').val());
                
                // Actual AJAX upload
                $.ajax({
                    url: '{{ route("payment.upload.store") }}', // You'll need to create this route
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
                $('#upload-text').text('Upload Proof of Payment');
                
                // Show error message
                $('#error-text').text(message);
                $('#error-message').show();
            }

            function handleReset() {
                selectedFile = null;
                uploadStatus = 'idle';
                
                if (previewUrl) {
                    URL.revokeObjectURL(previewUrl);
                    previewUrl = null;
                }
                
                // Reset form
                $('#file-upload').val('');
                $('#file-preview').hide();
                $('#image-preview').hide();
                $('#error-message').hide();
                
                // Reset upload button
                $('#upload-btn').prop('disabled', false);
                $('#loading-spinner').hide();
                $('#upload-text').text('Upload Proof of Payment');
            }
            @endif
        });
    </script>
</body>
</html>
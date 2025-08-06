<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Uploader</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fade-in { animation: fadeIn 0.5s ease-out; }
        .animate-slide-in { animation: slideIn 0.3s ease-out; }
        .animate-pulse-custom { animation: pulse 2s infinite; }
        
        /* Dark background matching the login image */
        .dark-bg {
            background-color: #374151; /* Dark slate gray */
        }
        
        .header-gradient {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }
        
        .file-preview-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }
        .drop-zone-active {
            background: linear-gradient(135deg, #e0f2fe 0%, #f3e5f5 100%);
            border-color: #3b82f6;
            transform: scale(1.02);
        }
    </style>
</head>
<body class="min-h-screen">
    <div class="min-h-screen py-8 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden animate-fade-in">
                
                {{-- Header Section --}}
                 @if(in_array($order->status, [1,4]))
                <div class="header-gradient p-8 text-white text-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative z-10">
                        <div class="w-16 h-16 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse-custom">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold mb-2">
                            {{ $order->status != 4 ? 'Upload' : 'Reupload' }} Proof of Payment
                        </h1>
                        <p class="text-gray-200 text-lg">Order #{{ $order->reference_number }} - Please upload your payment receipt or proof of downpayment</p>
                    </div>
                </div>
                @endif
                <div class="p-8">
                    {{-- Already Uploaded State --}}
                    @if(!in_array($order->status, [1,4]))
                        <div class="text-center animate-fade-in">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse-custom">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Payment Proof Already Uploaded for Order #{{ $order->reference_number }}</h3>
                            <p class="text-gray-600 text-lg">You have already uploaded your payment proof for this order.</p>
                        </div>
                    @else
                        
                        {{-- Success State --}}
                        <div id="success-state" class="text-center animate-fade-in" style="display: none;">
                            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse-custom">
                                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-4">Upload Successful!</h3>
                            <p class="text-gray-600 text-lg mb-8">Your payment proof images have been submitted successfully.</p>
                            <button onclick="location.reload()" class="bg-gray-800 hover:bg-gray-900 text-white px-8 py-3 rounded-xl transition-all duration-300 transform hover:scale-105 shadow-lg">
                                Done
                            </button>
                        </div>

                        {{-- Upload State --}}
                        <div id="upload-state" class="animate-fade-in">
                            <form id="upload-form-element" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="encrypted_id" value="{{ $encryptedId }}">
                                
                                {{-- Drop Zone --}}
                                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-12 text-center hover:border-gray-400 transition-all duration-300 transform hover:scale-[1.02]" id="drop-zone">
                                    <input type="file" id="file-upload" name="payment_proof[]" accept="image/*" multiple class="hidden" />
                                    <label for="file-upload" class="cursor-pointer flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-6 group-hover:bg-gray-200 transition-colors">
                                            <svg class="w-8 h-8 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                            </svg>
                                        </div>
                                        <span class="text-xl font-semibold text-gray-700 mb-3">Click to upload or drag and drop</span>
                                        <span class="text-gray-500 mb-2">PNG, JPG, JPEG images up to 10MB each</span>
                                        <span class="text-sm text-gray-400">You can select multiple images at once</span>
                                    </label>
                                </div>

                                {{-- Selected Files Preview --}}
                                <div id="files-preview" class="mt-8 animate-slide-in" style="display: none;">
                                    <div class="bg-gray-50 rounded-2xl p-6 border border-gray-200">
                                        <div class="flex items-center justify-between mb-6">
                                            <h4 class="text-xl font-bold text-gray-900 flex items-center">
                                                <svg class="w-5 h-5 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z" />
                                                </svg>
                                                Selected Images (<span id="file-count">0</span>)
                                            </h4>
                                            <button type="button" id="clear-all-btn" class="text-red-600 hover:text-red-800 transition-colors text-sm font-medium bg-red-50 px-4 py-2 rounded-lg hover:bg-red-100">
                                                Clear All
                                            </button>
                                        </div>

                                        {{-- Files Grid --}}
                                        <div id="files-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-6">
                                            {{-- File items will be inserted here --}}
                                        </div>

                                        <button type="button" id="upload-btn" class="w-full bg-gray-800 hover:bg-gray-900 text-white py-4 px-6 rounded-xl disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-300 transform hover:scale-[1.02] shadow-lg flex items-center justify-center text-lg font-semibold">
                                            <span id="upload-text">Upload Payment Proof Images</span>
                                            <svg id="loading-spinner" class="animate-spin -ml-1 mr-3 h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" style="display: none;">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </form>

                            {{-- Error State --}}
                            <div id="error-message" class="mt-6 bg-red-50 border-l-4 border-red-400 rounded-lg p-4 animate-slide-in" style="display: none;">
                                <div class="flex">
                                    <svg class="w-6 h-6 text-red-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p id="error-text" class="text-red-700 font-medium">Upload failed. Please try again.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Upload Guidelines --}}
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <div class="flex items-start space-x-4">
                                <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 mb-3 text-lg">Upload Guidelines:</h4>
                                    <ul class="space-y-2 text-gray-700">
                                        <li class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                                            <span>Only image files: PNG, JPG, JPEG</span>
                                        </li>
                                        <li class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                                            <span>Maximum file size: 10MB per image</span>
                                        </li>
                                        <li class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                                            <span>You can upload multiple images at once</span>
                                        </li>
                                        <li class="flex items-center space-x-3">
                                            <div class="w-2 h-2 bg-gray-600 rounded-full"></div>
                                            <span>Make sure images are clear and readable</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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

            // Enhanced drag and drop handlers
            $('#drop-zone').on('dragover', function(e) {
                e.preventDefault();
                $(this).addClass('drop-zone-active');
            });

            $('#drop-zone').on('dragleave', function(e) {
                e.preventDefault();
                $(this).removeClass('drop-zone-active');
            });

            $('#drop-zone').on('drop', function(e) {
                e.preventDefault();
                $(this).removeClass('drop-zone-active');
                const files = Array.from(e.originalEvent.dataTransfer.files);
                if (files.length > 0) {
                    const imageFiles = files.filter(file => file.type.startsWith('image/'));
                    if (imageFiles.length > 0) {
                        handleFilesSelect(imageFiles);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid Files',
                            text: 'Please select only image files (PNG, JPG, JPEG)',
                            confirmButtonColor: '#374151'
                        });
                    }
                }
            });

            // Upload button handler with SweetAlert confirmation
            $('#upload-btn').on('click', function() {
                if (selectedFiles.length > 0 && uploadStatus !== 'uploading') {
                    Swal.fire({
                        title: 'Upload Payment Proof?',
                        text: `You are about to upload ${selectedFiles.length} image(s). This action cannot be undone.`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#374151',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, upload now!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            handleUpload();
                        }
                    });
                }
            });

            // Clear all files button handler with SweetAlert confirmation
            $('#clear-all-btn').on('click', function() {
                Swal.fire({
                    title: 'Clear all files?',
                    text: 'This will remove all selected images.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, clear all!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        handleReset();
                        Swal.fire({
                            title: 'Cleared!',
                            text: 'All files have been removed.',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                });
            });

            function handleFilesSelect(files) {
                let validFiles = [];
                let errors = [];

                files.forEach(file => {
                    if (!file.type.startsWith('image/')) {
                        errors.push(`${file.name} is not an image file`);
                        return;
                    }
                    if (file.size > 10 * 1024 * 1024) {
                        errors.push(`${file.name} is larger than 10MB`);
                        return;
                    }
                    validFiles.push(file);
                });

                if (errors.length > 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Files',
                        html: errors.join('<br>'),
                        confirmButtonColor: '#374151'
                    });
                }

                if (validFiles.length > 0) {
                    selectedFiles = validFiles;
                    displayFilePreviews();
                    $('#files-preview').show();
                    $('#error-message').hide();
                }
            }

            function displayFilePreviews() {
                previewUrls.forEach(url => URL.revokeObjectURL(url));
                previewUrls = [];
                $('#files-grid').empty();
                $('#file-count').text(selectedFiles.length);

                selectedFiles.forEach((file, index) => {
                    const previewUrl = URL.createObjectURL(file);
                    previewUrls.push(previewUrl);

                    const fileItem = $(`
                        <div class="bg-white border border-gray-200 rounded-xl p-4 relative file-preview-hover transition-all duration-300 animate-slide-in">
                            <button type="button" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm hover:bg-red-600 transition-colors shadow-lg remove-file-btn" data-index="${index}">
                                ×
                            </button>
                            <div class="aspect-square mb-3 overflow-hidden rounded-lg bg-gray-100">
                                <img src="${previewUrl}" alt="Preview" class="w-full h-full object-cover" />
                            </div>
                            <div class="text-sm text-gray-700 font-medium truncate mb-1" title="${file.name}">
                                ${file.name}
                            </div>
                            <div class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded-full inline-block">
                                ${(file.size / 1024 / 1024).toFixed(2)} MB
                            </div>
                        </div>
                    `);

                    $('#files-grid').append(fileItem);
                });

                $('.remove-file-btn').on('click', function() {
                    const index = parseInt($(this).data('index'));
                    removeFile(index);
                });
            }

            function removeFile(index) {
                if (previewUrls[index]) {
                    URL.revokeObjectURL(previewUrls[index]);
                }
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
                $('#upload-btn').prop('disabled', true);
                $('#loading-spinner').show();
                $('#upload-text').text(`Uploading ${selectedFiles.length} image(s)...`);

                const formData = new FormData();
                selectedFiles.forEach((file, index) => {
                    formData.append('payment_proof[]', file);
                });
                formData.append('encrypted_id', '{{ $encryptedId }}');
                formData.append('_token', $('input[name="_token"]').val());

                $.ajax({
                    url: '{{ route("payment.upload.store") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        uploadStatus = 'success';
                        Swal.fire({
                            icon: 'success',
                            title: 'Upload Successful!',
                            text: 'Your payment proof has been submitted successfully.',
                            confirmButtonColor: '#10b981',
                            timer: 2000
                        }).then(() => {
                            showSuccessState();
                        });
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

                        Swal.fire({
                            icon: 'error',
                            title: 'Upload Failed',
                            text: errorMessage,
                            confirmButtonColor: '#ef4444'
                        });

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
                $('#upload-btn').prop('disabled', false);
                $('#loading-spinner').hide();
                $('#upload-text').text('Upload Payment Proof Images');
                $('#error-text').text(message);
                $('#error-message').show();
            }

            function handleReset() {
                selectedFiles = [];
                uploadStatus = 'idle';
                previewUrls.forEach(url => URL.revokeObjectURL(url));
                previewUrls = [];
                $('#file-upload').val('');
                $('#files-preview').hide();
                $('#files-grid').empty();
                $('#error-message').hide();
                $('#upload-btn').prop('disabled', false);
                $('#loading-spinner').hide();
                $('#upload-text').text('Upload Payment Proof Images');
            }
        });
    </script>
</body>
</html>
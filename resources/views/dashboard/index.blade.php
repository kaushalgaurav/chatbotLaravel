@extends('layouts.master')

@section('title')
    @lang('translation.Dashboards')
@endsection

@section('content')
    <style>
        body {
            background-color: #fff !important;
        }
    </style>
    <!-- small inline style for previews -->
    <style>
        .msme-preview-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            padding: .4rem;
            border: 1px solid #ececec;
            margin-bottom: .5rem;
            border-radius: 6px;
        }

        .msme-preview-thumb {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            background: #f7f7f7;
            border-radius: 4px;
            overflow: hidden;
        }

        .msme-preview-meta {
            flex: 1;
            overflow: hidden;
        }

        .msme-remove-btn {
            cursor: pointer;
            color: #dc3545;
        }
    </style>
    <div class="chatbot-builder">

        <div class="d-flex align-items-center justify-content-between mb-4 mt-4">
            <div class="welcome-text">
                <h4 class="mb-0 fs-22">Welcome to Chatbot Builder!</h4>
                <p class="text-muted m-0 p-0">Your free trial expires in <span class="fw-semibold">14 days</span></p>
            </div>
            <button class="btn btn-primary" style="padding: 0px 50px;">Upgrade <i class="fa fa-arrow-right ms-1"></i></button>
        </div>

        <div class="create-card mb-5">
            <div class="create-card-header">
                <h5 class="mb-3 fs-22">Create a bot for</h5>
            </div>
            <div class="create-card-body d-flex gap-3">
                <div class="create-card-item" data-bs-toggle="modal" data-bs-target="#addchatbot" data-type="1">
                    <div class="create-card-icon btn-primary">
                        <img src="{{ URL::asset('build/images/icons/responsive.png') }}" alt="">
                    </div>
                    <h6 class="mb-0 fs-16">Web(Rule Based)</h6>
                </div>
                <div class="create-card-item" data-bs-toggle="modal" data-bs-target="#addchatbot" data-type="2">
                    <div class="create-card-icon btn-warning">
                        <img src="{{ URL::asset('build/images/icons/ApiChatbot.png') }}" alt="">
                    </div>
                    <h6 class="mb-0 fs-16">Small MSME</h6>
                </div>
            </div>
        </div>


        <div class="last-updated-chatbots mb-5">
            <h5 class="mb-3 fs-22">Last updated chatbots</h5>
            <!-- end Bot Card -->
            @foreach ($chatbots as $chatbot)
                @php
                    $encryptedId = Crypt::encryptString($chatbot->id);
                @endphp
                <div class="bot-card-container">
                    <div class="bot-info">
                        <input type="checkbox" class="form-check-input" type="checkbox" value="">
                        <div class="bot-icon">🤖</div>
                        <div class="bot-text">
                            <h4>{{ $chatbot->name }}</h4>
                            <small>Created {{ $chatbot->created_at->diffForHumans() }} / Updated {{ $chatbot->updated_at->diffForHumans() }}</small>
                        </div>
                    </div>
                    <div class="bot-info-right">
                        <div class="bot-stats">
                            <p class="text-muted m-0 p-0 fw-400">CHATS<span class="fw-semibold">0</span></p>
                            <p class="text-muted m-0 p-0 fw-400">FINISHED<span class="fw-semibold">0</span></p>
                        </div>
                        <ul class="bot-actions">
                            <li><a href="javascript:void(0);"><i class="mdi mdi-poll"></i></a></li>
                            <li><a href="javascript:void(0);"><i class="mdi mdi-forum"></i></a></li>
                            <li><a href="javascript:void(0);"><i class="mdi mdi-export-variant"></i></a></li>
                            <li class="dropdown">
                                <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown"><i class="mdi mdi-dots-horizontal"></i></a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Duplicate</a></li>
                                    <li><a class="dropdown-item" href="{{ route('chatbots.build', $encryptedId) }}">Open Bot</a></li>
                                    <li><a class="dropdown-item" href="#">Rename</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('chatbots.destroy', $encryptedId) }}" style="display:inline-block;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger dropdown-item" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            @endforeach
            <!-- end Bot Card -->
        </div>


        {{-- last div --}}
    </div>


    <!-- Static add chatbot Modal -->
    <div class="modal fade" id="addchatbot" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl start-building-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <h5 class="modal-title fs-30 text-center mb-5">Start building!</h5>
                <!-- Hidden input to store selected bot type -->
                <input type="hidden" id="botType" value="">
                <div class="row align-items-center justify-content-center">
                    <div class="col-lg-4 col-md-6">
                        <a href="javascript: void(0);">
                            <div class="start-building-card ">
                                <div class="start-building-icon"><img src="{{ URL::asset('build/images/icons/scratch.png') }}" alt="scratch"></div>
                                <h3 class="my-2 fs-20">Build it for me!</h3>
                                <p class="mb-0">Tell what you need and we will create it automatically</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="javascript: void(0);" class="start-from-scratch">
                            <div class="start-building-card ">
                                <div class="start-building-icon"><img src="{{ URL::asset('build/images/icons/scratch.png') }}" alt="scratch"></div>
                                <h3 class="my-2 fs-20">Start from scratch</h3>
                                <p class="mb-0">Start with a blank builder and let your imagination flow!</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="javascript: void(0);" data-bs-toggle="modal" data-bs-target="#templateListModal">
                            <div class="start-building-card ">
                                <div class="start-building-icon"><img src="{{ URL::asset('build/images/icons/template.png') }}" alt="template"></div>
                                <h3 class="my-2 fs-20">Use a template</h3>
                                <p class="mb-0">Choose a pre-made bot and edit them as you want</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal for template --}}
    <div class="modal fade" id="templateListModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="templateListLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fs-20" id="templateListLabel">Select a Template</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="template-list" class="row g-3">
                        <!-- Template cards will be injected here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal for msme upload --}}
    <!-- MSME Upload Modal -->
    <!-- MSME Upload Modal -->
    {{-- <div class="modal fade" id="msmeUploadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="msmeUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="msmeUploadForm" method="POST" action="{{ route('msme.upload-products') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="msmeUploadLabel">Upload Files for <span id="msmeBotName">Bot</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">

                        <!-- Dummy download button -->
                        <div class="mb-3">
                            <a href="{{ url('/msme/download-dummy') }}" class="btn btn-info">
                                <i class="bi bi-download"></i> Download Dummy CSV/Excel
                            </a>
                        </div>

                        <!-- File drop area -->
                        <div id="msmeDropArea" class="p-4 border rounded text-center" style="cursor: pointer;">
                            <p class="mb-1">Drag & drop files here, or click to browse</p>
                            <small class="text-muted">Allowed: csv, xls, xlsx. Max file size: 100MB</small>
                            <input id="msmeFileInput" name="files" type="file" style="display:none;" accept=".csv,.xls,.xlsx" />
                        </div>

                        <!-- Preview area -->
                        <div id="msmePreview" class="mt-3"></div>
                        <div id="msmeError" class="text-danger mt-2" style="display:none;"></div>

                        <!-- Bootstrap progress bar -->
                        <div class="progress mt-3" style="height: 25px; display: none;" id="msmeProgressWrapper">
                            <div id="msmeProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">
                                0%
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="mt-2" id="msmeProgressStats" style="display:none;">
                            Inserted: <span id="msmeInserted">0</span> |
                            Updated: <span id="msmeUpdated">0</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}

    <div class="modal fade" id="msmeUploadModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="msmeUploadLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="msmeUploadForm" method="POST" action="{{ route('msme.upload-products') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="msmeUploadLabel">Upload Files for <span id="msmeBotName">Bot</span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Dummy download button -->
                        <div class="mb-3">
                            <a href="{{ url('/msme/download-dummy') }}" class="btn btn-info" target="_blank">
                                <i class="bi bi-download"></i> Download Dummy CSV/Excel
                            </a>
                        </div>

                        <!-- File drop area -->
                        <div id="msmeDropArea" class="p-4 border rounded text-center" style="cursor: pointer;">
                            <p class="mb-1">Drag & drop files here, or click to browse</p>
                            <small class="text-muted">Allowed: csv, xls, xlsx. Max file size: 100MB</small>
                            <input id="msmeFileInput" name="files" type="file" style="display:none;" accept=".csv,.xls,.xlsx" />
                        </div>

                        <!-- Preview area -->
                        <div id="msmePreview" class="mt-3"></div>
                        <div id="msmeError" class="text-danger mt-2" style="display:none;"></div>

                        <!-- Bootstrap progress bar -->
                        <div class="progress mt-3" style="height: 25px; display: none;" id="msmeProgressWrapper">
                            <div id="msmeProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">
                                0%
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="mt-2" id="msmeProgressStats" style="display:none;">
                            Inserted: <span id="msmeInserted">0</span> |
                            Updated: <span id="msmeUpdated">0</span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('script')
    <!-- dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard.init.js') }}"></script>
    <!-- <script>
        $(document).ready(function() {
            $('.start-from-scratch').on('click', function(e) {
                e.preventDefault();
                let requestData = {
                    name: 'My Custom Bot', // required field
                    description: 'This is a new bot',
                    platform: 'web',
                    language: 'en',
                    is_active: true
                };
                $.ajax({
                    url: '/chatbots/store',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    contentType: 'application/json',
                    dataType: 'json',
                    data: JSON.stringify(requestData), // send extra data if needed
                    success: function(data) {
                        if (data.success) {
                            // redirect to chatbot page
                            window.location.href = '/chatbots/' + data.bot_id + '/build';
                        } else {
                            alert('Error creating chatbot');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                        alert('Something went wrong!');
                    }
                });
            });
        });
    </script> -->
    <script>
        $(document).ready(function() {

            let selectedBotType = null; // 1 = Web, 2 = MSME

            // Detect which card was clicked (Web or MSME)
            $('.create-card-item').on('click', function() {
                selectedBotType = $(this).data('type');
                console.log('Selected bot type:', selectedBotType);
            });

            // Handle 'Start from scratch' click
            $('.start-from-scratch').on('click', function(e) {
                e.preventDefault();

                if (!selectedBotType) {
                    alert('Please select a bot type first!');
                    return;
                }

                // ✅ If Web bot type = 1 → Create bot via AJAX
                if (selectedBotType == 1) {
                    let requestData = {
                        name: 'My Web Bot',
                        description: 'This is a new web bot',
                        platform: selectedBotType,
                        language: 'en',
                        is_active: true
                    };

                    $.ajax({
                        url: '/chatbots/store',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        contentType: 'application/json',
                        dataType: 'json',
                        data: JSON.stringify(requestData),
                        success: function(data) {
                            if (data.success) {
                                window.location.href = '/chatbots/' + data.bot_id + '/build';
                            } else {
                                alert('Error creating chatbot');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error(error);
                            alert('Something went wrong!');
                        }
                    });

                } else {
                    // ✅ MSME bot → Show file upload modal
                    $("#addchatbot").modal('hide'); // Close previous modal first
                    setTimeout(function() {
                        $("#msmeUploadModal").modal('show');
                    }, 400);
                }
            });

        });
    </script>

    {{-- <script>
        $(document).ready(function() {

            let msmeFiles = []; // store selected files

            const allowedTypes = [
                'text/csv',
                'application/vnd.ms-excel', // .xls
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' // .xlsx
            ];
            const maxFileSize = 5 * 1024 * 1024; // 5MB

            // Open file browser on click
            $("#msmeDropArea").on("click", function() {
                $("#msmeFileInput").click();
            });

            // Handle file input selection
            $("#msmeFileInput").on("change", function(e) {
                handleFiles(e.target.files);
            });

            // Drag & Drop events
            $("#msmeDropArea").on("dragover", function(e) {
                e.preventDefault();
                $(this).addClass("bg-light");
            });

            $("#msmeDropArea").on("dragleave", function(e) {
                e.preventDefault();
                $(this).removeClass("bg-light");
            });

            $("#msmeDropArea").on("drop", function(e) {
                e.preventDefault();
                $(this).removeClass("bg-light");
                handleFiles(e.originalEvent.dataTransfer.files);
            });

            // Handle and Validate Files
            function handleFiles(files) {
                $.each(files, function(i, file) {
                    if (!allowedTypes.includes(file.type)) {
                        showError("Invalid file type: " + file.name);
                        return;
                    }
                    if (file.size > maxFileSize) {
                        showError("File size too large: " + file.name);
                        return;
                    }
                    msmeFiles.push(file);
                    previewFiles();
                });
            }

            // Show preview of files
            function previewFiles() {
                $("#msmePreview").html(''); // clear old preview
                $.each(msmeFiles, function(i, file) {
                    $("#msmePreview").append(`
                <div class="d-flex justify-content-between align-items-center border p-2 mb-2">
                    <span>${file.name} (${Math.round(file.size / 1024)} KB)</span>
                    <button class="btn btn-sm btn-danger remove-file" data-index="${i}">Remove</button>
                </div>
            `);
                });
            }

            // Remove file
            $(document).on("click", ".remove-file", function() {
                let index = $(this).data("index");
                msmeFiles.splice(index, 1);
                previewFiles();
            });

            // Show error message
            function showError(message) {
                $("#msmeError").text(message).show();
                setTimeout(() => $("#msmeError").hide(), 3000);
            }

            // Submit Upload Form
            $("#msmeUploadForm").on("submit", function(e) {
                e.preventDefault();

                if (msmeFiles.length === 0) {
                    showError("Please select files before uploading.");
                    return;
                }

                let formData = new FormData();
                $.each(msmeFiles, function(i, file) {
                    formData.append("files[]", file);
                });

                // append bot ID or other data if needed
                formData.append("chatbot_id", $("#msmeChatbotId").val());

                $.ajax({
                    url: "/msme/upload-files", // CHANGE THIS URL
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(res) {
                        alert("Files uploaded successfully!");
                        $("#msmeUploadModal").modal("hide");
                    },
                    error: function(xhr) {
                        showError("Upload failed. Try again.");
                    }
                });
            });

        });
    </script> --}}


    <script>
        $(document).ready(function() {

            // Load templates when modal opens
            $('#templateListModal').on('shown.bs.modal', function() {
                loadTemplates();
            });

            function loadTemplates() {
                let $container = $('#template-list');
                $container.html('<div class="text-center py-4">Loading templates...</div>');

                $.ajax({
                    url: "{{ route('templates.index') }}", // your API route for templates
                    method: "GET",
                    success: function(data) {
                        if (data.length === 0) {
                            $container.html('<div class="text-center text-muted py-4">No templates found.</div>');
                            return;
                        }

                        let html = '';
                        $.each(data, function(index, tpl) {
                            html += `
                        <div class="col-md-4">
                            <div class="card h-100 shadow-sm border-0 template-card">
                                <div class="card-body text-center">
                                    <h5 class="card-title">${tpl.title}</h5>
                                    <p class="card-text text-muted text-truncate">${tpl.content}</p>
                                    <button class="btn btn-primary btn-sm mt-2 use-template-btn" data-id="${tpl.id}">
                                        Use Template
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                        });
                        $container.html(html);
                    },
                    error: function() {
                        $container.html('<div class="text-center text-danger py-4">Failed to load templates.</div>');
                    }
                });
            }

            // Handle Use Template click
            $(document).on('click', '.use-template-btn', function() {
                let templateId = $(this).data('id');
                let $btn = $(this);

                if (!templateId) {
                    alert('No template selected!');
                    return;
                }

                $btn.prop('disabled', true).text('Applying...');

                $.ajax({
                    url: "{{ route('templates.copy', ':id') }}".replace(':id', templateId),
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        chatbot_id: templateId
                    },
                    success: function(response) {
                        alert('Template applied successfully!');
                        $('#templateListModal').modal('hide');
                        location.reload(); // or redirect if needed
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong!';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        alert(msg);
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Use Template');
                    }
                });
            });

        });
    </script>

    {{-- <script>
        $(document).ready(function() {

            const $msmeForm = $('#msmeUploadForm');
            const $fileInput = $('#msmeFileInput');
            const $dropArea = $('#msmeDropArea');
            const $preview = $('#msmePreview');
            const $error = $('#msmeError');

            let uploadUuid = null;
            let pollInterval = null;

            // Click to select file
            $dropArea.on('click', function() {
                $fileInput.click();
            });

            // Drag & Drop
            $dropArea.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $dropArea.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $dropArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                $fileInput[0].files = files;
                renderPreview(files);
            });

            // File input change
            $fileInput.on('change', function() {
                renderPreview(this.files);
            });

            // Render file preview
            function renderPreview(files) {
                $preview.empty();
                $error.hide();
                if (files.length === 0) return;

                Array.from(files).forEach(file => {
                    const item = $('<div>').text(file.name + ' (' + formatBytes(file.size) + ')');
                    $preview.append(item);
                });
            }

            // Format bytes nicely
            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Submit form
            $msmeForm.on('submit', function(e) {
                e.preventDefault();
                if ($fileInput[0].files.length === 0) {
                    alert('Please select a file to upload.');
                    return;
                }

                const formData = new FormData(this);
                // const chatbotId = $('#msmeChatbotId').val();
                // formData.append('chatbot_id', chatbotId);

                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true).text('Uploading...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/msme/upload-products',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        uploadUuid = res.upload_uuid;
                        startPolling();
                    },
                    error: function(xhr) {
                        let msg = 'Upload failed.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        $error.text(msg).show();
                        $submitBtn.prop('disabled', false).text('Upload');
                    }
                });
            });

            // Poll upload progress
            function startPolling() {
                if (!uploadUuid) return;

                pollInterval = setInterval(function() {
                    $.getJSON('/msme/upload-status/' + uploadUuid, function(res) {
                        if (res.success) {
                            const data = res.data;
                            const percent = data.total_rows > 0 ?
                                Math.round((data.processed_rows / data.total_rows) * 100) :
                                0;

                            updateProgressBar(percent, data.inserted, data.updated);

                            if (data.status === 'done' || data.status === 'failed') {
                                clearInterval(pollInterval);
                                $('#msmeUploadModal').modal('hide');
                                alert('Upload completed! Inserted: ' + data.inserted + ', Updated: ' + data.updated);
                                location.reload(); // optional: refresh product list
                            }
                        }
                    });
                }, 2000);
            }

            // Update Bootstrap progress bar and stats
            function updateProgressBar(percent, inserted, updated) {
                const $wrapper = $('#msmeProgressWrapper');
                const $bar = $('#msmeProgressBar');
                const $stats = $('#msmeProgressStats');

                $wrapper.show();
                $stats.show();

                $bar.css('width', percent + '%');
                $bar.text(percent + '%');

                $('#msmeInserted').text(inserted);
                $('#msmeUpdated').text(updated);
            }
        });
    </script> --}}

    {{-- <script>
        $(document).ready(function() {

            const $msmeForm = $('#msmeUploadForm');
            const $fileInput = $('#msmeFileInput');
            const $dropArea = $('#msmeDropArea');
            const $preview = $('#msmePreview');
            const $error = $('#msmeError');

            let uploadUuid = null;
            let pollInterval = null;

            // Click to select file
            $dropArea.on('click', function() {
                $fileInput.click();
            });

            // Drag & Drop
            $dropArea.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $dropArea.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $dropArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                $fileInput[0].files = files;
                renderPreview(files);
            });

            // File input change
            $fileInput.on('change', function() {
                renderPreview(this.files);
            });

            // Render file preview
            function renderPreview(files) {
                $preview.empty();
                $error.hide();
                if (files.length === 0) return;

                Array.from(files).forEach(file => {
                    const item = $('<div>').text(file.name + ' (' + formatBytes(file.size) + ')');
                    $preview.append(item);
                });
            }

            // Format bytes nicely
            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Submit form
            $msmeForm.on('submit', function(e) {
                e.preventDefault();
                if ($fileInput[0].files.length === 0) {
                    alert('Please select a file to upload.');
                    return;
                }

                const formData = new FormData(this);

                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true).text('Uploading...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '/msme/upload-products',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        uploadUuid = res.upload_uuid;
                        startPolling();
                    },
                    error: function(xhr) {
                        let msg = 'Upload failed.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        $error.text(msg).show();
                        $submitBtn.prop('disabled', false).text('Upload');
                    }
                });
            });

            // Poll upload progress
            function startPolling() {
                if (!uploadUuid) return;

                pollInterval = setInterval(function() {
                    $.getJSON('/msme/upload-status/' + uploadUuid, function(res) {
                        if (res.success) {
                            const data = res.data;
                            const percent = data.total_rows > 0 ?
                                Math.round((data.processed_rows / data.total_rows) * 100) :
                                0;

                            updateProgressBar(percent, data.inserted, data.updated);

                            if (data.status === 'done' || data.status === 'failed') {
                                clearInterval(pollInterval);
                                $('#msmeUploadModal').modal('hide');
                                alert('Upload completed! Inserted: ' + data.inserted + ', Updated: ' + data.updated);
                                location.reload(); // optional: refresh product list
                            }
                        }
                    });
                }, 2000);
            }

            // Update Bootstrap progress bar and stats
            function updateProgressBar(percent, inserted, updated) {
                const $wrapper = $('#msmeProgressWrapper');
                const $bar = $('#msmeProgressBar');
                const $stats = $('#msmeProgressStats');

                $wrapper.show();
                $stats.show();

                $bar.css('width', percent + '%');
                $bar.text(percent + '%');

                $('#msmeInserted').text(inserted);
                $('#msmeUpdated').text(updated);
            }

        });
    </script> --}}
    <script>
        $(document).ready(function() {
            const $msmeForm = $('#msmeUploadForm');
            const $fileInput = $('#msmeFileInput');
            const $dropArea = $('#msmeDropArea');
            const $preview = $('#msmePreview');
            const $error = $('#msmeError');

            let uploadUuid = null;
            let pollInterval = null;

            // Click to select file
            $dropArea.on('click', function() {
                $fileInput.click();
            });

            // Drag & Drop
            $dropArea.on('dragover', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).addClass('dragover');
            });

            $dropArea.on('dragleave', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
            });

            $dropArea.on('drop', function(e) {
                e.preventDefault();
                e.stopPropagation();
                $(this).removeClass('dragover');
                const files = e.originalEvent.dataTransfer.files;
                $fileInput[0].files = files;
                renderPreview(files);
            });

            // File input change
            $fileInput.on('change', function() {
                renderPreview(this.files);
            });

            // Render file preview
            function renderPreview(files) {
                $preview.empty();
                $error.hide();
                if (files.length === 0) return;
                Array.from(files).forEach(file => {
                    const item = $('<div>').text(file.name + ' (' + formatBytes(file.size) + ')');
                    $preview.append(item);
                });
            }

            // Format bytes nicely
            function formatBytes(bytes) {
                if (bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Submit form
            $msmeForm.on('submit', function(e) {
                e.preventDefault();
                if ($fileInput[0].files.length === 0) {
                    alert('Please select a file to upload.');
                    return;
                }

                const formData = new FormData(this);
                const $submitBtn = $(this).find('button[type="submit"]');
                $submitBtn.prop('disabled', true).text('Uploading...');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(res) {
                        uploadUuid = res.upload_uuid;
                        startPolling();
                    },
                    error: function(xhr) {
                        let msg = 'Upload failed.';
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                        }
                        $error.text(msg).show();
                        $submitBtn.prop('disabled', false).text('Upload');
                    }
                });
            });

            // Poll upload progress
            function startPolling() {
                if (!uploadUuid) return;

                pollInterval = setInterval(function() {
                    $.getJSON('/msme/upload-status/' + uploadUuid, function(res) {
                        if (res.success) {
                            const data = res.data;
                            const percent = data.total_rows > 0 ?
                                Math.round((data.processed_rows / data.total_rows) * 100) :
                                0;

                            updateProgressBar(percent, data.inserted, data.updated);

                            if (data.status === 'done' || data.status === 'failed') {
                                clearInterval(pollInterval);
                                $('#msmeUploadModal').modal('hide');
                                alert('Upload completed! Inserted: ' + data.inserted + ', Updated: ' + data.updated);
                                location.reload();
                            }
                        }
                    });
                }, 2000);
            }

            // Update Bootstrap progress bar and stats
            function updateProgressBar(percent, inserted, updated) {
                const $wrapper = $('#msmeProgressWrapper');
                const $bar = $('#msmeProgressBar');
                const $stats = $('#msmeProgressStats');

                $wrapper.show();
                $stats.show();

                $bar.css('width', percent + '%');
                $bar.text(percent + '%');

                $('#msmeInserted').text(inserted);
                $('#msmeUpdated').text(updated);
            }
        });
    </script>
@endsection

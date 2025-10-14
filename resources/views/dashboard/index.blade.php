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

    // Store selected bot type when modal opens
    let selectedBotType = null;

    // Detect which card was clicked (Web or MSME)
    $('.create-card-item').on('click', function() {
        selectedBotType = $(this).data('type'); // 1 = Web, 2 = MSME
        console.log('Selected bot type:', selectedBotType);
    });

    // Handle 'Start from scratch' click inside modal
    $('.start-from-scratch').on('click', function(e) {
        e.preventDefault();

        if (!selectedBotType) {
            alert('Please select a bot type first!');
            return;
        }

        let requestData = {
            name: selectedBotType == 1 ? 'My Web Bot' : 'My Store Bot',        // required field
            description: selectedBotType == 1 ? 'This is a new web bot' : 'This is a new store bot',
            platform: selectedBotType,
            language: 'en',
            is_active: true,
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
                    // redirect to chatbot build page
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
</script>

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
@endsection

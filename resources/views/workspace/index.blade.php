@extends('layouts.master')

@section('title')
    @lang('translation.Dashboards')
@endsection

@section('content')
    <div class="workspace-container d-flex">
        <div class="sidebar-workspace">
            <!-- Workspaces Section -->
            <div class="section-header">
                <h3>Workspaces</h3>
                <div class="item-list">
                    <i class="mdi mdi-trash-can-outline"></i> Trash
                </div>
                <button class="add-btn" onclick="convertToInput(this)">Add Workspace</button>
            </div>

            <!-- Team Members Section -->
            <div class="section-header">
                <h3>Team Members</h3>
                <div class="item-list">
                    <i class="mdi mdi-account-multiple-outline"></i> Nadeem
                </div>
                <button class="add-btn" data-bs-toggle="modal" data-bs-target="#addTeamMember">Add Team Member</button>
            </div>
        </div>

        <div class="sidebar-workspace-right">
            <div class="d-flex align-items-center justify-content-between">
                <form class="bot-search d-none d-lg-block">
                    <input type="text" class="form-control" placeholder="Search bots...">
                    <i class="mdi mdi-magnify"></i>
                </form>
                <button class="btn btn-primary" style="padding: 0px 50px;" data-bs-toggle="modal" data-bs-target="#addchatbot">Build a chatbot <i class="fa fa-arrow-right ms-1"></i></button>
            </div>

            <!-- Header -->
            <div class="workspace-header mb-5 mt-5">
                <h3><input type="checkbox" class="form-check-input" type="checkbox" value=""> ALL BOTS</h3>
                <div>
                    <select class="me-2">
                        <option>Filter by All bots</option>
                    </select>
                    <select class="me-2">
                        <option>Order by Last updated</option>
                    </select>
                </div>
            </div>

            <!-- Bot Card -->
            @foreach ($chatbots as $chatbot)
                @php
                    $encryptedId = Crypt::encryptString($chatbot->id);
                @endphp
                <div class="bot-card-container">
                    <div class="bot-info">
                        <input type="checkbox" class="form-check-input" type="checkbox" value="">
                        <a href="javascript:void(0);" class="selected-link">
                        {{-- <a href="{{ route('chatbots.build', $encryptedId) }}" class="stretched-link"> --}}
                        <div class="bot-icon">🤖</div>
                        <div class="bot-text">
                            <h4>{{ $chatbot->name }}</h4>
                            <small>Created {{ $chatbot->created_at->diffForHumans() }} / Updated {{ $chatbot->updated_at->diffForHumans() }}</small>
                        </div>
                        </a>
                        {{-- </a> --}}
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
                                    {{-- <li><a class="dropdown-item" href="#">Duplicate</a></li> --}}
                                    <li><a class="dropdown-item" href="{{ route('chatbots.build', $encryptedId) }}">Open Bot</a></li>
                                    {{-- <li><a class="dropdown-item" href="#">Rename</a></li> --}}
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

    </div>

    <!-- Static add chatbot Modal -->
    <div class="modal fade" id="addchatbot" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl start-building-modal" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <h5 class="modal-title fs-30 text-center mb-5">Start building!</h5>
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
                        <a href="javascript: void(0);">
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


   <!-- Static Add Team Member Modal -->
        <div class="modal fade" id="addTeamMember" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered modal-lg custom-modal" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <h5 class="modal-title fs-28 text-center mb-4">Add Team Member</h5>
            <div class="row align-items-center justify-content-center">
               <div class="col-lg-12 col-md-12">
                 <div class="form-group mb-4">
                    <label for="exampleInputEmail1" class="form-label fs-14">Team Name</label>
                    <input type="text" class="form-control" id="team-member-name" name="team-member-name" placeholder="Enter Name">
                 </div>
				</div>
				 <div class="col-lg-12 col-md-12">
                    <div class="form-group mb-4">
						<label for="exampleInputEmail1" class="form-label fs-14">Email</label>
						<input type="email" class="form-control" id="teammate-email" name="teammate-email" placeholder="Enter Email">
                    </div>
                </div>
				<div class="col-lg-12 col-md-12">
                    <div class="modal-footer border-0">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">CANCEL</button>
						<button type="button" class="btn btn-primary ml-16">ADD</button>
                    </div>
                 </div>
               
              </div>
             </div>
           </div>
        </div>

        <!-- end build chatbot Modal -->

@endsection
@section('script')
    <!-- dashboard init -->
    <script src="{{ URL::asset('build/js/pages/dashboard.init.js') }}"></script>
    <script>
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
    </script>

    <script>
function convertToInput(button) {
    // Create input element
    let input = document.createElement("input");
    input.type = "text";
    input.placeholder = "Enter workspace name...";
    input.className = "workspace-input";

    // Replace button with input
    button.replaceWith(input);
    input.focus();

    // Enter key → save and revert back to button
    input.addEventListener("keydown", function(e) {
        if (e.key === "Enter") {
            revertToButton(input);
        }
    });

    // Double click → cancel and revert back
    input.addEventListener("dblclick", function() {
        revertToButton(input);
    });
}

function revertToButton(input) {
    let newBtn = document.createElement("button");
    newBtn.className = "add-btn";
    newBtn.textContent = input.value || "Add Workspace";
    newBtn.setAttribute("onclick", "convertToInput(this)");
    input.replaceWith(newBtn);
}
</script>

@endsection

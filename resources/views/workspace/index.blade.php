@extends('layouts.master')

@section('title') @lang('translation.Dashboards') @endsection

@section('content')

<div class="workspace-container d-flex">
   <div class="sidebar-workspace">
        <!-- Workspaces Section -->
        <div class="section-header">
        <h3>Workspaces</h3>
        <div class="item-list">
            <i class="mdi mdi-trash-can-outline"></i> Trash
        </div>
        <button class="add-btn">Add Workspace</button>
        </div>

        <!-- Team Members Section -->
        <div class="section-header">
        <h3>Team Members</h3>
        <div class="item-list">
            <i class="mdi mdi-account-multiple-outline"></i> Nadeem
        </div>
        <button class="add-btn">Add Team Member</button>
        </div>
    </div>

   <div class="sidebar-workspace-right">
       <div class="d-flex align-items-center justify-content-between">
           <form class="bot-search d-none d-lg-block">
            <input type="text" class="form-control" placeholder="Search bots..."> 
             <i class="mdi mdi-magnify"></i>  
          </form>
           <button class="btn btn-primary" style="padding: 0px 50px;">Build a chatbot <i class="fa fa-arrow-right ms-1"></i></button>
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
            <div class="bot-card-container">
            <div class="bot-info">
                <input type="checkbox" class="form-check-input" type="checkbox" value="">
                <div class="bot-icon">🤖</div>
                <div class="bot-text">
                <h4>NEW BOT</h4>
                <small>Created 2 hours ago / Updated 2 hours ago</small>
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
                        <li><a class="dropdown-item" href="#">Open Bot</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </li>
            </ul>
            </div>
            </div>
        <!-- end Bot Card -->
        <div class="bot-card-container">
            <div class="bot-info">
                <input type="checkbox" class="form-check-input" type="checkbox" value="">
                <div class="bot-icon">🤖</div>
                <div class="bot-text">
                <h4>NEW BOT</h4>
                <small>Created 2 hours ago / Updated 2 hours ago</small>
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
                        <li><a class="dropdown-item" href="#">Open Bot</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </li>
            </ul>
            </div>
            </div>
        <!-- end Bot Card -->
        <div class="bot-card-container">
            <div class="bot-info">
                <input type="checkbox" class="form-check-input" type="checkbox" value="">
                <div class="bot-icon">🤖</div>
                <div class="bot-text">
                <h4>NEW BOT</h4>
                <small>Created 2 hours ago / Updated 2 hours ago</small>
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
                        <li><a class="dropdown-item" href="#">Open Bot</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </li>
            </ul>
            </div>
            </div>
        <!-- end Bot Card -->
        <div class="bot-card-container">
            <div class="bot-info">
                <input type="checkbox" class="form-check-input" type="checkbox" value="">
                <div class="bot-icon">🤖</div>
                <div class="bot-text">
                <h4>NEW BOT</h4>
                <small>Created 2 hours ago / Updated 2 hours ago</small>
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
                        <li><a class="dropdown-item" href="#">Open Bot</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </li>
            </ul>
            </div>
            </div>
        <!-- end Bot Card -->
        <div class="bot-card-container">
            <div class="bot-info">
                <input type="checkbox" class="form-check-input" type="checkbox" value="">
                <div class="bot-icon">🤖</div>
                <div class="bot-text">
                <h4>NEW BOT</h4>
                <small>Created 2 hours ago / Updated 2 hours ago</small>
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
                        <li><a class="dropdown-item" href="#">Open Bot</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </li>
            </ul>
            </div>
            </div>
        <!-- end Bot Card -->
        <!-- end Bot Card -->
        <div class="bot-card-container">
            <div class="bot-info">
                <input type="checkbox" class="form-check-input" type="checkbox" value="">
                <div class="bot-icon">🤖</div>
                <div class="bot-text">
                <h4>NEW BOT</h4>
                <small>Created 2 hours ago / Updated 2 hours ago</small>
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
                        <li><a class="dropdown-item" href="#">Open Bot</a></li>
                        <li><a class="dropdown-item" href="#">Rename</a></li>
                        <li><a class="dropdown-item" href="#">Delete</a></li>
                    </ul>
                </li>
            </ul>
            </div>
            </div>
        <!-- end Bot Card -->
    </div>

</div>


@endsection
@section('script')
<!-- dashboard init -->
<script src="{{ URL::asset('build/js/pages/dashboard.init.js') }}"></script>
@endsection
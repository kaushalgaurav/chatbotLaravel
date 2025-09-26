<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.Dashboards'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
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
            <?php $__currentLoopData = $chatbots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $chatbot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $encryptedId = Crypt::encryptString($chatbot->id);
                ?>
                <div class="bot-card-container">
                    <div class="bot-info">
                        <input type="checkbox" class="form-check-input" type="checkbox" value="">
                        
                        <div class="bot-icon">🤖</div>
                        <div class="bot-text">
                            <h4><?php echo e($chatbot->name); ?></h4>
                            <small>Created <?php echo e($chatbot->created_at->diffForHumans()); ?> / Updated <?php echo e($chatbot->updated_at->diffForHumans()); ?></small>
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
                                    
                                    <li><a class="dropdown-item" href="<?php echo e(route('chatbots.build', $encryptedId)); ?>">Open Bot</a></li>
                                    
                                    <li>
                                        <form method="POST" action="<?php echo e(route('chatbots.destroy', $encryptedId)); ?>" style="display:inline-block;">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-danger dropdown-item" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

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
                                <div class="start-building-icon"><img src="<?php echo e(URL::asset('build/images/icons/scratch.png')); ?>" alt="scratch"></div>
                                <h3 class="my-2 fs-20">Build it for me!</h3>
                                <p class="mb-0">Tell what you need and we will create it automatically</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="javascript: void(0);" class="start-from-scratch">
                            <div class="start-building-card ">
                                <div class="start-building-icon"><img src="<?php echo e(URL::asset('build/images/icons/scratch.png')); ?>" alt="scratch"></div>
                                <h3 class="my-2 fs-20">Start from scratch</h3>
                                <p class="mb-0">Start with a blank builder and let your imagination flow!</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <a href="javascript: void(0);">
                            <div class="start-building-card ">
                                <div class="start-building-icon"><img src="<?php echo e(URL::asset('build/images/icons/template.png')); ?>" alt="template"></div>
                                <h3 class="my-2 fs-20">Use a template</h3>
                                <p class="mb-0">Choose a pre-made bot and edit them as you want</p>
                            </div>
                        </a>
                    </div>

                </div>



            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
    <!-- dashboard init -->
    <script src="<?php echo e(URL::asset('build/js/pages/dashboard.init.js')); ?>"></script>
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
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/chatbotLaravel/resources/views/workspace/index.blade.php ENDPATH**/ ?>
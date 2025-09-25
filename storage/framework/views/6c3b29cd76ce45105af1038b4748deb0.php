

<?php $__env->startSection('title'); ?> <?php echo app('translator')->get('translation.Dashboards'); ?> <?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<style>
    .page-content {
  background: #fff;
}
</style>
<div class="chatbot-builder">

<div class="d-flex align-items-center justify-content-between mb-4 mt-4">
 <div class="welcome-text">
    <h4 class="mb-0 fs-22">Welcome to Chatbot Builder!</h4>
    <p class="text-muted m-0 p-0">Your free trial expires in <span class="fw-semibold">14 days</span></p>    
</div>
 <button class="btn btn-primary">Upgrade <i class="fa fa-arrow-right ms-1"></i></button>
</div>

<div class="create-card mb-5">
    <div class="create-card-header">
        <h5 class="mb-3 fs-22">Create a bot for</h5>
    </div>
    <div class="create-card-body d-flex gap-3">
        <div class="create-card-item" data-bs-toggle="modal" data-bs-target="#addchatbot">
            <div class="create-card-icon">
                <img src="<?php echo e(URL::asset('build/images/icons/responsive.png')); ?>" alt="">
            </div>
            <h6 class="mb-0 fs-16">Web</h6>
        </div>
        <div class="create-card-item ">
            <div class="create-card-icon btn-warning">
                <img src="<?php echo e(URL::asset('build/images/icons/ApiChatbot.png')); ?>" alt="">
            </div>
            <h6 class="mb-0 fs-16">ApiChatbot</h6>
        </div>
    </div>
</div>  


<div class="last-updated-chatbots mb-5">
  <h5 class="mb-3 fs-22">Last updated chatbots</h5>
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
                             <a href="javascript: void(0);">
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
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /var/www/html/chatbotLaravel/resources/views/dashboard/index.blade.php ENDPATH**/ ?>
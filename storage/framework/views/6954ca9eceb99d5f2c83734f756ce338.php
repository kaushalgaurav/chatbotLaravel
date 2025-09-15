

<?php $__env->startSection('title'); ?>
    <?php echo app('translator')->get('translation.chatbot'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <?php $__env->startComponent('components.breadcrumb'); ?>
        <?php $__env->slot('li_1'); ?>
            Backend
        <?php $__env->endSlot(); ?>
        <?php $__env->slot('title'); ?>
            Chatbot
        <?php $__env->endSlot(); ?>
    <?php echo $__env->renderComponent(); ?>

<style>
    .text-right {
    text-align: right !important;
}
</style>

    <div id="app">
        
        <div class="row">
     <div class="col-xl-12">
        <div class="card">
        <div class="card-body">
            <div class="card-header d-flex align-items-center justify-content-between p-0 bg-transparent mb-3">
              <h4 class="card-title">Chatbot</h4>
            </div>
           <form>
            <div class="row g-3 align-items-center mb-4">
                <div class="col-lg-2">
                    <label for="" class="col-form-label">Name</label>
                </div>
                <div class="col-lg-6">
                    <input type="text" id="inpurName" class="form-control" placeholder="Name">
                </div>
             </div>
              <div class="row g-3 align-items-center mb-4">
                <div class="col-lg-2">
                    <label for="" class="col-form-label">Purpose</label>
                </div>
                <div class="col-lg-6">
                    <input type="text" id="Purpose" class="form-control" placeholder="Purpose">
                </div>
             </div>
             <div class="row g-3 align-items-center mb-4">
                <div class="col-lg-2">
                    <label for="" class="col-form-label">Name</label>
                </div>
                <div class="col-lg-6">
                    <input type="text" id="inpurName" class="form-control" placeholder="Name">
                </div>
             </div> 
             <div class="row g-3 align-items-center">
                <div class="col-lg-8">
                    <div class="text-right">
                        <a href="javascript: void(0);" class="btn btn-secondary">Reset<span class="mdi mdi-alert-circle-outline ms-1"></span></a>
                        <a href="javascript: void(0);" class="btn btn-primary ms-3">Submit<span class="mdi mdi-rocket-launch-outline ms-1"></span></a>
                    </div>
                </div>
             </div>
           </form>
        </div>
      </div>
    </div>
  </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views/chatbots/build_chatbot.blade.php ENDPATH**/ ?>
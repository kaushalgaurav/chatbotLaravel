

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
            <form method="post" action="<?php echo e(route('chatbots.update', Crypt::encryptString($chatbot->id))); ?>">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Name</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" id="inpurName" class="form-control" placeholder="Name" name="chatbot_name" value="<?php echo e($chatbot->name); ?>" readonly>
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Purpose</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" id="Purpose" class="form-control" placeholder="Purpose" name="purpose">
                    </div>
                </div>
                <div class="row g-3 align-items-center mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Chatbot Type</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="text" id="ChatbotType" class="form-control" placeholder="Chatbot Type" value="<?php echo e($chatbot->platform); ?>" disabled>
                        
                    </div>
                </div> 
                <div class="row g-3  mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Description</label>
                    </div>
                    <div class="col-lg-6">
                        <textarea class="form-control" rows="5" placeholder="Description" name="description"></textarea>
                    </div>
                </div>
                <div class="row g-3  mb-4">
                    <div class="col-lg-2">
                        <label for="" class="col-form-label">Upload File</label>
                    </div>
                    <div class="col-lg-6">
                        <input type="file" class="form-control" name="upload_file">
                    </div>
                </div>  
                
                <div class="row g-3 align-items-center">
                    <div class="col-lg-8">
                        <div class="text-right">
                            <a href="javascript: void(0);" class="btn btn-secondary">Reset<span class="mdi mdi-alert-circle-outline ms-1"></span></a>
                            <button type="submit" class="btn btn-primary ms-3">Submit<span class="mdi mdi-rocket-launch-outline ms-1"></span></button>
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

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views/chatbots/details.blade.php ENDPATH**/ ?>
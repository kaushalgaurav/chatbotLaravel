

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

    <div id="app">
        <build-chatbot :chatbot='<?php echo json_encode($chatbot, 15, 512) ?>'></build-chatbot>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views\chatbots\build_chatbot.blade.php ENDPATH**/ ?>
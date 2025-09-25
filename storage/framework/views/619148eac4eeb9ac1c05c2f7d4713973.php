

<?php echo $__env->yieldContent('css'); ?>


<?php echo app('Illuminate\Foundation\Vite')->reactRefresh(); ?>
<?php echo app('Illuminate\Foundation\Vite')([
  'resources/js/app.jsx',
  'resources/scss/bootstrap.scss',
  'resources/scss/icons.scss',
  'resources/scss/app.scss',
  'resources/scss/custom.scss',
]); ?>
<?php /**PATH C:\xampp\htdocs\chatbotLaravel\resources\views/layouts/head-css.blade.php ENDPATH**/ ?>
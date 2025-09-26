<!doctype html>
<html lang="en">

<head>
<<<<<<< HEAD
  <meta charset="utf-8" />
  <title>Dashboards | Admin & Dashboard Template</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Premium Multipurpose Admin & Dashboard Template" />
  <meta name="author" content="Themesbrand" />
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
=======
    <meta charset="utf-8" />
    <title>Dashboards | Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="Premium Multipurpose Admin & Dashboard Template" />
    <meta name="author" content="Themesbrand" />
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <?php echo app('Illuminate\Foundation\Vite')->reactRefresh(); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/landbot/main.jsx'); ?>
>>>>>>> a5f58729dc637b09e293bc5d99d11124362c1024

    <!-- you can keep any other meta tags here -->
</head>
<<<<<<< HEAD
<body>
  <div id="root" data-chatbot-id="<?php echo e($chatbot->id); ?>" data-user-id="<?php echo e(Auth::user()->id); ?>"></div>
=======
>>>>>>> a5f58729dc637b09e293bc5d99d11124362c1024

<body>
    <div id="root" data-chatbot-id="<?php echo e($chatbot->id); ?>" data-user-id="<?php echo e(Auth::user()->id); ?>"></div>

    
    <?php echo $__env->make('layouts.vendor-scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html>
<?php /**PATH /var/www/html/chatbotLaravel/resources/views/chatbots/build_chatbot.blade.php ENDPATH**/ ?>
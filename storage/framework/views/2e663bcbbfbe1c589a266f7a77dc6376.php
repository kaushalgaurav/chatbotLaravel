<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chatbot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php echo app('Illuminate\Foundation\Vite')->reactRefresh(); ?>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/js/landbot/main.jsx']); ?>   
  
  <?php echo app('Illuminate\Foundation\Vite')(['resources/scss/bootstrap.scss','resources/scss/icons.scss','resources/scss/app.scss']); ?>
</head>
<body>
  <div id="root"></div>
</body>
</html>
<?php /**PATH /var/www/html/chatbotLaravel/resources/views/chatbots/build_chatbot.blade.php ENDPATH**/ ?>
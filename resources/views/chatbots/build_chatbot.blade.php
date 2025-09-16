<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Chatbot</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @viteReactRefresh
  @vite(['resources/js/landbot/main.jsx'])   {{-- this actually injects your JS --}}
  {{-- (Optional) Load your SCSS bundle if needed on this page: --}}
  @vite(['resources/scss/bootstrap.scss','resources/scss/icons.scss','resources/scss/app.scss'])
</head>
<body>
  <div id="root"></div>
</body>
</html>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Dashboards | Admin & Dashboard Template</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="Premium Multipurpose Admin & Dashboard Template" />
  <meta name="author" content="Themesbrand" />

  {{-- Vite: inject correct built assets (CSS + JS) --}}
  @viteReactRefresh
  @vite('resources/js/landbot/main.jsx')

  <!-- you can keep any other meta tags here -->
</head>
<body>
  <div id="root"></div>

  {{-- Any other scripts you need can go here --}}
  @include('layouts.vendor-scripts')
</body>
</html>
